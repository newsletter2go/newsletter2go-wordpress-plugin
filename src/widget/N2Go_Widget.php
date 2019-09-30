<?php

class N2Go_Widget extends WP_Widget
{

    public function __construct()
    {
        load_plugin_textdomain('newsletter2go');
        parent::__construct(
            'n2go_widget', 'Newsletter2Go Widget',
            array('description' => __('Display subscription form', NEWSLETTER2GO_TEXTDOMAIN))
        );

        wp_enqueue_style('n2go-styles', plugin_dir_url(__FILE__) . 'styles.css');
    }

    public function css()
    {
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
        require_once __DIR__ . '/../gui/N2Go_Gui.php';
        $gui = new N2Go_Gui();
        $forms = $gui->getForms();
        $formUniqueCode = get_option('n2go_formUniqueCode');
        if (!empty($forms[$formUniqueCode])) {
            $form = $forms[$formUniqueCode];
            ?>

            <p>
                <label
                        for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:',
                        'Newsletter2Go'); ?></label>
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
                                value="subscribe" <?= ($type == 'subscribe' ? 'selected="selected"' : '') ?>><?= __("Subscription-Form",
                                NEWSLETTER2GO_TEXTDOMAIN) ?></option>
                        <?php
                    }
                    if ($form['type_unsubscribe'] == 1) { ?>
                        <option
                                value="unsubscribe" <?= ($type == 'unsubscribe' ? 'selected="selected"' : '') ?>><?= __("Unsubscription-Form",
                                NEWSLETTER2GO_TEXTDOMAIN) ?></option>
                        <?php
                    }
                    ?>
                </select>

            </p>
        <?php } else { ?>
            <h3>kein gültiges Formular konfiguriert</h3>
            <?php
        }
    }

    /**
     * @param $args
     * @param $instance
     * @param bool $print
     * @return mixed
     */
    public function widget($args, $instance, $print = true)
    {
        $formTypeAvaliable = array();

        if ($instance && isset($instance['type'])) {
            $type = $instance['type'];
        } else {
            $type = 'subscribe';
        }

        $n2gConfig = stripslashes(get_option('n2go_widgetStyleConfig'));
        $formUniqueCode = get_option('n2go_formUniqueCode');

        $formTypeAvaliable['subscribe'] = get_option('n2go_typeSubscribe');
        $formTypeAvaliable['unsubscribe'] = get_option('n2go_typeUnsubscribe');

        $popup = false;

        $uniqueId = uniqid();

        if (isset($args['params'][0])) {
            $args['params'][0] == "'subscribe:createPopup'" ?: $args['params'][0] = "'" . $type . ":createForm'";
            $args['params'][0] == "'subscribe:createPopup'" ? $popup = true : '';
        } else {
            $args['params'][0] = "'" . $type . ":createForm'";
        }

        if (strlen(trim($n2gConfig)) > 0) {
            $args['params'][1] = $n2gConfig;
        } else {
            $args['params'][1] = 'null';
        }

        ksort($args['params']);

        $n2gParams = implode(', ', $args['params']);

        $response = require('widgetView.php');
        if (!empty($instance['title'])) {
            $response = "<h2 class=\"widget-title\">" . $instance['title'] . "</h2>" . $response;
        }
        $response = <<<HTML
            <section class="widget widget_newsletter2go">
                    $response
            </section>
HTML;

        if ($print) {
            echo $response;
        } else {
            return $response;
        }

        return null;
    }

}

function newsletter2goRegisterWidgets()
{
    register_widget('N2Go_Widget');
}

add_action('widgets_init', 'newsletter2goRegisterWidgets');
