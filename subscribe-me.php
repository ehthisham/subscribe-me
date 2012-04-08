<?php  
    /* 
    Plugin Name: SubscribeMe
    Description: This plugin helps user to subscribe to your site and are displayed in your dashboard under Subscribers. 
    Author: Ehthisham tk
    Author URI: www.wowmakers.com
    Version: 1.0 
    */  
		/*
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY;
		*/

global $sme_db_version;
$sme_db_version = "1.0";

function sme_install() {
   global $wpdb;
   global $sme_db_version;

   $table_name = $wpdb->prefix . "subscribers_list";
      
   $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  name tinytext NOT NULL,
	  country_code VARCHAR(10) NULL,
	  phone VARCHAR(20) NOT NULL,
	  email VARCHAR(55) DEFAULT '' NOT NULL,
	  UNIQUE KEY id (id)
    );";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   add_option("sme_db_version", $sme_db_version);
}

register_activation_hook(__FILE__,'sme_install');


$installed_ver = get_option( "sme_db_version" );

   if( $installed_ver != $sme_db_version ) {

      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  name tinytext NOT NULL,
	  country_code VARCHAR(10) NULL,
	  phone VARCHAR(20) NOT NULL,
	  email VARCHAR(55) DEFAULT '' NOT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      update_option( "sme_db_version", $sme_db_version );
  }


function sme_update_db_check() {
    global $sme_db_version;
    if (get_site_option('sme_db_version') != $sme_db_version) {
        sme_install();
    }
}
add_action('plugins_loaded', 'sme_update_db_check');

/************Dashboard needs**************/

function subscribeme2dashboard(){
	include('subscribe-me-admin.php');
}
function subscribeme_actions(){
	add_menu_page('Subscirbers','Subscirbers','read','subscribers-list','subscribeme2dashboard');
}
add_action('admin_menu','subscribeme_actions');


/************shortcode functions*********/

$sme_base_dir = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__)); 
function subscription_shortcode( $atts, $content = null)	{
 
	// gives access to the plugin's base directory
	
	global $sme_base_dir;
 
	extract( shortcode_atts( array(
      'title' => 'Subscribe Me'
      ), $atts ) );
 
		$content .= '
		<script type="text/javascript"> 
			var $j = jQuery.noConflict();
			$j(window).load(function(){				
				$j("#subscription-form").submit(function() {
				  // validate and process form here
				  if($j("#subscription-form").valid()== false) {
						return false;
				  }
					var str = $j(this).serialize();					 
					$j.ajax({
					   type: "POST",
					   url: "' . $sme_base_dir . 'subscribe.php",
					   data: str,
					   success: function(msg){						
							$j("#note").ajaxComplete(function(event, request, settings)
							{ 
								if(msg == "OK") // Data inserted; now show thank you message.
								{
									result = "You have successfully subscribed. Thank You!";
									$j("#subscribe-me-fields").hide();
								}
								else
								{
									result = msg;
								}								 
								$j(this).html(result);							 
							});					 
						}					 
					 });					 
					return false;
				});			
			});
		</script>'; 

    // The subscribe form

		$content .= '<div id="subscribe-me-wrap" class="clear">';
		$content .= '	<div id="subscribe-me-fields">';
		$content .= '		<h4>'.$title.'</h4>';
		$content .= '		<form id="subscription-form" action="">';
		$content .= '			<p>';
		$content .= '				<label for="name">Name *</label>';
		$content .= '				<input name="name" type="text" id="name" class="required"/>';
		$content .= '			</p>';
		$content .= '			<p>';
		$content .= '				<label for="email">E-mail address *</label>';
		$content .= '				<input name="email" type="text" id="email" class="required email"/>';
		$content .= '			</p>';
		$content .= '			<p><label for="country_code">Country code</label><input name="country_code" type="text" id="country-code" class="number"/></p>';
		$content .= '			<p><label for="phone">Phone *</label>';
		$content .= '			<input name="phone" type="text" id="phone" class="required number"/></p>'; 
		$content .= '			<p><input type="submit" value="Submit" class="button" id="contact-submit" /></p>';
		$content .= '		</form>';
		$content .= '	</div><!--end fields-->';
		$content .= '	<div id="note"></div> <!--notification area used by jQuery/Ajax -->';
		$content .= '</div>';
	return $content;
}
add_shortcode('subscribe-me', 'subscription_shortcode');
?>
