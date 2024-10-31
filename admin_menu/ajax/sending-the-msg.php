<?php

/*
 *  ajax call for sending the notifications
 * 
 * @package  Notifications
 * @author   Marc Bou Sleiman
 */
if (!defined('ABSPATH')) {
    exit;
}
add_action('wp_ajax_oss_sending_the_msg', 'oss_sending_the_msg');
add_action('wp_ajax_nopriv_oss_sending_the_msg', 'oss_sending_the_msg');

function oss_sending_the_msg() {
    if (!wp_verify_nonce($_REQUEST['nonce'], "oss_sending_the_msg")) {
        exit("You think you are smart?");
    }
    $selected_method = strip_tags($_REQUEST['selected_method']);
    $notify_title = strip_tags($_REQUEST['notify_title']);
    $notify_message = strip_tags($_REQUEST['notify_message']);
    $notify_time = strip_tags($_REQUEST['notify_time']);

    if ($selected_method && $notify_title && $notify_message) {
        $result['html'] = "";


        $OneSignalWPSetting = get_option('OneSignalWPSetting');
        $OneSignalWPSetting_app_id = $OneSignalWPSetting['app_id'];
        $OneSignalWPSetting_rest_api_key = $OneSignalWPSetting['app_rest_api_key'];
        $selected_method = strip_tags($_REQUEST['selected_method']);
        $notify_title = strip_tags($_REQUEST['notify_title']);
        $notify_message = strip_tags($_REQUEST['notify_message']);
        $notify_time = strip_tags($_REQUEST['notify_time']);
        $notify_url = strip_tags($_REQUEST['notify_url']);

//        $date = new DateTime($notify_time);

//        $date_timestamp = $date->getTimestamp();
        $final_date = $notify_time;
        $final_readable_date = date('Y-m-d h:i:00a', $final_date);
        $content = array(
            "en" => $notify_message
        );
        $title = array(
            "en" => $notify_title
        );
        if ($selected_method == "send-scheduled") {
            $body = new stdClass();
            $body->app_id = $OneSignalWPSetting_app_id;
            $body->contents = $content;
            $body->headings = $title;
            $body->url = $notify_url;
            $body->send_after = $final_readable_date;
            $body->included_segments = array('All');
            $bodyAsJson = json_encode($body);
        } else {
            $body = new stdClass();
            $body->app_id = $OneSignalWPSetting_app_id;
            $body->contents = $content;
            $body->headings = $title;
            $body->url = $notify_url;
            $body->included_segments = array('All');
            $bodyAsJson = json_encode($body);
        }

        $response = wp_remote_post("https://onesignal.com/api/v1/notifications", array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array("Content-type" => "application/json;charset=UTF-8",
                "Authorization" => "Basic " . $OneSignalWPSetting_rest_api_key),
            'body' => $bodyAsJson,
                )
        );
//        echo $response["body"];

        if ($selected_method == "send-scheduled") {
            $result['msgstatus'] = "Scheduled";
        } else {
            $result['msgstatus'] = "Sent";
        }
        $result['type'] = "success";
        $result['api_response'] = $response["body"];
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
