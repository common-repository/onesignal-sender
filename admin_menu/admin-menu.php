<?php
/**
 * admin_menu.php
 *
 * @package  Notifications system
 * @author   Marc Bou sleiman
 */
if (!defined('ABSPATH')) {
    exit;
}

function oss_notifications_admin_main_menu_scr() {
//    $icn = get_bloginfo('template_url') . '/includes/functions/admin_menu/images/notification-logo.png';
    $capability = 10;

    add_menu_page(__('OneSignal Sender'), __('OneSignal Sender', 'notificationssend'), $capability, "SP_menu_", 'oss_general_overview', "dashicons-rss", null, 0);
    add_submenu_page("SP_menu_", __('Overview', 'notifications'), __('Overview', 'notifications'), $capability, "oss_general_overview", 'oss_general_overview');
    add_submenu_page("SP_menu_", __('New Message', 'notifications'), __('New Message', 'notifications'), $capability, "oss_send_notifications", 'oss_send_notifications');
    add_submenu_page("SP_menu_", __('Scheduled', 'notifications'), __('Scheduled', 'notifications'), $capability, "oss_all_notifications", 'oss_all_notifications');
    add_submenu_page("SP_menu_", __('Sent', 'notifications'), __('Sent', 'notifications'), $capability, "oss_sent_notifications", 'oss_sent_notifications');
    add_submenu_page("SP_menu_", __('Settings', 'notifications'), __('Settings', 'notifications'), $capability, "oss_settings_page_options", 'oss_settings_page_options');
}

add_action('admin_menu', 'oss_notifications_admin_main_menu_scr');

/* functions */
include( plugin_dir_path(__FILE__) . 'sections/send-nots.php');
include( plugin_dir_path(__FILE__) . 'sections/all-nots.php');
include( plugin_dir_path(__FILE__) . 'sections/sent-nots.php');
include( plugin_dir_path(__FILE__) . 'sections/general_overview.php');


/* Including SRC files start */

function oss_onesignal_sender_src_files() {
    wp_enqueue_style('oss_onesignal_sender_intimidate_css', plugins_url('js/Intimidatetime-master/dist/Intimidatetime.min.css', __FILE__), array(), '1.0');
    wp_enqueue_script('oss_onesignal_sender_intimidate_js', plugins_url("js/Intimidatetime-master/dist/Intimidatetime.min.js", __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('oss_onesignal_sender_moment', plugins_url("js/moment/moment.js", __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('oss_onesignal_sender_moment_timezone', plugins_url("js/moment/moment-timezone-with-data.js", __FILE__), array('jquery'), '1.0', true);
}

add_action('admin_enqueue_scripts', 'oss_onesignal_sender_src_files');
/* Including SRC files end */

/* Register settings page start */

function oss_settings_page() {
    ?>
    <input type="text" class="oss_settings_page" name="oss_settings_page"
           value="<?php echo get_option('oss_settings_page'); ?>" />
           <?php
       }

       function oss_settings_page_fields() {
           add_settings_section("oss-settings-section", "Please Provide Your OneSignal User Authentication key (in order to see your app's info)", null, "oss-settings-options");

           add_settings_field("oss_settings_page", "Authenticaton key : ", "oss_settings_page", "oss-settings-options", "oss-settings-section");

           register_setting("oss-settings-section", "oss_settings_page");
       }

       add_action("admin_init", "oss_settings_page_fields");
       /* Register settings page end */

       /* Mourning Plugin page start */

       function oss_settings_page_options() {
           $OneSignalWPSetting = get_option('OneSignalWPSetting');
           $OneSignalWPSetting_app_id = $OneSignalWPSetting['app_id'];
           $OneSignalWPSetting_rest_api_key = $OneSignalWPSetting['app_rest_api_key'];
           $pluginList = get_option('active_plugins');
           $plugin = 'onesignal-free-web-push-notifications/onesignal.php';
           if (in_array($plugin, $pluginList) && $OneSignalWPSetting_app_id && $OneSignalWPSetting_rest_api_key) {
               ?>
        <div class="wrap oss_plugin_options">
            <div class="elt">
                <h2>Settings Page</h2>
            </div>
            <?php settings_errors(); ?>
            <form method="POST" action="options.php" class="settings_form">
                <?php
                settings_fields("oss-settings-section");
                do_settings_sections("oss-settings-options");
                ?>
                <h3 class="error_notice">Follow the below steps to get your OneSignal's user authentication key :</h3>
                <ul class="todo_list">
                    <li>Go to <a target="_blank" href="https://onesignal.com/">OneSignal.com</a> and log in</li>
                    <li>In the left side menu, click on Account</li>
                    <li>A popup will appear, click on Account & API keys</li>
                    <li>Scroll down to User Auth Key section</li>
                    <li>Copy and paste the key in the above field and click save options</li>
                    <li>Go to <a href="<?php echo admin_url('admin.php?page=oss_general_overview'); ?>">Overview page</a></li>
                </ul>
                <?php
                submit_button();
                ?>
            </form>
        </div>
        <h4 class="the_right_path">For any inquiry or concern feel free to <a href="http://marcbousleiman.com/#contact_me">CONTACT ME</a></h4>
    <?php } else { ?>
        <div class="wrap">
            <div class="icon32" id="icon-options-general">
                <br/>
            </div>
            <div class="header">
                <div class="elt">
                    <h2>Settings Page</h2>
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
    <style type="text/css">
        .the_right_path{
            color: #182b49;
            font-size: 20px;
            margin: 10px 22px 0 0;
            text-align: right;
        }

        .settings_form {
            padding: 0 25px;
        }

        .the_right_path a{
            color: #028482;
            text-decoration: underline;
        }
        .elt h2 {
            background: rgba(0, 0, 0, 0) linear-gradient(to right, #182b49, #028482) repeat scroll 0 0;
            color: #fff;
            font-size: 25px;
            margin: 0;
            padding: 30px 0;
            text-align: center;
        }

        .wrap.oss_plugin_options {
            background-color: #fff;
            padding: 0;
            border: 1px solid #929497;
        }

        .settings_form #submit{
            background-color: #fff;
            border: 1px solid #929497;
            border-radius: 0;
            box-shadow: none;
            color: #929497;
            font-weight: 600;
            height: 35px;
            padding: 0 15px;
            text-shadow: none;
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

        .wrap{
            background-color: #fff;
            padding: 0;
            border: 1px solid #929497;
        }
        .wrap .elt.srch{
            padding: 0 15px;
        }

        .error_notice {
            color: #182b49;
            text-align: center;
        }


        .notice_hr {
            background-color: rgba(0, 0, 0, 0.15);
            height: 1px;
            margin: 32px auto;
            width: 200px;
        }

        .error_notice:nth-child(3) {
            margin: 0;
            text-align: left;
        }
    </style>
    <?php
}
