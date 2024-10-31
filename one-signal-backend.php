<?php
/*
  Plugin Name: OneSignal Sender
  Plugin URI: http://marcbousleiman.com
  Description: A OneSignal Addon to send Notifications from the backend.
  Version: 1.4
  Author: Marc Bou Sleiman
  Author URI: http://marcbousleiman.com
 */

/*Includes start*/

/*Main functions start*/
include_once( plugin_dir_path( __FILE__ ) . 'admin_menu/admin-menu.php');
/*Main functions end*/

/*Canceling ajax functions start*/
include_once( plugin_dir_path( __FILE__ ) . 'admin_menu/ajax/canceling-the-msg.php');
///*Main functions end*/

///*Sending the message ajax functions start*/
include_once( plugin_dir_path( __FILE__ ) . 'admin_menu/ajax/sending-the-msg.php');
/*Main functions end*/

/*Includes end*/
