<?php
class WPIU_Ipn_Log extends WPIU{
	
	
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
		
	}//function
	
	
	
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
				$this->page_hook = add_submenu_page('edit.php?post_type=wpiu-invoices',__( 'WP Invoices Ultimate IPN Log',WPIU_LANG),__( 'Invoice IPN Log',WPIU_LANG),'administrator','wp_iu_log', array(&$this,'_page_html'));
		
		
	}//function
	
	
	
	/**
	 * the page html
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _page_html(){$log = get_option(WPIU_OPTION.'_ipn_log');?>
		<div class="wrap">
			<img src="<?php echo WPIU_IMG .'32/icon.png';?>" style="margin:14px 6px 0px 0px;float:left;"/>
			<h2 style="display:inline-block;"><?php echo get_admin_page_title(); ?></h2>
			<p style="margin-top:40px;"><?php _e( 'The IPN Log contains all post data sent from paypal to your domian through the invoicing system. This can be used to debug issues if they arise and see an overview of all your transactions, complted or failed.',WPIU_LANG);?></p>
			
			<?php
				
		
		if($log){?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#invoice_trans .details').hide();
			jQuery('#invoice_trans .trans_show').click(function(){
				jQuery(this).next('div').slideToggle('slow');	
			});
		});//end onload
		</script>
		<?php
		
		echo '<table class="widefat" id="invoice_trans">';
		
		echo '<thead><tr>';
				echo '<th>'.__('Amount', WPIU_LANG).'</th>';	
				echo '<th>'.__('Date', WPIU_LANG).'</th>';
				echo '<th>'.__('Transaction ID', WPIU_LANG).'</th>';
				echo '<th>'.__('Details', WPIU_LANG).'</th>';		
		echo '</tr></thead>';
		
		echo '<tbody>';
		
		foreach(array_reverse($log) as $trans){
			echo '<tr>';
				echo '<td>'.$trans['mc_gross'].'</td>';
				echo '<td>'.$trans['payment_date'].'</td>';
				echo '<td><a target="_blank" href="https://www.paypal.com/uk/cgi-bin/webscr?cmd=_view-a-trans&id='.$trans['txn_id'].'">'.$trans['txn_id'].'</a></td>';

				echo '<td width="50%">';
					echo '<a href="javascript:void(0);" class="trans_show">'.__('Show Details', WPIU_LANG).'</a>';
					echo '<div class="details">';
						foreach($trans as $transk => $transv){
							echo '<strong>'.$transk.': </strong> '.$transv.'<br/>'; 	
						}
					echo '</div>';
				echo '</td>';
			echo '</tr>';
		}//foreach
		
		echo '</tbody>';

		
		
		echo '</table>';
		}else{
			_e('<p>No IPN data to display</p>', WPIU_LANG);	
		}
		?>
				<div class="clear"></div>
		</div>	<?php 
	}//function

		
}//class
?>