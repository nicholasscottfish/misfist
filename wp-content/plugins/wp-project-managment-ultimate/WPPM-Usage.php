<?php
class WPPM_Ultimate_Usage extends WPPM_Ultimate{
	
	function __construct(){
	}//function 
	
	
	/**
	 * easy way to get options without having to parse the options array, simply ask for the array value you want
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function option($option_name, $return = false){
		$options = get_option(WPPM_OPTION);
		if($options[$option_name]){return $options[$option_name];}else{if($return){return $return;}else{return false;}}
	}//function
	
	
}//class
?>