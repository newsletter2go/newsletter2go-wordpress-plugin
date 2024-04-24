<?php

/*
  Plugin Name: Newsletter2Go
  Plugin URI: https://www.newsletter2go.co.uk/features/wordpress-newsletter-plugin/
  Description: Adds email marketing functionality to your E-commerce platform. Easily synchronize your contacts and send product newsletters
  Version: 4.0.14
  Author: Newsletter2Go
  Author URI: https://www.newsletter2go.de/
 */

define('NEWSLETTER2GO_ROOT_PATH', dirname(__FILE__));
define('NEWSLETTER2GO_TEXTDOMAIN', 'newsletter2go');

function n2Go_ApiInit()
{
    add_filter('rewrite_rules_array', 'n2Go_ApiRewrites');
    add_filter('query_vars', 'n2Go_AddQueryVars');
    add_action('template_redirect', 'n2Go_TemplateRedirect');
    add_action('template_redirect', 'n2Go_Callback');
    load_plugin_textdomain( NEWSLETTER2GO_TEXTDOMAIN , false, 'newsletter2go/lang/');
    require_once NEWSLETTER2GO_ROOT_PATH . "/gui/N2Go_Gui.php";
    N2Go_Gui::run();
}

function n2Go_ApiActivation()
{
    global $wp_rewrite;
    add_filter('query_vars', 'n2Go_AddQueryVars');
    add_filter('rewrite_rules_array', 'n2Go_ApiRewrites');
    $wp_rewrite->flush_rules();

    $authKey = wp_generate_password(40, false);
    (get_option('n2go_apikey', null) !== null) ? update_option('n2go_apikey', $authKey) : add_option('n2go_apikey', $authKey);
}


function n2Go_ApiDeactivation()
{
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function n2Go_ApiRewrites($wpRules)
{
    $n2goRules = array(
        "n2go-api\$" => 'index.php?pagename=n2go-api&method=test',
        "n2go-api/getVersion\$" => 'index.php?pagename=n2go-api&method=getVersion',
        "n2go-api/([^/]+)/([0-9]+)\$" => 'index.php?pagename=n2go-api&method=$matches[1]&postId=$matches[2]',
        "n2go-callback\$" => 'index.php?pagename=n2go-callback'
    );

    return array_merge($n2goRules, $wpRules);
}

function n2Go_AddQueryVars($aVars)
{
    $aVars[] = "method";
    $aVars[] = "postId";

    return $aVars;
}

function n2Go_TemplateRedirect()
{
    $pageNameVar = get_query_var('pagename');
    if ($pageNameVar == 'n2go-api') {
        require_once NEWSLETTER2GO_ROOT_PATH . "/api/N2Go_Api.php";
        N2Go_Api::run();
    }
}

function n2Go_Callback()
{
    $pageNameVar = get_query_var('pagename');
    if ($pageNameVar == 'n2go-callback') {
        $result = array('result' => false);
        $accessToken = filter_input(INPUT_POST, 'access_token');
        $refreshToken = filter_input(INPUT_POST, 'refresh_token');
        $authKey = filter_input(INPUT_POST, 'auth_key');
        if(isset($accessToken) && !empty($accessToken) && isset($refreshToken) && !empty($refreshToken) && isset($authKey) && !empty($authKey)) {
            (get_option('n2go_accessToken', null) !== null) ? update_option('n2go_accessToken', $accessToken) : add_option('n2go_accessToken', $accessToken);
            (get_option('n2go_refreshToken', null) !== null) ? update_option('n2go_refreshToken', $refreshToken) : add_option('n2go_refreshToken', $refreshToken);
            (get_option('n2go_authKey', null) !== null) ? update_option('n2go_authKey', $authKey) : add_option('n2go_authkey', $authKey);
            header('HTTP/1.1 200 OK', true);
            $result = array('result' => true);
        }
        wp_send_json($result);
    }
}

/**
 * @param $attr
 *
 * Shortcode syntax:
 * embedded default [newsletter2go], [newsletter2go type=plugin],
 * modal [newsletter2go type=popup], [newsletter2go type=popup delay=5]
 * @return mixed
 */
function n2Go_Shortcode ($attr)
{
    $instance['title'] = isset($attr['title']) ? $attr['title'] : '';
    $args = array();

    $form_type = 'subscribe';
    if (is_array($attr) && isset($attr['form_type'])) {
        switch ($attr['form_type']) {
            case 'unsubscribe':
                $form_type = 'unsubscribe';
                break;
            default:
                $form_type = 'subscribe';
                break;
        }
    }

    if (is_array($attr) && isset($attr['type'])) {
        switch ($attr['type']) {
            case 'popup':
                $args['params'][0] = "'" . $form_type . ":createPopup'";
                (isset($attr['delay'])) ? $args['params'][3] = $attr['delay'] : $args['params'][3] = 5;
                break;
            default:
                $args['params'][0] = "'" . $form_type . ":createForm'";
                break;
        }
    }

    $instance['type'] = $form_type;

    $widget = new N2Go_Widget;
    return $widget->widget($args, $instance, false);
}

function n2Go_DeletePluginOptions() {
    delete_option('n2go_authKey');
    delete_option('n2go_accessToken');
    delete_option('n2go_refreshToken');
    delete_option('n2go_formUniqueCode');
}

add_action('init', 'n2Go_ApiInit');
require_once NEWSLETTER2GO_ROOT_PATH . "/widget/N2Go_Widget.php";
register_activation_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2Go_ApiActivation');
register_deactivation_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2Go_ApiDeactivation');
register_uninstall_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2Go_DeletePluginOptions');
add_shortcode('newsletter2go', 'n2Go_Shortcode');
