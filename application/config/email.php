<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com';
// @link https://codeigniter.com/user_guide/libraries/email.html

// $config['useragent'] = '@webSchedulr';
// $config['protocol'] = 'mail'; // or 'smtp'
// $config['mailtype'] = 'html'; // or 'text'
// // $config['smtp_debug'] = '0'; // or '1'
// // $config['smtp_auth'] = TRUE; //or FALSE for anonymous relay.
// // $config['smtp_host'] = '';
// // $config['smtp_user'] = '';
// // $config['smtp_pass'] = '';
// // $config['smtp_crypto'] = 'ssl'; // or 'tls'
// // $config['smtp_port'] = 25;
// // $config['from_name'] = '';
// // $config['from_address'] = '';
// // $config['reply_to'] = '';
// $config['crlf'] = "\r\n";
// $config['newline'] = "\r\n";

// Production email configuration:

// <?php
// defined('BASEPATH') or exit('No direct script access allowed');


$config['useragent'] = '@webSchedulr';
$config['protocol'] = 'smtp'; // Use 'smtp' for sending via SMTP
$config['mailtype'] = 'html'; // Email format: 'html' or 'text'
$config['smtp_debug'] = '0'; // Set to '1' for debugging SMTP issues
$config['smtp_auth'] = TRUE; // Enable SMTP authentication
$config['smtp_host'] = 'smtp.office365.com'; // Microsoft 365 SMTP server
$config['smtp_user'] = 'sue@webschedulr.co.za'; // Your Microsoft 365 email address
$config['smtp_pass'] = 'blvjclsjfbmtmzth'; // Use the generated app password here
$config['smtp_crypto'] = 'tls'; // Encryption: 'tls' (required for Microsoft 365)
$config['smtp_port'] = 587; // Port for Microsoft 365 SMTP
$config['from_name'] = 'Apple Day Services'; // Sender's name
$config['from_address'] = 'sue@webschedulr.co.za'; // Sender's email address
$config['reply_to'] = 'sue@webschedulr.co.za'; // Reply-to email address
$config['crlf'] = "\r\n"; // Newline character
$config['newline'] = "\r\n"; // Newline character