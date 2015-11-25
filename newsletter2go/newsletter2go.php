<?php

/*
  Plugin Name: Newsletter2Go
  Plugin URI: https://www.newsletter2go.de/
  Description: A RESTful API for Newsletter2Go
  Version: 3.0.04
  Author: Newsletter2Go
  Author URI: https://www.newsletter2go.de/
 */

define('NEWSLETTER2GO_ROOT_PATH', dirname(__FILE__));

function n2GoApiInit()
{
    add_filter('rewrite_rules_array', 'n2GoApiRewrites');
    add_filter('query_vars', 'n2goAddQueryVars');
    add_action('template_redirect', 'n2goTemplateRedirect');
    require_once NEWSLETTER2GO_ROOT_PATH . "/gui/N2GoGui.php";
    N2GoGui::run();
}

function n2GoApiActivation()
{
    global $wp_rewrite;
    add_filter('query_vars', 'n2goAddQueryVars');
    add_filter('rewrite_rules_array', 'n2GoApiRewrites');
    $wp_rewrite->flush_rules();
    $general = array(
        'success' => 'Thank you for signing up. We have sent you an email with a confirmation link. Please check your inbox.',
        'failureSubsc' => 'Thank you for signing up. You are already signed up and will continue to receive our newsletter.',
        'failureEmail' => 'The email address you inserted does not seem to be valid. Please correct it.',
        'failureRequired' => 'Please fill all fields.',
        'failureError' => 'We were not able to sign you up. Please try again.',
        'buttonText' => 'Subscribe now!',
        'landingpage' => '',
    );
    n2goSaveOption('n2go_general', $general);
}

function n2GoApiDeactivation()
{
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function n2GoApiRewrites($wpRules)
{
    $n2goRules = array(
        "n2go-api\$" => 'index.php?pagename=n2go-api&method=test',
        "n2go-api/getVersion\$" => 'index.php?pagename=n2go-api&method=getVersion',
        "n2go-api/([^/]+)/([0-9]+)\$" => 'index.php?pagename=n2go-api&method=$matches[1]&postId=$matches[2]',
    );

    return array_merge($n2goRules, $wpRules);
}

function n2goAddQueryVars($aVars)
{
    $aVars[] = "method";
    $aVars[] = "postId";

    return $aVars;
}

function n2goTemplateRedirect()
{
    $pageNameVar = get_query_var('pagename');
    if ($pageNameVar == 'n2go-api') {
        require_once NEWSLETTER2GO_ROOT_PATH . "/api/N2GoApi.php";
        N2GoApi::run();
    }
}

function n2goSaveOption($id, $value)
{
    $option_exists = (get_option($id, null) !== null);
    if (!$option_exists) {
        add_option($id, $value);
    }
}

function n2goEditOption($id, $value)
{
    (get_option($id, null) !== null) ? update_option($id, $value) : add_option($id, $value);
}

add_action('init', 'n2GoApiInit');
require_once NEWSLETTER2GO_ROOT_PATH . "/widget/N2GoWidget.php";
register_activation_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2GoApiActivation');
register_deactivation_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2GoApiDeactivation');
