jQuery(document).ready(function() {
	
	
	
	jQuery('.datepicker').each(function() {
		jQuery(this).datepicker({ dateFormat: 'dd-mm-yy' });
	});
	
	check_paid_val();
	
	jQuery('#wppm_paid').change(function(){
		check_paid_val();
	});
	
	function check_paid_val(){
		if(jQuery('#wppm_paid').val() == 'Part'){
			jQuery('.amount_right').fadeIn('slow');
		}else{
			jQuery('#wppm_paid_amount').val('');
			jQuery('.amount_right').fadeOut('slow')
		}
	}
	
	
	
	
	jQuery('.delete-att').click(function(){
		att_id = jQuery(this).attr('data-att-id');
		wppm_nonce = jQuery('#_wppm_project_meta_nonce').val();
		proceed = confirm(wppm_lang.confirm_delete);
		
		if(proceed){
		
			var data = {action: 'project_delete_att',att_delete_id: att_id, _wppm_project_meta_nonce: wppm_nonce};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if(response == 'success'){
					alert(wppm_lang.delete_success);
					jQuery('#att-'+att_id).fadeOut('slow');
				}else{
					alert(response);
				}
			});	//post
		}//if proceed
		
	});
	
	
	
	
	
	
	
});