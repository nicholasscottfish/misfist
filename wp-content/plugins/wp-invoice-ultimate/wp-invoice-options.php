<?php
class WPIU_Options extends WPIU{
	
	
	function option($option_name){
		
		$options = get_option(WPIU_OPTION);
		
		return $option[$option_name];
		
	}//function
	
	/**
	 * setup some class vars - one for page hook -help guides / etc | one for option name
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * 
	 *
	 */
	
	public $page_hook;
	public $option_name = WPIU_OPTION;
	
	/**
	 * class constructor
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * adds options pages, registes settings, etc
	 */
	function __construct(){
		
		if ( is_admin() ){
			add_action('admin_menu', array(&$this, '_add_page'));
		}//if
		
		add_action('admin_init', array(&$this, '_register_settings') );
		
		add_action( 'admin_notices', array(&$this,'_admin_notices'));
		
	}//function
	
	
	
	/**
	 * internal function for displaying input fileds - reduces markup
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * removed support for wysiwyg as wp 3.3 contains wp_editor() function
	 *
	 */
	function _field($type, $options_name, $option, $id = '', $length = '', $value = '', $placeholder = ''){
		
		$options_array = get_option($options_name);
		
		switch($type){
			
			case 'checkbox':
			
				echo "<input ".checked( isset($options_array[$option]), 1 , false)." id='".$id."' name='".$options_name."[".$option."]' type='checkbox' value ='1'/>";
				
			break;
			case 'text':
			
				echo "<input id='".$id."' name='".$options_name."[".$option."]"."' size='".$length."' type='text' value='";
				echo isset($options_array[$option]) ? $options_array[$option] : '';
				echo "' placeholder='{$placeholder}'/>";

			break;
			case 'password':
			
				echo "<input id='".$id."' name='".$options_name."[".$option."]"."' size='".$length."' type='password' value='";
				echo isset($options_array[$option]) ? $options_array[$option] : '';
				echo "' />"; 
				
			break;
			case 'textarea':
			
				echo "<textarea id='".$id."' name='".$options_name."[".$option."]"."' rows='4' cols='60' type='textarea'>";
				echo isset($options_array[$option]) ? $options_array[$option] : '';
				echo "</textarea>";
				
			break;
			case 'select':
			
				echo "<select id='".$id."' name='".$options_name."[".$option."]"."'>";
				
					foreach($value as $item) {
						echo "<option value='$item' ".selected($options_array[$option], $item, false).">$item</option>";
					}//foreach
					
				echo "</select>";
				
			break;
			case 'file':
			
				echo '<input type="file" name="'.$option.'" size="'.$length.'"/>';
				
			break;
	
		}//switch
		
	}//function
	
	
	
	/**
	 * gets the site url and removes the prepended part of the domain so we can use the domain for email address
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * simply removes the http:// | https:// | www. from the domian (if its there)
	 *
	 */
	function get_part_url(){

		//$email = preg_replace("`(http://|https://)?(www.)?(.*?)`is","$3", site_url());
		
		//return $email;
		
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		return $sitename;
		
		
	}

	
	
	/**
	 * add options page to post type
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * 
	 *
	 */
	function _add_page() {
				$this->page_hook = add_submenu_page('edit.php?post_type=wpiu-invoices',__( 'WP Invoices Ultimate Options',WPIU_LANG),__( 'Invoice Options',WPIU_LANG),'administrator','wp_iu', array(&$this,'_page_html'));
		
		
	}//function
	
	
	
	/**
	 * register the settings sections for use on the options page
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 *
	 */
	function _register_settings(){
				register_setting( $this->option_name.'_group', $this->option_name, array(&$this,'_validate_options'));		add_settings_section($this->option_name.'_section', __('General Settings',WPIU_LANG), array(&$this,'invoice_general_intro'), __FILE__);		

			add_settings_field('invoice_name_options', __('Business Name',WPIU_LANG), array(&$this,'invoice_name_options'), __FILE__, $this->option_name.'_section');
			
					
		add_settings_section($this->option_name.'_section1', __('Invoice Notifications',WPIU_LANG), array(&$this,'invoice_notify_intro'), __FILE__);
		
		
			add_settings_field('invoice_notify_name_options', __('Notification From Name',WPIU_LANG), array(&$this,'invoice_notify_name_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('invoice_notify_email_options', __('Notification From Email',WPIU_LANG), array(&$this,'invoice_notify_email_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('invoice_notify_send_subject', __('Send Invoice Subject',WPIU_LANG), array(&$this,'invoice_notify_send_subject'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('invoice_notify_send_reminder_subject', __('Send Invoice Reminder Subject',WPIU_LANG), array(&$this,'invoice_notify_send_reminder_subject'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('invoice_notify_paid', __('Invoice Payment Subject',WPIU_LANG), array(&$this,'invoice_notify_paid'), __FILE__, $this->option_name.'_section1');
			
			
		add_settings_section($this->option_name.'_section2', __('Payment Settings',WPIU_LANG), array(&$this,'invoice_payment_intro'), __FILE__);
		
			add_settings_field('invoice_payment_email', __('Paypal Email Address',WPIU_LANG), array(&$this,'invoice_payment_email'), __FILE__, $this->option_name.'_section2');
			
			add_settings_field('invoice_payment_currency', __('Currency Format',WPIU_LANG), array(&$this,'invoice_payment_currency'), __FILE__, $this->option_name.'_section2');
			
			add_settings_field('invoice_payment_tax', __('Global Tax',WPIU_LANG), array(&$this,'invoice_payment_tax'), __FILE__, $this->option_name.'_section2');
			
			add_settings_field('invoice_payment_details', __('Payment Details',WPIU_LANG), array(&$this,'invoice_payment_details'), __FILE__, $this->option_name.'_section2');
			
			add_settings_field('invoice_payment_deadlines', __('Payment Deadlines',WPIU_LANG), array(&$this,'invoice_payment_deadlines'), __FILE__, $this->option_name.'_section2');
			}//function
	
	




	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_general_intro(){
		
		echo __('<p>This is the general settings section for your invoices.</p>',WPIU_LANG);
		
	}//function



	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_name_options(){
		
		$this->_field('text', $this->option_name, 'invoice_name', $this->option_name, '30', '', __('Business Name',WPIU_LANG));
		
		echo '<span class="description">'.__('This is the name that appears in the header of you invoice template', WPIU_LANG).'</span>';

	}//function
	
	
	



	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_notify_intro(){
		
		echo __('<p>Here you can choose to add custom "From" name and email address, this is what is displayed as the sender in the invoice notification emails:</p>',WPIU_LANG);
		
	}//function
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_notify_name_options(){
		
		$this->_field('text', $this->option_name, 'invoice_notify_name', $this->option_name, '30', '', __('Your Name',WPIU_LANG));

	}//function
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_notify_email_options(){
		
		$this->_field('text', $this->option_name, 'invoice_notify_email', $this->option_name, '30', '', __('invoices',WPIU_LANG));
		
		echo '<span class="description">@'.$this->get_part_url().'</span>';

	}//function
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_notify_send_subject(){
		
		$this->_field('text', $this->option_name, 'invoice_notify_send_subject', $this->option_name, '67', '', __('Invoice %%title%% - #%%number%% has been created!',WPIU_LANG));
		
		echo '<span class="description">'.__('You can use tags her for the Invoice Title <strong>%%title%%</strong> and Invoice Number <strong>%%number%%</strong>', WPIU_LANG).'</span>';

	}//function
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_notify_send_reminder_subject(){
		
		$this->_field('text', $this->option_name, 'invoice_notify_send_reminder_subject', $this->option_name, '67', '', __('Invoice %%title%% - #%%number%% needs to be paid!',WPIU_LANG));
		
		echo '<span class="description">'.__('You can use tags her for the Invoice Title <strong>%%title%%</strong> and Invoice Number <strong>%%number%%</strong>', WPIU_LANG).'</span>';

	}//function
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_notify_paid(){
		
		$this->_field('text', $this->option_name, 'invoice_notify_paid', $this->option_name, '67', '', __('Invoice %%title%% - #%%number%% has a new payment!',WPIU_LANG));
		
		echo '<span class="description">'.__('You can use tags her for the Invoice Title <strong>%%title%%</strong> and Invoice Number <strong>%%number%%</strong>', WPIU_LANG).'</span>';

	}//function
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_payment_intro(){
		
		echo __('<p>The invoice payments can be automated using paypal. Simply enter your paypal email address below.<br/>You can still accept manual payments and update the payment amount for each invoice with or without the paypal intergration.</p>',WPIU_LANG);
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_payment_email(){
		
		$this->_field('text', $this->option_name, 'invoice_payment_email', $this->option_name, '30', '', __('paypal@paypal.com',WPIU_LANG));

	}//function
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_payment_currency(){

		$this->_field('select', $this->option_name, 'invoice_payment_currency', $this->option_name, '', array("GBP", "USD", "AUD", "BRL", "CAD", "CZK", "DKK", "EUR", "HKD", "HUF", "ILS", "JPY", "MYR", "MXN", "NOK", "NZD", "PHP", "PLN", "SGD", "SEK", "CHF", "TWD", "THB", "TRY"));
				echo '<span class="description">'.__('Simply choose the currency you wish to use for your invoices. For more info on your countries currency code click <a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes" target="_blank">here</a>', WPIU_LANG).'</span>';

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_payment_tax(){
		
		$this->_field('text', $this->option_name, 'invoice_payment_tax', $this->option_name, '2', '', __('05',WPIU_LANG));
				echo '% <span class="description">'.__('Enter the tax that will be applied to the total of you invoices', WPIU_LANG).'</span>';

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_payment_details(){
		
		//$this->_field('text', $this->option_name, 'invoice_payment_currency', $this->option_name, '2', '', __('£',WPIU_LANG));
			//	echo '<span class="description">'.__('Simply enter the currency you wish to use for your invoices', WPIU_LANG).'</span>';
				
				
				
				$options = get_option($this->option_name);
		
		wp_editor( $options['invoice_payment_details'], 'invoice_payment_details', array('textarea_name' => $this->option_name.'[invoice_payment_details]', 'textarea_rows' => 4) );
		echo '<span class="description">'.__('Here you can enter payment details for manual payments. IE, bank details and a how to reference the invoice in the payment.', WPIU_LANG).'</span>';
		echo '<br/><br/>';

	}//function	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function invoice_payment_deadlines(){
		
		//$this->_field('text', $this->option_name, 'invoice_payment_deadlines', $this->option_name, '67', '', __('IMPORTANT! Invoices should be paid by the due date. Late invoice payments will be subject to a 5% fee.',WPIU_LANG));
			//echo '<span class="description">'.__('Enter a little information about punishment for late payments.', WPIU_LANG).'</span>';
			
			$options = get_option($this->option_name);
		
		wp_editor( $options['invoice_payment_deadlines'], 'invoice_payment_deadlines', array('textarea_name' => $this->option_name.'[invoice_payment_deadlines]', 'textarea_rows' => 4) );
		echo '<span class="description">'.__('Enter a little information about punishment for late payments.', WPIU_LANG).'</span>';
		echo '<br/><br/>';


	}//function	

	
	
	
	/**
	 * the page html
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _page_html(){$options = get_option($this->option_name);?>
		<div class="wrap">
			<img src="<?php echo WPIU_IMG .'32/icon.png';?>" style="margin:14px 6px 0px 0px;float:left;"/>
			<h2 style="display:inline-block;"><?php echo get_admin_page_title(); ?></h2>
				<form method="post" action="options.php" enctype="multipart/form-data">					<?php settings_fields($this->option_name.'_group');
							$options = get_option($this->option_name);
							do_settings_sections(__FILE__); ?>
					<p><br/><br/><input type="submit" name="<?php echo $this->option_name;?>[save]" value="Save Changes" class="button-primary" /></p>				</form>				<!-- <?php $options = get_option($this->option_name); print_r($options);?> -->
				<div class="clear"></div>
		</div>	<?php 
	}//function

	
	
	/**
	 * vaildate options callback
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * adds settings updated error to page as well
	 *
	 */
	function _validate_options($plugin_options){
		//validate the file upload
		$keys = array_keys($_FILES);$i = 0;
		foreach ( $_FILES as $image ) {// if a files was upload
		if ($image['size']) {// if it is an image
		if ( preg_match('/(jpg|jpeg|png|gif|ico)$/', $image['type']) ) {$override = array('test_form' => false);// save the file, and store an array, containing its location in $file
		$file = wp_handle_upload( $image, $override );$plugin_options[$keys[$i]] = $file['url'];} else {// Not an image. 
		$options = get_option($this->option_name);$plugin_options[$keys[$i]] = $options[$logo];// Die and let the user know that they made a mistake.
		wp_die('No image was uploaded. Please try again');}}// Else, the user didn't upload a file.// Retain the image that's already on file.
		else {$options = get_option($this->option_name);$plugin_options[$keys[$i]] = $options[$keys[$i]];}$i++;}


		if(!is_numeric($plugin_options['invoice_payment_tax'])){
			unset($plugin_options['invoice_payment_tax']);
			add_settings_error(__FILE__, 'invoice_tax', __('The invoice tax amount must be a numerical value!',WPIU_LANG), 'error' );
		}
		if(isset($plugin_options['invoice_payment_tax']) && is_numeric($plugin_options['invoice_payment_tax']) && $plugin_options['invoice_payment_tax'] < 10){
			$plugin_options['invoice_payment_tax'] = '0'.$plugin_options['invoice_payment_tax'];
		}


		add_settings_error(__FILE__, 'settings_updated', __('Settings Updated',WPIU_LANG), 'updated' );		return $plugin_options;	}//function
	
	
	
	/**
	 * hooks the admin notices to the options page
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * needed so the update message can be used
	 *
	 */
	function _admin_notices() {
		
    	settings_errors(__FILE__);
    	
    }//function
    
    
    
	/**
	 * get currency symbol from currency option in db
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * used in the invoice template to highlight the currency in use
	 *
	 */
    function get_currency(){
    	$options = get_option(WPIU_OPTION);
    	
    	
    	$currency = array(
    	"GBP" => "&#163;", 
    	"USD" => "&#36;", 
    	"AUD" => "A&#36;", 
    	"BRL" => "R&#36;", 
    	"CAD" => "C&#36;", 
    	"CZK" => "CZK&#164;", 
    	"DKK" => "DKK&#164;", 
    	"EUR" => "&#8364;", 
    	"HKD" => "HKD&#164;", 
    	"HUF" => "HUF&#164;", 
    	"ILS" => "ILS&#164;", 
    	"JPY" => "&#165", 
    	"MYR" => "MYR&#164;", 
    	"MXN" => "MXN&#164;", 
    	"NOK" => "NOK&#164;", 
    	"NZD" => "N&#36;", 
    	"PHP" => "PHP&#164;", 
    	"PLN" => "PLN&#164;", 
    	"SGD" => "SGD&#164;", 
    	"SEK" => "SEK&#164;", 
    	"CHF" => "CHF&#164;", 
    	"TWD" => "TWD&#164;", 
    	"THB" => "THB&#164;", 
    	"TRY" => "TRY&#164;"
    	);
    	return $currency[$options['invoice_payment_currency']];
    	
    	
    }//function
	
	
	
		
}//class
?>