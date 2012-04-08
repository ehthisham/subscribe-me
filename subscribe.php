<?php 
// allow us to use core wordpress functions so load wp-load.php
$plugin_name = 'subscribe-me';
$oldURL = dirname(__FILE__);
$newURL = str_replace(DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $plugin_name, '', $oldURL);
include($newURL . DIRECTORY_SEPARATOR . 'wp-load.php');

// validate email

function ValidateEmail($email)
{
	/*
	(Name) Letters, Numbers, Dots, Hyphens and Underscores
	(@ sign)
	(Domain) (with possible subdomain(s) ).
	Contains only letters, numbers, dots and hyphens (up to 255 characters)
	(. sign)
	(Extension) Letters only (up to 10 (can be increased in the future) characters)
	*/
 
	$regex = '/([a-z0-9_.-]+)'. # name

	'@'. # at

	'([a-z0-9.-]+){2,255}'. # domain & possibly subdomains

	'.'. # period

	'([a-z]+){2,10}/i'; # domain extension 

	if($email == '') { 
		return false;
	}
	else {
		$eregi = preg_replace($regex, '', $email);
	}
   
	return empty($eregi) ? true : false;
}

//validate country code

function ValidateCountryCode($country_code){
	$regex = '/^\d*$/';
	
	if($country_code == '') { 
		return true;
	}
	else {
		$eregi = preg_match($regex, $country_code);
	}
	return $eregi== 0 ? false : true;
}

//Validate phone number

function ValidatePhone($phone){
	$regex = '/^([0-9\(\)\/\+ \-]*)$/';
	
	if($phone == '') { 
		return false;
	}
	else {
		$eregi = preg_match($regex, $phone);
	}
	return empty($eregi) ||$eregi== 0 ? false : true;
}

//check if the phone number is already in our list

function AlreadyRegistered($phone){
	global $wpdb;
	$phone=ereg_replace( ' +', '', $phone);
	$ph = $wpdb->query( "SELECT phone FROM $wpdb->prefix"."subscribers_list where phone='".$phone."'" );
	if($ph){ return true;}
}

//process the request and add user to the database table

$post = (!empty($_POST)) ? true : false;
if($post)
{
	// get field values

	$name = stripslashes($_POST['name']);
	$email = trim($_POST['email']);
	$country_code = stripslashes($_POST['country_code']);
	$phone = stripslashes($_POST['phone']);
	$error = '';
 
	// Check if Name is entered
 
	if(!$name)
	{
		$error .= 'Please enter your name.<br />';
	}
 
	// Check email
 
	if(!$email)
	{
		$error .= 'Please enter an e-mail address.<br />';
	}
 
	if($email && !ValidateEmail($email))
	{
		$error .= 'Please enter a valid e-mail address.<br />';
	}
	
	if(!ValidateCountryCode($country_code))
	{
		$error .= "Please enter a valid country code.<br />";
	}
 
	// Check phone pattern="^[\d|\+|\(]+[\)|\d|\s|-]*[\d]$" or ^([0-9\(\)\/\+ \-]*)$
 
	if(!$phone)
	{
		$error .= "Please enter your phone. <br />";
	}
	
	if($phone && !ValidatePhone($phone))
	{
		$error .= "Please enter a valid phone number.<br />";
	}

	if(AlreadyRegistered($phone)){
		$error .= "Sorry! But the phone number you have entered is already registered.";
	}
	
	// if no errors found add user to db

	if(!$error) 
	{
			$phone=ereg_replace( ' +', '', $phone);
			$insert=$wpdb->query( $wpdb->prepare( 
			"
				INSERT INTO $wpdb->prefix"."subscribers_list
				( name , country_code, phone, email )
				VALUES ( %s, %d, %d, %s )
			", 
			$name, 
			$country_code,
			$phone, 
			$email 
		) );
	
		 #$wpdb->print_error(); // uncomment to see db inser errors if any 

		if($insert)
		{
			echo 'OK';
		}
 
	}
	else
	{
		echo '<div class="notification_error">'.$error.'</div>'; 
	}
 
}
?>
