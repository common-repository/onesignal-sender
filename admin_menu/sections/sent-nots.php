<?php
/**
 * checking scheduled Notifications systems
 *
 * @package  All Notifications
 * @author   Marc Bou Sleiman
 */
if (!defined('ABSPATH')) {
    exit;
}

function oss_sent_notifications() {
    $OneSignalWPSetting = get_option('OneSignalWPSetting');
    $OneSignalWPSetting_app_id = $OneSignalWPSetting['app_id'];
    $OneSignalWPSetting_rest_api_key = $OneSignalWPSetting['app_rest_api_key'];
    $pluginList = get_option('active_plugins');
    $plugin = 'onesignal-free-web-push-notifications/onesignal.php';
    if (in_array($plugin, $pluginList) && $OneSignalWPSetting_app_id && $OneSignalWPSetting_rest_api_key) {
        ?>
        <div class="bread_crumbs">
            <a href="<?php echo admin_url('admin.php?page=oss_general_overview'); ?>">Overview</a>
            <a href="<?php echo admin_url('admin.php?page=oss_send_notifications'); ?>">Send Notification</a>
            <a href="<?php echo admin_url('admin.php?page=oss_all_notifications'); ?>">Scheduled Notifications</a>
            <a href="<?php echo admin_url('admin.php?page=oss_sent_notifications'); ?>" class="active_bread">Sent Notifications</a>
        </div>
        <div class="wrap">
            <div class="icon32" id="icon-options-general">
                <br/>
            </div>
            <div class="header">
                <div class="elt">
                    <h2>Sent Notifications</h2>
                </div>
                <div class="statistics_wrapper">
                    <span class="close_statistics_wrapper">close</span>
                    <h3 class="notification_failed"></h3>
                    <h3 class="notification_delivered"></h3>
                    <h3 class="notification_pending"></h3>
                    <h3 class="notification_converted"></h3>
                    <h3 class="notification_not_converted"></h3>
                </div>
                <!--<div class="elt srch">-->
                <?PHP
                $OneSignalWPSetting = get_option('OneSignalWPSetting');
                $OneSignalWPSetting_app_id = $OneSignalWPSetting['app_id'];
                $OneSignalWPSetting_rest_api_key = $OneSignalWPSetting['app_rest_api_key'];

                $args = array(
                    'headers' => array(
                        'Authorization' => 'Basic ' . $OneSignalWPSetting_rest_api_key
                    ),
                    'timeout' => 500,
                    'sslverify' => false,
                );
                $url = "https://onesignal.com/api/v1/notifications?app_id=" . $OneSignalWPSetting_app_id . "&limit=50&offset=0";
                $response = wp_remote_get($url, $args);

                $response_to_arrays = json_decode(wp_remote_retrieve_body($response), true);
                ?>
                <div class="panel table-panel panel-default">
                    <table id="notifications" class="table table-striped">
                        <thead>
                            <tr>
                                <th colspan="2">Actions</th>
                                <th colspan="2">Status</th>
                                <th colspan="2">Sent At</th>
                                <th colspan="2">Message</th>
                                <th colspan="2">Sent To</th>
                                <th colspan="2">Clicked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $response_counter = 0;
//                var_dump($response_to_arrays);
                            if ($response_to_arrays['notifications']) {
                                foreach ($response_to_arrays['notifications'] as $response_array) {
//                    var_dump($response_array) . '<br>';
                                    $now_time = time();
                                    $notification_send_after = $response_array['send_after'];
                                    if ($now_time > $notification_send_after) {
                                        $notification_id = $response_array['id'];
                                        $app_id = $response_array['app_id'];
                                        $if_canceled = $response_array['canceled'];
                                        $notification_url = $response_array['url'];
                                        $notification_queued_at = $response_array['queued_at'];
                                        $final_readable_date_queued = date('d, F Y h:i:00a', $notification_queued_at);
                                        $final_readable_date_after = date('d, F Y h:i:00a', $notification_send_after);
                                        $notification_message = $response_array['contents']['en'];
                                        $notification_converted = $response_array['converted'];
                                        $notification_delivered = $response_array['successful'];
                                        $notification_failed = $response_array['failed'];
                                        $notification_pending = $response_array['remaining'];
                                        if ($notification_delivered > 0 && $if_canceled != 1) {
                                            $delivery_response = 'Delivered';
                                        } else {
                                            $delivery_response = 'Canceled';
                                        }

                                        $total_notification_stats = $notification_delivered + $notification_failed + $notification_pending;
                                        $decimal_delivered = ($notification_delivered / $total_notification_stats) * 100;
                                        $decimal_failed = ($notification_failed / $total_notification_stats) * 100;
                                        $decimal_pending = ($notification_pending / $total_notification_stats) * 100;
                                        $decimal_converted = ($notification_converted / $total_notification_stats) * 100;
                                        $decimal_not_converted = 100 - $decimal_converted;
                                        ?>
                                        <tr class="notification-entry" data-unix="<?php echo $notification_send_after; ?>">
                                            <td colspan="2" class="one action text-center">
                                                <span data-not-converted="<?php echo round($decimal_not_converted); ?>" data-converted="<?php echo round($decimal_converted); ?>" data-failed="<?php echo round($decimal_failed); ?>" data-delivered="<?php echo round($decimal_delivered); ?>" data-pending="<?php echo round($decimal_pending); ?>" class="view_statistics_button">View Statistics</span>
                                            </td>
                                            <td colspan="2" class="notification-status">
                                                <span class="status-label <?php echo strtolower($delivery_response); ?>">
                                                    <?php echo $delivery_response; ?>
                                                </span>
                                            </td>
                                            <td colspan="2" class="submitted date">
                                                -
                                            </td>
                                            <td colspan="2" class="notification-content">
                                                <?php echo $notification_message; ?>
                                            </td>
                                            <td colspan="2" class="notification-content">
                                                <?php echo $notification_delivered; ?>
                                            </td>
                                            <td colspan="2" class="notification-content">
                                                <?php echo $notification_converted; ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $response_counter++;
                                    }
                                }
                            }
                            if ($response_counter == 0 || !$response_to_arrays['notifications']) {
                                ?>
                                <tr class="notification-entry">
                                    <td colspan="12" class="one action text-center no_notifications">
                                        You have no sent notifications.
                                    </td>
                                </tr>
                            <?php }
                            ?>
                        </tbody>
                    </table>
                </div>       
                <!--</div>-->
                <div class="clear"></div>
            </div>
        </div>
        <h4 class="the_right_path">For any inquiry or concern feel free to <a href="http://marcbousleiman.com/#contact_me">CONTACT ME</a></h4>
    <?php } else { ?>
        <div class="bread_crumbs">
            <a href="<?php echo admin_url('admin.php?page=oss_general_overview'); ?>">Overview</a>
            <a href="<?php echo admin_url('admin.php?page=oss_send_notifications'); ?>">Send Notification</a>
            <a href="<?php echo admin_url('admin.php?page=oss_all_notifications'); ?>">Scheduled Notifications</a>
            <a href="<?php echo admin_url('admin.php?page=oss_sent_notifications'); ?>" class="active_bread">Sent Notifications</a>
        </div>
        <div class="wrap">
            <div class="icon32" id="icon-options-general">
                <br/>
            </div>
            <div class="header">
                <div class="elt">
                    <h2>Sent Notifications</h2>
                </div>
                <div class="elt srch">
                    <h3 class="error_notice">Please complete the OneSignal – Free Web Push Notifications setup before using this plugin.</h3>
                    <div class="notice_hr"></div>
                    <h3 class="error_notice">To do so :</h3>
                    <ul class="todo_list">
                        <li>Download <a href="https://wordpress.org/plugins/onesignal-free-web-push-notifications/">OneSignal – Free Web Push Notifications</a></li>
                        <li>Activate OneSignal – Free Web Push Notifications plugin</li>
                        <li>Go to OneSignal – Free Web Push Notifications settings page</li>
                        <li>Provide the App ID and the REST API key as mentioned</li>
                        <li>Save and get back here...</li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <h4 class="the_right_path">For any inquiry or concern feel free to <a href="http://marcbousleiman.com/#contact_me">CONTACT ME</a></h4>
    <?php } ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            //convert unix time found in tr data attr to human date
            jQuery('#notifications > tbody  > tr').each(function () {
                var unix_value = jQuery(this).attr('data-unix');
                jQuery(this).find('td.submitted.date').html(moment.unix(unix_value).format('dddd, MMMM Do, YYYY h:mm:00a'));
            });
            //on click open statistics div and scroll up
    //            var notification_failed;
    //            var notification_delivered ;
    //            var notification_pending ;
    //            var notification_converted ;
    //            var notification_not_converted ;
            jQuery('.view_statistics_button').on('click', function () {
                var notification_failed = jQuery(this).attr('data-failed');
                var notification_delivered = jQuery(this).attr('data-delivered');
                var notification_pending = jQuery(this).attr('data-pending');
                var notification_converted = jQuery(this).attr('data-converted');
                var notification_not_converted = jQuery(this).attr('data-not-converted');
                var notification_row = jQuery(this).closest('.notification-entry').attr('id');
                jQuery('.close_statistics_wrapper').attr('data-not-row', notification_row);
                jQuery('html,body').animate({
                    scrollTop: jQuery('.wrap').offset().top},
                        1000);
                jQuery('.statistics_wrapper').slideDown(700);
                jQuery('.statistics_wrapper').slideDown(700);
                jQuery('.statistics_wrapper h3.notification_failed').html('<span>Failed</span>: ' + notification_failed + '%');
                jQuery('.statistics_wrapper h3.notification_delivered').html('<span>Delivered</span>: ' + notification_delivered + '%');
                jQuery('.statistics_wrapper h3.notification_pending').html('<span>Pending</span>: ' + notification_pending + '%');
                jQuery('.statistics_wrapper h3.notification_converted').html('<span>Converted</span>: ' + notification_converted + '%');
                jQuery('.statistics_wrapper h3.notification_not_converted').html('<span>Not converted</span>: ' + notification_not_converted + '%');
            });

    //click to close statistics_wrapper
            jQuery('.close_statistics_wrapper').on('click', function () {
    //            var notification_row = jQuery(this).attr('data-not-row');
                jQuery('.statistics_wrapper').slideUp(700);
            });
        });
    </script> 
    <style type="text/css">
        .statistics_wrapper {
            text-align: center;
            display: none;
            position: relative;
        }
        
        .statistics_wrapper h3 span{
            text-decoration: underline;
        }

        .close_statistics_wrapper{
            cursor: pointer;
            text-decoration: underline;
            font-weight: bold;
            color: #000;
            position: absolute;
            top: 2%;
            right: 2%;
        }

        .view_statistics_button {
            cursor: pointer;
            text-decoration: underline;
        }

        .view_statistics_button:hover{
            color: #7CB5EC;
        }

        .bread_crumbs{
            text-align: center;
            margin: 40px 0 0 0;
        }

        .the_right_path{
            color: #182b49;
            font-size: 20px;
            margin: 10px 22px 0 0;
            text-align: right;
        }

        .the_right_path a{
            color: #028482;
            text-decoration: underline;
        }

        .error_notice{
            text-align: center;
            color: #182b49;
        }

        .error_notice:nth-child(3){
            text-align: left;
            margin: 0;
        }

        ul.todo_list{
            list-style: upper-greek;
            text-align: left;
            padding: 0 17px;
        }

        ul.todo_list li{
            color: #182b49;
            font-size: 17px;
        }

        .notice_hr{
            height: 1px;
            width: 200px;
            background-color: rgba(0,0,0,0.15);
            margin: 32px auto;
        }

        ul.todo_list li a{
            background-color: transparent;
            text-decoration: underline !important;
            color: #000;
            border: none !important;
            box-shadow: none !important;
        }

        .bread_crumbs a {
            color: #182b49 !important;
            border: 2px solid #182b49;
            display: inline-block;
            font-size: 16px;
            min-width: 200px;
            padding: 7px 25px;
            text-decoration: none;
            margin: 0 25px 0 0;
        }

        .bread_crumbs a.active_bread{
            text-decoration: underline;
            color: #929497;
            font-weight: bold;
        }
        .schedule_date_section{
            display: none;
            position: relative;
        }

        .wrap {
            background-color: #fff;
            margin: 40px 20px 0 0;
            border: 1px solid #182b49;
            box-shadow: 0 0 3px 0 grey;
        }
        .status{
            text-align:center;
        }
        .status .loader{
            display:none;
            margin:0 auto;
        }
        .status p{
            font-weight: bold;
            font-size:16px;
        }
        .loader {
            display: none;
        }

        .widefat tbody th {
            color: #000;
        }

        .widefat tbody th a {
            color: #000;
            font-weight: bold;
        }

        .widefat tbody tr td{
            color: #000;
        }

        .clear {
            clear: both;
        }

        .header .elt {
            display: block;
        }

        .header .elt h2 {
            background: #182b49; /* For browsers that do not support gradients */
            background: -webkit-linear-gradient(left, #182b49 , #028482); /* For Safari 5.1 to 6.0 */
            background: -o-linear-gradient(right, #182b49, #028482); /* For Opera 11.1 to 12.0 */
            background: -moz-linear-gradient(right, #182b49, #028482); /* For Firefox 3.6 to 15 */
            background: linear-gradient(to right, #182b49 , #028482); /* Standard syntax */
            color: #fff;
            font-size: 25px;
            margin: 0;
            padding: 30px 0;
            text-align: center;
        }

        .header .elt.srch {
            display: block;
            text-align: left;
            padding: 30px;
        }

        .header .elt.srch form input[name="submit"]{
            background-color: #fff;
            color: #611341;
            font-weight: bold;
            margin: 25px 0 0 0;
        }

        .header .elt.srch form input[name="submit"]:hover{
            background-color: rgba(255,255,255,0.5);
        }

        .header .elt.srch form input#notification-title,
        .header .elt.srch form input#notification-url,
        .header .elt.srch form textarea{
            width: 100%;
        }

        .header .elt.srch form input[name="submit"],
        .header .elt.srch form textarea,
        .header .elt.srch form input{
            border: 1px solid #929497;
            padding: 7px;
            font-weight: 600;
            border-radius: 0;
            background-color: #fff;
            color: #929497;
        }

        .header .elt.srch a {
            -webkit-box-shadow: 1.7px 1.7px 1px #787878;
            -moz-box-shadow: 1.7px 1.7px 1px #787878;
            box-shadow: 1.7px 1.7px 1px #787878;
            padding: 5px;
            text-decoration: none;
            border: 1px solid #611431;
            background-color: #fff;
            color: #611431;
        }
        .Zebra_DatePicker{
            top: 14% !important;
        }

        .datepicker-type-wrapper{
            margin: 0 0 20px 0;
        }

        .datepicker-type-wrapper .radio-wrapper{
            display: block;
            margin: 10px 30px;
        }

        .section_title{
            color: #929497;
            font-size: 17px;
            font-weight: bold;
            line-height: normal;
            margin: 10px 0;
            display: block;
        }

        .section_title h6{
            display: inline-block;
            margin: 0;
        }

        .datepicker-type-wrapper .radio-wrapper label{
            vertical-align: top;
            min-height: 18px;
            color: #505050;
            font-size: 16px;
        }

        .ajax_result h5{
            background-color: rgba(146, 148, 151, 0.30);
            display: inline-block;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0 0;
            padding: 17px 25px;
        }

        .wppb-serial-notification{
            display: none;
        }

        .panel {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .panel > .table:last-child, .panel > .table-responsive:last-child > .table:last-child {
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            max-width: 100%;
            width: 100%;
            margin-bottom: 0;
        }

        .table thead tr, .fc-border-separate thead tr {
            background-color: #eeeeee;
            font-size: 12px;
        }

        .table > thead > tr > th {
            border-bottom: 2px solid #ddd;
        }

        .table > thead > tr > th, 
        .table > thead > tr > td, 
        .table > tbody > tr > th, 
        .table > tbody > tr > td, 
        .table > tfoot > tr > th, 
        .table > tfoot > tr > td {
            line-height: 1.42857;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            max-width: 100px;
        }

        .table-striped > tbody > tr:nth-child(2n+1) {
            background-color: #f9f9f9;
        }

        .text-center{
            text-align: center;
        }

        .dashboard-button, .dropdown-button {
            background-color: #dedede;
            border: 1px solid #d9d9d9;
            border-radius: 3px;
            transition: none 0s ease 0s ;
            padding: 0.7em 1.1em;
            box-shadow: 0 -2px 0 rgba(0, 0, 0, 0.05) inset;
            color: #333;
            cursor: pointer;
        }

        .status-label.delivered {
            background-color: #82bf82;
            border: 1px solid #82bf82;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 12px;
            font-weight: 400;
            letter-spacing: 0.07em;
            line-height: 1;
            margin: 10px 5px;
            padding: 11px 8px;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle;
            white-space: nowrap;
            text-decoration: none;
            min-width: 70px;
        }

        .status-label.canceled{
            background-color: #caa36e;
            border: 1px solid #caa36e;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 12px;
            font-weight: 400;
            letter-spacing: 0.07em;
            line-height: 1;
            margin: 10px 5px;
            padding: 11px 8px;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle;
            white-space: nowrap;
            text-decoration: none;
            min-width: 70px;
        }

        .no_notifications{
            color: #b2b2b2;
            font-size: 1.35em;
            font-weight: 300 !important;
        }
    </style>
    <?php
}
