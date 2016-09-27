<?php

class N2GoGui
{

    const N2GO_INTEGRATION_URL = 'https://ui.newsletter2go.com/integrations/connect/WP/';
    const N2GO_API_URL = 'https://api.newsletter2go.com/';
    const N2GO_REFRESH_GRANT_TYPE = 'https://nl2go.com/jwt_refresh';

    /**
     * Register actions.
     *
     */
    public static function run()
    {
        $obj = new N2GoGui();
        add_action('admin_menu', array($obj, 'adminMenu'));
        add_action('admin_enqueue_scripts', array($obj, 'myScripts'));
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
        add_menu_page('Newsletter2Go API Settings', 'Newsletter2Go', 'manage_options', 'n2go-api',
            array(&$this, 'adminOptions'), plugins_url('/lib/wordpress_icon.png', __FILE__), 30);
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
            if ($_GET['task'] == 'resetApiKey') {
                $this->restApiKey();
                wp_redirect(admin_url('admin.php?page=n2go-api'));
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->save_option('n2go_formUniqueCode', $_POST['formUniqueCode']);
            $widgetStyleConfig = $_POST['widgetStyleConfig'];
            $this->save_option('n2go_widgetStyleConfig', $widgetStyleConfig);
            if(isset($_POST['resetValues'])){
                $this->disconnect($_POST['resetValues']);
            }

        }

        $curl_error = null;
        $apiKey = get_option('n2go_apikey');
        $authKey = get_option('n2go_authKey');

        $pluginInfo = get_plugin_data(WP_PLUGIN_DIR . '/newsletter2go/newsletter2go.php');
        $queryParams['version'] = str_replace('.', '', $pluginInfo['Version']);

        $queryParams['apiKey'] = $apiKey;

        $locale = get_locale();
        $queryParams['language'] = current(explode("_", $locale));

        $queryParams['url'] = get_site_url();
        $queryParams['callback'] = $queryParams['url'] . '/index.php?pagename=n2go-callback';

        $connectUrl = self::N2GO_INTEGRATION_URL . '?' . http_build_query($queryParams);

        $forms = $this->getForms($authKey);

        $formUniqueCode = get_option('n2go_formUniqueCode');
        $nl2gStylesConfigObject = stripslashes(get_option('n2go_widgetStyleConfig'));

        if (!strlen($formUniqueCode) > 0) {
            $errorMessage = "Please connect to Newsletter2Go by clicking on \"Login or Create Account\" button";
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
        $apiKey = $this->generateRandomString();
        $this->save_option('n2go_apikey', $apiKey);
    }

    /**
     * Generates random string with $length characters
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Get forms
     * @param string $authKey
     * @return array
     */
    public function getForms($authKey = '')
    {
        $result = false;

        if (strlen($authKey) > 0) {
            $form = $this->executeNewApi('forms/all?_expand=1');
            if (isset($form['status']) && $form['status'] >= 200 && $form['status'] < 300) {
                $result = array();
                foreach ($form['value'] as $key => $value) {
                    $result[$key]['name'] = $value['name'];
                    $result[$key]['hash'] = $value['hash'];
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
        $this->refreshTokens();
        $access_token = get_option('n2go_accessToken');

        $response = wp_remote_get(self::N2GO_API_URL . $action, array(
                'method' => 'GET',
                'timeout' => 45,
                'headers' => array('Authorization' => 'Bearer ' . $access_token)
            )
        );

        return json_decode($response['body'], true);
    }

    /**
     * Creates request and returns response, refresh access token
     *
     * @return void
     * @internal param mixed $params
     */
    private function refreshTokens()
    {
        $url = self::N2GO_API_URL . 'oauth/v2/token';
        $auth = base64_encode(get_option('n2go_authKey'));
        $header = array(
            'Authorization' => 'Basic ' . $auth,
            'Content-Type' => 'application/x-www-form-urlencoded',
        );
        $refreshPost = array(
            'refresh_token' => get_option('n2go_refreshToken'),
            'grant_type' => self::N2GO_REFRESH_GRANT_TYPE
        );

        $responseRaw = wp_remote_post($url, array(
                'method' => 'POST',
                'timeout' => 45,
                'headers' => $header,
                'sslverify' => false,
                'body' => $refreshPost,
            )
        );

        $response = json_decode($responseRaw['body']);
        if (isset($response->access_token) && !empty($response->access_token)) {
            $this->save_option('n2go_accessToken', $response->access_token);
        }

        if (isset($response->refresh_token) && !empty($response->refresh_token)) {
            $this->save_option('n2go_refreshToken', $response->refresh_token);
        }
    }

    /**
     * Reset the values that are set when callback is made
     *
     * @param string $data
     */
    private function disconnect($data){

        if($data == 'Disconnect'){

            $this->save_option('n2go_authKey', null);
            $this->save_option('n2go_accessToken', null);
            $this->save_option('n2go_refreshToken', null);
            $this->save_option('n2go_formUniqueCode', null);
            $this->save_option('n2go_widgetStyleConfig', null);

        }
    }


}
