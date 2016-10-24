<?php

class N2GoWidget extends WP_Widget
{

    public function __construct()
    {
        load_plugin_textdomain('newsletter2go');
        parent::__construct(
            'n2go_widget', 'Newsletter2Go Widget',
            array('description' => __('Display subscription form', NEWSLETTER2GO_TEXTDOMAIN))
        );

        //wp_enqueue_script('n2go-ajax-handle', plugin_dir_url(__FILE__) . 'ajax.js', array('jquery'));
        wp_enqueue_style('n2go-styles', plugin_dir_url(__FILE__) . 'styles.css');
//        wp_localize_script('n2go-ajax-handle', 'n2go_ajax_script', array('ajaxurl' => admin_url('admin-ajax.php')));
//        add_action('wp_ajax_n2go_subscribe', array(&$this, 'ajaxSubscribe'));
//        add_action('wp_ajax_nopriv_n2go_subscribe', array(&$this, 'ajaxSubscribe'));

//        if (is_active_widget(false, false, $this->id_base)) {
//            add_action('wp_head', array($this, 'css'));
//        }
    }

    public function css()
    {
//        $colors = get_option('n2go_colors');
//        $dir = dirname(__FILE__);
//        require_once "$dir/widgetStyles.php";
    }

    public function update($new_instance, $old_instance)
    {
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['type'] = strip_tags($new_instance['type']);
        return $instance;
    }

    public function form($instance)
    {
        if ($instance) {
            $title = $instance['title'];
            $type = $instance['type'];
        } else {
            $title = 'Newsletter2Go';
            $type = 'subscribe';
        }
        require_once __DIR__ . '/../gui/N2GoGui.php';
        $gui = new N2GoGui();
        $forms = $gui->getForms();
        $formUniqueCode = get_option('n2go_formUniqueCode');
        if (isset($forms[$formUniqueCode])) {
            $form = $forms[$formUniqueCode];
            ?>

            <p>
                <label
                    for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'Newsletter2Go'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
                       value="<?php echo esc_attr($title); ?>"/>
                <br/>
                <br/>
                <label
                    for="<?php echo $this->get_field_id('type'); ?>"><?= __("Formtype", NEWSLETTER2GO_TEXTDOMAIN) ?>
                    :</label>
                <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>"
                        name="<?php echo $this->get_field_name('type'); ?>"
                >

                    <?php if ($form['type_subscribe'] == 1) { ?>
                        <option
                            value="subscribe" <?= ($type == 'subscribe' ? 'selected="selected"' : '') ?>><?= __("Subscription-Form", NEWSLETTER2GO_TEXTDOMAIN) ?></option>
                        <?php
                    }
                    if ($form['type_unsubscribe'] == 1) { ?>
                    <option
                        value="unsubscribe" <?= ($type == 'unsubscribe' ? 'selected="selected"' : '') ?>><?= __("Unsubscription-Form", NEWSLETTER2GO_TEXTDOMAIN) ?></option>
                        <?php
                    }
                    ?>
                </select>

            </p>
        <?php } else { ?>
            <p>
            <h3>kein gültiges Formular konfiguriert</h3>

            </p>
            <?php
        }
    }

    public function widget($args, $instance, $print = true)
    {
        if ($instance && isset($instance['type'])) {
            $type = $instance['type'];
        } else {
            $type = 'subscribe';
        }

        $n2gConfig = stripslashes(get_option('n2go_widgetStyleConfig'));
        $formUniqueCode = get_option('n2go_formUniqueCode');

        $uniqueId = false;
        $form = false;
        //if (!isset($args['params'])) {
            $uniqueId = uniqid();
            $args['params'][0] = "'" . $type . ":createForm'";
        //}
        //var_dump($args['params']);
        if(strlen(trim($n2gConfig))>0) {
            $args['params'][1] = $n2gConfig;
        }else{
            $args['params'][1] = 'null';
        }
        ksort($args['params']);

        $n2gParams = implode(', ', $args['params']);

        $response = require('widgetView.php');
        if ($print) {
            echo $response;
        } else {
            return $response;
        }
    }

//    public function ajaxSubscribe()
//    {
//        $notFound = false;
//        $noValidEmail = false;
//        $attributes = get_option('n2go_attributes');
//        $texts = get_option('n2go_general');
//        $post = array();
//        foreach ($attributes as $k => $v) {
//            if (!empty($v['required']) && empty($_POST[$k])) {
//                $notFound = true;
//                break;
//            }
//            if ($k == 'email') {
//                if (!filter_var($_POST[$k], FILTER_VALIDATE_EMAIL)) {
//                    $noValidEmail = true;
//                }
//            }
//
//            $post[$k] = $_POST[$k];
//        }
//
//        if ($notFound) {
//            $result = array('success' => 0, 'message' => $texts['failureRequired']);
//            echo json_encode($result);
//            die;
//        }
//        if ($noValidEmail) {
//            $result = array('success' => 0, 'message' => $texts['failureEmail']);
//            echo json_encode($result);
//            die;
//        }
//
//        $post['key'] = get_option('n2go_apikey');
//        $post['doicode'] = get_option('n2go_doiCode');
//        $response = $this->execute('recipient', $post);
//        $result = array('success' => $response['success']);
//        if (!$response) {
//            $result['message'] = $texts['failureEmail'];
//        } else {
//            switch ($response['status']) {
//                case 200:
//                    $result['message'] = $texts['success'];
//                    break;
//                case 441:
//                    $result['message'] = $texts['failureSubsc'];
//                    break;
//                case 434:
//                case 429:
//                    $result['message'] = $texts['failureEmail'];
//                    break;
//                default:
//                    $result['message'] = $texts['failureError'];
//                    break;
//            }
//        }
//
//        echo json_encode($result);
//        die;
//    }

    private function execute($action, $post)
    {
        $response = wp_remote_post("https://www.newsletter2go.com/de/api/create/$action/", array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $post,
                'cookies' => array()
            )
        );

        if (is_wp_error($response)) {
            return array('success' => false, 'error' => $response->get_error_message(), 'curl' => true);

        } else {

            return json_decode($response['body'], true);
        }
    }

}

function newsletter2goRegisterWidgets()
{
    register_widget('N2GoWidget');
}

add_action('widgets_init', 'newsletter2goRegisterWidgets');
