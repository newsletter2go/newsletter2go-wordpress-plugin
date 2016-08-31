<?php

/*
  Plugin Name: Newsletter2Go
  Plugin URI: https://www.newsletter2go.de/
  Description: Adds email marketing functionality to your E-commerce platform. Easily synchronize your contacts and send product newsletters
  Version: 4.0.00
  Author: Newsletter2Go
  Author URI: https://www.newsletter2go.de/
 */

define('NEWSLETTER2GO_ROOT_PATH', dirname(__FILE__));
define('NEWSLETTER2GO_TEXTDOMAIN', 'newsletter2go');

function n2GoApiInit()
{
    add_filter('rewrite_rules_array', 'n2GoApiRewrites');
    add_filter('query_vars', 'n2goAddQueryVars');
    add_action('template_redirect', 'n2goTemplateRedirect');
    load_plugin_textdomain( NEWSLETTER2GO_TEXTDOMAIN , false, 'newsletter2go/lang/');
    require_once NEWSLETTER2GO_ROOT_PATH . "/gui/N2GoGui.php";
    N2GoGui::run();
}

function n2GoApiActivation()
{
    global $wp_rewrite;
    add_filter('query_vars', 'n2goAddQueryVars');
    add_filter('rewrite_rules_array', 'n2GoApiRewrites');
    $wp_rewrite->flush_rules();
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

/**
 * @param $attr
 * 
 * Shortcode syntax:
 * embedded default [newsletter2go], [newsletter2go type=plugin],
 * modal [newsletter2go type=popup], [newsletter2go type=popup delay=5]
 */
function n2goShortcode ($attr)
{
    $instance['title'] = 'Newsletter2Go';
    $args = array();

    if (is_array($attr) && isset($attr['type'])) {
        switch ($attr['type']) {
            case 'popup':
                $args['params'][0] = "'subscribe:createPopup'";
                (isset($attr['delay'])) ? $args['params'][3] = $attr['delay'] : $args['params'][3] = 5;
                break;
            default:
                $args['params'][0] = "'subscribe:createForm'";
                break;
        }
    }

    $widget = new N2GoWidget;
    $widget->widget($args, $instance);
}

add_action('init', 'n2GoApiInit');
require_once NEWSLETTER2GO_ROOT_PATH . "/widget/N2GoWidget.php";
register_activation_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2GoApiActivation');
register_deactivation_hook(NEWSLETTER2GO_ROOT_PATH . "/newsletter2go.php", 'n2GoApiDeactivation');
add_shortcode('newsletter2go', 'n2goShortcode');