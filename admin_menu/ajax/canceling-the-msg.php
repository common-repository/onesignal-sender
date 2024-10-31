<?php

/*
 *  ajax call for canceling the notifications
 * 
 * @package  Notifications
 * @author   Marc Bou Sleiman
 */
if (!defined('ABSPATH')) {
    exit;
}
add_action('wp_ajax_oss_canceling_the_msg', 'oss_canceling_the_msg');
add_action('wp_ajax_nopriv_oss_canceling_the_msg', 'oss_canceling_the_msg');

function oss_canceling_the_msg() {
    if (!wp_verify_nonce($_REQUEST['nonce'], "oss_canceling_the_msg")) {
        exit("You think you are smart?");
    }
    $notify_id = strip_tags($_REQUEST['notify_id']);

    if ($notify_id) {
        $result['html'] = "";

            $OneSignalWPSetting = get_option('OneSignalWPSetting');
            $OneSignalWPSetting_app_id = $OneSignalWPSetting['app_id'];
            $OneSignalWPSetting_rest_api_key = $OneSignalWPSetting['app_rest_api_key'];
            $notify_id = strip_tags($_REQUEST['notify_id']);
            
        $response = wp_remote_post("https://onesignal.com/api/v1/notifications/" . $notify_id . "?app_id=" . $OneSignalWPSetting_app_id, array(
            'method' => 'DELETE',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array("Content-type" => "application/json;charset=UTF-8",
                "Authorization" => "Basic " . $OneSignalWPSetting_rest_api_key)
                )
        );
        $result['type'] = "success";
    } else {
        $result['type'] = "error";
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    } else {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    die();
}
