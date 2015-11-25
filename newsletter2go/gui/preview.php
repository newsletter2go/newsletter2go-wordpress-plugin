<?php
    require_once dirname(__FILE__) . '/../../../../wp-config.php';
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        <!--[if lt IE 9]>
        <script src="<?php echo esc_url(get_template_directory_uri()); ?>/js/html5.js"></script>
        <![endif]-->
        <script>(function () {
                document.documentElement.className = 'js';
            })();</script>
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <div id="page" class="hfeed site">
            <div id="sidebar" class="sidebar-container">
                <div class="widget" style="padding-right: 20px;margin: 0;">
                    <?= stripslashes(urldecode($_GET['widget'])) ?>
                </div>
            </div><!-- #content -->
            <div style="clear:both;float:none;"></div>
        </div><!-- #page -->
    </body>
</html>
