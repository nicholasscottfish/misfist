<?php
class WPIU_Client extends WPIU{
	
	
	
	
	/**
	 * constructor class for function
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function __construct(){
		add_filter('user_contactmethods', array(&$this, '_add_profile_fields'));
		
		add_action('admin_head', array(&$this, '_divide_profile_table'));
	}//function
	
	
	
	/**
	 * add additional profile fields for all users
	 *
	 * @package WPIU
	 * @since 3.3
	 * add meta info to each user for use in the invoicing system
	 * - has little java hack below with some weird marging styling with profile fields as no current option to add new sections to the profile field
	 * - could of added extra forms + fields etc, but trying to use the wp preffered way of adding meta. this could nbe extended to allow the use required.
	 *
	 */
	function _add_profile_fields($fields){
		//add new
		$fields['company_data'] = __('<strong style="font-size:1.17em;margin-left:-10px;">Invoice System Data</strong><br/><br/>', NHP_IN_PLUGIN);
		$fields['company_name'] = __('Company Name <span class="description">(required)</span>', NHP_IN_PLUGIN);
		$fields['company_phone'] = __('Phone <span class="description">(required)</span>', NHP_IN_PLUGIN);
		$fields['company_street'] = __('Street Address <span class="description">(required)</span>', NHP_IN_PLUGIN);
		$fields['company_city'] = __('City Address <span class="description">(required)</span>', NHP_IN_PLUGIN);
		$fields['company_zip'] = __('Zip Code <span class="description">(required)</span>', NHP_IN_PLUGIN);
		return $fields;
	}//function
	
	
	
	
	/**
	 * mentioned above
	 *
	 * @package WPIU
	 * @since 3.3
	 * hacks the layout of the table so it looks like a seperate section while maintaining the simplicity of the profile fields hook
	 *
	 */
	function _divide_profile_table(){
		global $pagenow;
		if($pagenow == 'profile.php' || $pagenow == 'user-edit.php'){
			echo '<script type="text/javascript">'."\n";
			echo 'jQuery(document).ready(function(){'."\n";
				echo 'jQuery("#company_data").hide();'."\n";
				echo ''."\n";
			echo '});'."\n";
			echo '</script>'."\n";
		}//if	
	}
	
}//class
?>