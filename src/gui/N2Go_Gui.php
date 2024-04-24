<?php

class N2Go_Gui
{

    const N2GO_INTEGRATION_URL = 'https://ui.newsletter2go.com/integrations/connect/WP/';
    const N2GO_API_URL = 'https://api.newsletter2go.com/';
    const N2GO_STATIC_URL = 'https://static.newsletter2go.com/';
    const N2GO_REFRESH_GRANT_TYPE = 'https://nl2go.com/jwt_refresh';
    const N2GO_API_FORMS = 'forms?_expand=1';

    private $apiErrorMessage;

    /**
     * Register actions.
     *
     */
    public static function run()
    {
        $obj = new N2Go_Gui();
        add_action('admin_menu', array($obj, 'adminMenu'));
        add_action('admin_enqueue_scripts', array($obj, 'myScripts'));
        add_action('wp_ajax_resetStyles', array($obj, 'resetStyles'));
    }

    /**
     * Register all scripts for our plugin.
     *
     * @param string $hook
     *
     */
    public function myScripts($hook)
    {
        if ($hook != 'toplevel_page_n2go-api') {
            return;
        }
        wp_register_style('font-awesome.css', plugins_url('/lib/font-awesome.css', __FILE__));
        wp_register_style('nl2g_admin_css', plugins_url('/lib/nl2g_admin_css.css', __FILE__));

        wp_enqueue_script('jscolor_min', plugins_url('/lib/jscolor.min.js', __FILE__));
        wp_enqueue_script('newsletter2go_default', plugins_url('/lib/newsletter2go_default.js', __FILE__));
        wp_enqueue_script('newsletter2go', plugins_url('/lib/newsletter2go.js', __FILE__));
        wp_enqueue_style('font-awesome.css');
        wp_enqueue_style('nl2g_admin_css');
    }

    /**
     * Adds menu page.
     */
    public function adminMenu()
    {
        add_menu_page(
            'Newsletter2Go API Settings',
            'Newsletter2Go',
            'manage_options',
            'n2go-api',
            array(&$this, 'adminOptions'),
            plugins_url('/lib/wordpress_icon.png', __FILE__),
            30
        );
    }

    /**
     * Checks if preconditions are fulfilled(permissions and having curl);
     * Checks if admin side is request with POST or with GET.Handles admin options;
     *
     * Requires "view"(adminView.php);
     *
     */
    public function adminOptions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (function_exists('curl_version') === false) {
            wp_die(__('You don\'t have cURL enabled. Please enable it for proper plugin functioning.'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($_GET['task']) && $_GET['task'] == 'resetApiKey') {
                $this->restApiKey();
                wp_redirect(admin_url('admin.php?page=n2go-api'));
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->save_option('n2go_formUniqueCode', htmlspecialchars($_POST['formUniqueCode']));

            if (isset($_POST['widgetStyleConfig'])) {
                $this->save_option('n2go_widgetStyleConfig', $_POST['widgetStyleConfig']);
            }

            if (isset($_POST['resetValues'])) {
                $this->disconnect();
            }
        }

        $curl_error = null;
        $apiKey = get_option('n2go_apikey');
        $authKey = get_option('n2go_authKey');

        $pluginInfo = get_plugin_data(dirname(__DIR__) . '/newsletter2go.php');
        $queryParams['version'] = str_replace('.', '', $pluginInfo['Version']);

        $queryParams['apiKey'] = $apiKey;

        $locale = get_locale();
        $queryParams['language'] = current(explode("_", $locale));

        $queryParams['url'] = get_site_url();
        $queryParams['callback'] = $queryParams['url'] . '/index.php?pagename=n2go-callback';

        $connectUrl = self::N2GO_INTEGRATION_URL . '?' . http_build_query($queryParams);

        $forms = $this->getForms();

        if (empty($forms)) {
            $errorMessage = "Please connect to Newsletter2Go by clicking on \"Login or Create Account\" button";
        }

        $formUniqueCode = get_option('n2go_formUniqueCode');

        ($_SERVER['REQUEST_METHOD'] !== 'POST' || (!isset($formUniqueCode)) || !is_array(
                $forms
            ) || $formUniqueCode == '') ?: $this->saveFormType(
            $forms,
            $formUniqueCode
        );

        $nl2gStylesConfigObject = stripslashes(get_option('n2go_widgetStyleConfig'));

        //delete selected form, if form doesn't exist anymore

        if (strlen($formUniqueCode) > 0 && !isset($forms[$formUniqueCode])) {
            $this->save_option('n2go_formUniqueCode', null);
            $formUniqueCode = null;
        }

        require_once dirname(__FILE__) . '/adminView.php';

    }

    /**
     * Considers if options should be updated or created.
     *
     * @param int $id
     * @param string $value
     *
     * @return void
     */
    private function save_option($id, $value)
    {
        (get_option($id, null) !== null) ? update_option($id, $value) : add_option($id, $value);
    }

    public function restApiKey()
    {
        $apiKey = wp_generate_password(40, false);
        $this->save_option('n2go_apikey', $apiKey);
    }

    /**
     * Get forms
     * @param string $authKey
     * @return array|bool
     */
    public function getForms()
    {
        $authKey = get_option('n2go_authKey');

        $result = false;

        if (strlen($authKey) > 0) {
            $form = $this->executeNewApi(self::N2GO_API_FORMS);
            if (isset($form['status']) && $form['status'] >= 200 && $form['status'] < 300) {
                $result = array();
                foreach ($form['value'] as $value) {
                    $key = $value['hash'];
                    $result[$key]['name'] = $value['name'];
                    $result[$key]['hash'] = $value['hash'];
                    $result[$key]['type_subscribe'] = $value['type_subscribe'];
                    $result[$key]['type_unsubscribe'] = $value['type_unsubscribe'];
                }
            }
        }

        return $result;
    }

    /**
     * Creates request and returns response. New API
     *
     * @param string $action
     * @return array
     * @internal param mixed $params
     */
    private function executeNewApi($action)
    {
        $access_token = get_option('n2go_accessToken');

        $response = wp_remote_get(
            self::N2GO_API_URL . $action,
            array(
                'method' => 'GET',
                'timeout' => 45,
                'headers' => array('Authorization' => 'Bearer ' . $access_token),
            )
        );

        if($this->verifyResponse($response)){
            return json_decode($response['body'], true);
        } elseif ($this->refreshTokens()){

            $access_token = get_option('n2go_accessToken');

            $response = wp_remote_get(
                self::N2GO_API_URL . $action,
                array(
                    'method' => 'GET',
                    'timeout' => 45,
                    'headers' => array('Authorization' => 'Bearer ' . $access_token),
                )
            );

            return json_decode($response['body'], true);

        } else {
            $this->disconnect();
            return null;
        }
    }

    /**
     * Creates request and returns response, refresh access token
     *
     * @return array|boolean
     * @internal param mixed $params
     */
    private function refreshTokens()
    {
        $url = self::N2GO_API_URL . 'oauth/v2/token';
        $auth = base64_encode(get_option('n2go_authKey'));
        $header = array(
            'Authorization' => 'Basic ' . $auth . '',
            'Content-Type' => 'application/x-www-form-urlencoded',
        );
        $refreshPost = array(
            'refresh_token' => get_option('n2go_refreshToken'),
            'grant_type' => self::N2GO_REFRESH_GRANT_TYPE,
        );

        $response = wp_remote_post(
            $url,
            array(
                'method' => 'POST',
                'timeout' => 45,
                'headers' => $header,
                'sslverify' => false,
                'body' => $refreshPost,
            )
        );

        if($this->verifyResponse($response)) {
            $response = json_decode($response['body']);

            if (isset($response->access_token) && isset($response->refresh_token)) {
                $this->save_option('n2go_accessToken', $response->access_token);
                $this->save_option('n2go_refreshToken', $response->refresh_token);

                return true;
            }
        }

        return false;
    }

    /**
     * Reset the values that are set when callback is made
     */
    private function disconnect()
    {
        $this->save_option('n2go_authKey', null);
        $this->save_option('n2go_accessToken', null);
        $this->save_option('n2go_refreshToken', null);
        $this->save_option('n2go_formUniqueCode', null);
    }


    /**
     * This function sets widgetStyleConfig to default value
     */
    function resetStyles()
    {
        $style = file_get_contents(plugins_url('/lib/newsletter2go_default.json', __FILE__));
        $this->save_option('n2go_widgetStyleConfig', $style);
        echo true;
        wp_die();
    }

    /**
     * This method saves form types in database.
     *
     * @param $forms
     * @param $formUniqueCode
     */
    private function saveFormType($forms, $formUniqueCode)
    {
        foreach ($forms as $form) {
            if ($form['hash'] == $formUniqueCode) {
                $subscribe = $form['type_subscribe'] !== false ? $subscribe = 1 : $subscribe = 0;
                $unsubscribe = $form['type_unsubscribe'] !== false ? $unsubscribe = 1 : $unsubscribe = 0;
                $this->save_option('n2go_typeSubscribe', $subscribe);
                $this->save_option('n2go_typeUnsubscribe', $unsubscribe);
            }
        }
    }

    private function verifyResponse($response)
    {
        if (is_wp_error($response)) {
            $this->apiErrorMessage = $response->get_error_message();
            return false;
        }

        if (empty($response['response']['code'])) {
            return false;
        }

        switch($response['response']['code']){
            case 200:
                return true;
            case 400:
                $this->disconnect();
                break;
            case 401:
            case 403:
               return false;
        }
    }
}
