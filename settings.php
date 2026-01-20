<?php
require('./inc/script_start.inc.php');

if (isset($_POST['usr_cust_save'])) {

    $old_settings = $s_cust;

    $s_cust['language'] = get_request_data('usr_cust_language');
    $s_cust['askdel'] = get_request_data('usr_cust_askdel') == $usr_strings['Yes'] ? 1 : 0;

    $settings_changed = true;
}

// reset the customizing values to the configuration defaults
if (isset($_POST['usr_cust_defaults'])) {

    $old_settings = $s_cust;
    $s_cust = get_customize_defaults($s_useragent);

    $settings_changed = true;
}

if ($settings_changed = true && isset($old_settings)) {

    if ($old_settings['language'] != $s_cust['language']) {

        include('./lang/' . $s_cust['language'] . '.inc.php');
        fix_language($s_cust['language']);
    }

    set_customize_cookie($s_cust);

    // force reloading of the stylesheet
    $s_stylesheet_etag = '';
}

// Redirect back to referer, but validate it first to prevent header injection
$referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'index.php';
// Validate referer to prevent header injection - only allow same-origin URLs
$referer_host = parse_url($referer, PHP_URL_HOST);
$current_host = $_SERVER['HTTP_HOST'];

// Allow only same-origin URLs (when host matches) or relative URLs (when host is null)
// Reject external URLs and malformed URLs
if (filter_var($referer, FILTER_VALIDATE_URL)) {
    // Absolute URL - must be same origin
    if ($referer_host === $current_host) {
        header("Location: " . $referer);
    } else {
        header("Location: index.php");
    }
} else {
    // Not a valid absolute URL - use default
    header("Location: index.php");
}

require('./inc/script_end.inc.php');

?>