<?php

define('LIKEBTN_LIKE_BUTTON_LAST_SUCCESSFULL_SYNC_TIME_OFFSET', 57600);
define('LIKEBTN_LIKE_BUTTON_API_URL', 'http://api.likebtn.com/api/');
define('LIKEBTN_LIKE_BUTTON_LOCALES_SYNC_INTERVAL', 57600);
define('LIKEBTN_LIKE_BUTTON_STYLES_SYNC_INTERVAL', 57600);

class LikeBtnLikeButton {

    protected static $synchronized = false;

    /**
     * Constructor.
     */
    public function __construct() {
        // Do nothing.
    }

    /**
     * Running votes synchronization.
     */
    public function runSyncVotes() {
        if (!self::$synchronized && get_option('likebtn_like_button_account_email') && get_option('likebtn_like_button_account_api_key') && get_option('likebtn_like_button_sync_inerval') && $this->timeToSyncVotes(get_option('likebtn_like_button_sync_inerval') * 60)) {
            $this->syncVotes(get_option('likebtn_like_button_account_email'), get_option('likebtn_like_button_account_api_key'));
        }
    }

    /**
     * Check if it is time to sync votes.
     */
    public function timeToSyncVotes($sync_period) {

        $last_sync_time = get_option('likebtn_like_button_last_sync_time');

        //$now = time();
        //update_option('likebtn_like_button_last_sync_time', $now);
        //return true;

        $now = time();
        if (!$last_sync_time) {
            update_option('likebtn_like_button_last_sync_time', $now);
            self::$synchronized = true;
            return true;
        } else {

            if ($last_sync_time + $sync_period > $now) {
                return false;
            } else {
                update_option('likebtn_like_button_last_sync_time', $now);
                self::$synchronized = true;
                return true;
            }
        }
    }

    /**
     * Retrieve data.
     */
    public function curl($url) {

        global $wp_version;

        $cms_version = $wp_version;

        $likebtn_version = _likebtn_like_button_get_plugin_version;
        $php_version = phpversion();
        $useragent = "WordPress $wp_version; likebtn plugin $likebtn_version; PHP $php_version";

        try {
            $http = new WP_Http();
            $response = $http->request($url, array('headers' => array("User-Agent" => $useragent)));
        } catch (Exception $e) {
            return '';
        }

        if (is_array($response) && !empty($response['body'])) {
            return $response['body'];
        } else {
            return '';
        }
    }

    /**
     * Comment sync function.
     */
    public function syncVotes() {
        $sync_result = true;

        $last_sync_time = number_format(get_option('likebtn_like_button_last_sync_time'), 0, '', '');

        $email = trim(get_option('likebtn_like_button_account_email'));
        $api_key = trim(get_option('likebtn_like_button_account_api_key'));
        $parse_url = parse_url(get_site_url());
        $domain = $parse_url['host'];

        $updated_after = '';

        if (get_option('likebtn_like_button_last_successfull_sync_time')) {
            $updated_after = get_option('likebtn_like_button_last_successfull_sync_time') - LIKEBTN_LIKE_BUTTON_LAST_SUCCESSFULL_SYNC_TIME_OFFSET;
        }

        $url = LIKEBTN_LIKE_BUTTON_API_URL . "?action=stat&email={$email}&api_key={$api_key}&domain={$domain}&output=json&last_sync_time=" . $last_sync_time;

        if ($updated_after) {
            $url .= '&updated_after=' . $updated_after;
        }

        // retrieve first page
        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        if (!$this->updateVotes($response)) {
            $sync_result = false;
        }

        // retrieve all pages after the first
        if (isset($response['response']['total']) && isset($response['response']['page_size'])) {
            $total_pages = ceil((int) $response['response']['total'] / (int) $response['response']['page_size']);

            for ($page = 2; $page <= $total_pages; $page++) {
                $response_string = $this->curl($url . '&page=' . $page);
                $response = $this->jsonDecode($response_string);

                if (!$this->updateVotes($response)) {
                    $sync_result = false;
                }
            }
        }

        if ($sync_result) {
            update_option('likebtn_like_button_last_successfull_sync_time', $last_sync_time);
        }
    }

    /**
     * Test synchronization.
     *
     * @param type $account_api_key
     * @param type $site_api_key
     */
    public function testSync($email, $api_key) {
        $last_sync_time = number_format(get_option('likebtn_like_button_last_sync_time'), 0, '', '');

        $email = trim($email);
        $api_key = trim($api_key);

        $parse_url = parse_url(get_site_url());
        $domain = $parse_url['host'];

        $url = LIKEBTN_LIKE_BUTTON_API_URL . "?action=stat&email={$email}&api_key={$api_key}&domain={$domain}&output=json&page_size=1&nocache=.php";

        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        return $response;
    }

    /**
     * Decode JSON.
     */
    public function jsonDecode($jsong_string) {
        return json_decode($jsong_string, true);
    }

    /**
     * Update votes in database from API response.
     */
    public function updateVotes($response) {
        global $likebtn_like_button_entities;
        global $likebtn_like_button_custom_fields;

        $entity_updated = false;

        if (!empty($response['response']['items'])) {
            foreach ($response['response']['items'] as $item) {
                $identifier_parts = explode('_', $item['identifier']);
                $entity_name = '';
                if (!empty($identifier_parts[0])) {
                    $entity_name = $identifier_parts[0];
                }
                // check if entity is supported
                if (!array_key_exists($entity_name, $likebtn_like_button_entities)) {
                    continue;
                }
                $entity_id = '';
                if (!empty($identifier_parts[1])) {
                    $entity_id = $identifier_parts[1];
                    if (!is_numeric($entity_id)) {
                        continue;
                    }
                }

                $likes = 0;
                if (!empty($item['likes'])) {
                    $likes = $item['likes'];
                }
                $dislikes = 0;
                if (!empty($item['dislikes'])) {
                    $dislikes = $item['dislikes'];
                }

                $likes_minus_dislikes = $likes - $dislikes;

                $entity_updated = true;

                // set Custom fields
                if ($entity_name == 'comment') {
                    // entity is comment
                    $comment = get_comment($entity_id);

                    // check if post exists and is not revision
                    if (!empty($comment) && $comment->comment_type != 'revision') {
                        if (count(get_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES)) > 1) {
                            delete_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES);
                            add_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES, $likes, true);
                        } else {
                            update_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES, $likes);
                        }
                        if (count(get_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES)) > 1) {
                            delete_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES);
                            add_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES, $dislikes, true);
                        } else {
                            update_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES, $dislikes);
                        }
                        if (count(get_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES)) > 1) {
                            delete_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES);
                            add_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes, true);
                        } else {
                            update_comment_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes);
                        }
                    } else {
                        $entity_updated = false;
                    }
                } else {
                    // entity is post
                    $post = get_post($entity_id);

                    // check if post exists and is not revision
                    if (!empty($post) && !empty($post->post_type) && $post->post_type != 'revision') {
                        if (count(get_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES)) > 1) {
                            delete_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES);
                            add_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES, $likes, true);
                        } else {
                            update_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES, $likes);
                        }
                        if (count(get_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES)) > 1) {
                            delete_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES);
                            add_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES, $dislikes, true);
                        } else {
                            update_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_DISLIKES, $dislikes);
                        }
                        if (count(get_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES)) > 1) {
                            delete_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES);
                            add_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes, true);
                        } else {
                            update_post_meta($entity_id, LIKEBTN_LIKE_BUTTON_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes);
                        }
                    } else {
                        $entity_updated = false;
                    }
                }
            }
        }
        return $entity_updated;
    }

    /**
     * Run locales synchronization.
     */
    public function runSyncLocales() {
        if ($this->timeToSync(LIKEBTN_LIKE_BUTTON_LOCALES_SYNC_INTERVAL, 'likebtn_like_button_last_locale_sync_time')) {
            $this->syncLocales();
        }
    }

    /**
     * Run styles synchronization.
     */
    public function runSyncStyles() {
        if ($this->timeToSync(LIKEBTN_LIKE_BUTTON_STYLES_SYNC_INTERVAL, 'likebtn_like_button_last_style_sync_time')) {
            $this->syncStyles();
        }
    }

    /**
     * Check if it is time to sync.
     */
    public function timeToSync($sync_period, $sync_variable) {

        $last_sync_time = get_option($sync_variable);

        $now = time();
        if (!$last_sync_time) {
            update_option($sync_variable, $now);
            return true;
        } else {
            if ($last_sync_time + $sync_period > $now) {
                return false;
            } else {
                update_option($sync_variable, $now);
                return true;
            }
        }
    }

    /**
     * Locales sync function.
     */
    public function syncLocales() {
        $url = LIKEBTN_LIKE_BUTTON_API_URL . "?action=locale";

        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        if (isset($response['result']) && $response['result'] == 'success' && isset($response['response']) && count($response['response'])) {
            update_option('likebtn_like_button_locales', $response['response']);
        }
    }

    /**
     * Styles sync function.
     */
    public function syncStyles() {
        $url = LIKEBTN_LIKE_BUTTON_API_URL . "?action=style";

        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        if (isset($response['result']) && $response['result'] == 'success' && isset($response['response']) && count($response['response'])) {
            update_option('likebtn_like_button_styles', $response['response']);
        }
    }

    /**
     * Reset likes/dislikes using API
     *
     * @param type $account_api_key
     * @param type $site_api_key
     */
    public function reset($identifier) {
        $result = false;

        $email = trim(get_option('likebtn_like_button_account_email'));
        $api_key = trim(get_option('likebtn_like_button_account_api_key'));
        $parse_url = parse_url(get_site_url());
        $domain = $parse_url['host'];

        $url = LIKEBTN_LIKE_BUTTON_API_URL . "?action=reset&email={$email}&api_key={$api_key}&domain={$domain}&identifier_filter={$identifier}&nocache=.php";

        // retrieve first page
        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        // check result
        if (isset($response['response']['reseted']) && $response['response']['reseted']) {
           $result = $response['response']['reseted'];
        }

        return $result;
    }

}
