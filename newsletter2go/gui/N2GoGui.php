<?php

class N2GoGui
{

    private $fields = array(
        'email' => array('title' => 'E-mail address'),
        'firstname' => array('title' => 'First name'),
        'lastname' => array('title' => 'Last name'),
        'gender' => array('title' => 'Gender'),
    );

    public static function run()
    {
        $obj = new N2GoGui();
        add_action('admin_menu', array($obj, 'adminMenu'));
        add_action('admin_enqueue_scripts', array($obj, 'myScripts'));
    }

    public function myScripts($hook)
    {
        if ($hook != 'toplevel_page_n2go-api') {
            return;
        }

        wp_register_style('farbtastic_css', plugins_url('/lib/farbtastic.css', __FILE__));
        wp_enqueue_script('farbtastic_js', plugins_url('/lib/farbtastic.js', __FILE__));
        wp_enqueue_style('farbtastic_css');
    }

    public function adminMenu()
    {
        add_menu_page('Newsletter2Go API Settings', 'Newsletter2Go', 'manage_options', 'n2go-api',
            array(&$this, 'adminOptions'), 'https://www.newsletter2go.de/pr/150204_wordpress_icon.png', 30);
    }

    public function adminOptions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (function_exists('curl_version') === false) {
            wp_die(__('You don\'t have cURL enabled. Please enable it for proper plugin functioning.'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->save_option('n2go_apikey', $_POST['apiKey']);
            $this->save_option('n2go_doiCode', $_POST['doiCode']);
            $checks = $_POST['attributes'];
            $attributes = array('email' => array(
                'sort' => $_POST['emailSort'],
                'required' => 'required',
            ));
            for ($i = 0; $i < count($checks); $i++) {
                $attributes[$checks[$i]] = array(
                    'sort' => $_POST[$checks[$i] . 'Sort'],
                    'required' => $_POST[$checks[$i] . 'Required'],
                );
            }

            $this->save_option('n2go_attributes', $attributes);

            $general = array();
            $general['success'] = $_POST['success'];
            $general['failureSubsc'] = $_POST['failureSubsc'];
            $general['failureEmail'] = $_POST['failureEmail'];
            $general['failureRequired'] = $_POST['failureRequired'];
            $general['failureError'] = $_POST['failureError'];
            $general['buttonText'] = $_POST['buttonText'];
            $this->save_option('n2go_general', $general);

            $colors = array();
            $colors['textColor'] = $_POST['textColor'];
            $colors['borderColor'] = $_POST['borderColor'];
            $colors['backgroundColor'] = $_POST['backgroundColor'];
            $colors['btnTextColor'] = $_POST['btnTextColor'];
            $colors['btnBackgroundColor'] = $_POST['btnBackgroundColor'];
            $this->save_option('n2go_colors', $colors);

            $widget = $_POST['widgetSourceCode'];
            $this->save_option('n2go_widgetSource', $widget);
        }
        $curl_error = null;
        $apiKey = get_option('n2go_apikey');
        $doiCode = get_option('n2go_doiCode');
        $attributesSelected = get_option('n2go_attributes');
        $texts = get_option('n2go_general');
        $colors = get_option('n2go_colors');
        $widget = stripslashes(get_option('n2go_widgetSource'));
        //calling API
        $attributesApi = $this->execute('attributes', array('key' => $apiKey));
        $attributes = array();
        if ($attributesApi['success']) {
            $allAttri = 4 + count($attributesApi['value']);
            foreach ($attributesApi['value'] as $atr) {
                $tmpId = strtolower(str_replace(' ', '', $atr));
                $attributes[] = array(
                    'id' => $tmpId,
                    'checked' => isset($attributesSelected[$tmpId]) ? 'checked' : '',
                    'sort' => $attributesSelected[$tmpId] ? $attributesSelected[$tmpId]['sort'] : $allAttri,
                    'title' => $atr,
                    'required' => $attributesSelected[$tmpId] ? $attributesSelected[$tmpId]['required'] : '',
                );
            }
        } else {
            if ($attributesApi['curl']) {
                $curl_error = $attributesApi['error'];
            }
        }

        if (strlen($doiCode) > 0) {

            $doi = $this->execute('form', array('key' => $apiKey, 'doicode' => $doiCode));
            if ($doi['success']) {
                $code = rawurldecode($doi['value']['code']);
                if (strpos($code, '"' . $doiCode . '"') !== false) {
                    $attributesApi['doi-success'] = true;
                    $attributesApi['doi-name'] = $doi['value']['host'];

                } else {
                    $attributesApi['doi-success'] = false;
                }


            } else {
                $attributesApi['doi-success'] = false;
            }
        }


        $attributes[] = array(
            'id' => 'email',
            'checked' => 'checked',
            'sort' => $attributesSelected['email'] ? $attributesSelected['email']['sort'] : $allAttri,
            'title' => 'E-mail address',
            'disabled' => 'disabled="true"',
            'required' => 'required',
        );
        $attributes[] = array(
            'id' => 'firstname',
            'checked' => isset($attributesSelected['firstname']) ? 'checked' : '',
            'sort' => $attributesSelected['firstname'] ? $attributesSelected['firstname']['sort'] : $allAttri,
            'title' => 'First name',
            'required' => $attributesSelected['firstname'] ? $attributesSelected['firstname']['required'] : '',
        );
        $attributes[] = array(
            'id' => 'lastname',
            'checked' => isset($attributesSelected['lastname']) ? 'checked' : '',
            'sort' => $attributesSelected['lastname'] ? $attributesSelected['lastname']['sort'] : $allAttri,
            'title' => 'Last name',
            'required' => $attributesSelected['lastname'] ? $attributesSelected['lastname']['required'] : '',
        );
        $attributes[] = array(
            'id' => 'gender',
            'checked' => isset($attributesSelected['gender']) ? 'checked' : '',
            'sort' => $attributesSelected['gender'] ? $attributesSelected['gender']['sort'] : $allAttri,
            'title' => 'Gender',
            'required' => $attributesSelected['gender'] ? $attributesSelected['gender']['required'] : '',
        );

        usort($attributes, array('N2GoGui', 'attributestCmp'));

        $previewUrl = site_url() . '/wp-content/plugins/newsletter2go/gui/preview.php?widget=';
        require_once dirname(__FILE__) . '/adminView.php';
    }

    public static function attributestCmp($a, $b)
    {
        if ($a['sort'] == $b['sort']) {
            return 0;
        }

        return $a['sort'] < $b['sort'] ? -1 : 1;
    }


    private function execute($action, $post)
    {


        $response = wp_remote_post("https://www.newsletter2go.com/en/api/get/$action/", array(
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


    private function save_option($id, $value)
    {
        (get_option($id, null) !== null) ? update_option($id, $value) : add_option($id, $value);
    }

}
