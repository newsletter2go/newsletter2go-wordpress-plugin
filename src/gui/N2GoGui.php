<?php

class N2GoGui
{

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
        wp_register_style('farbtastic_css', plugins_url('/lib/farbtastic.css', __FILE__));
        wp_register_style('nl2g_admin_css', plugins_url('/lib/nl2g_admin_css.css', __FILE__));

        wp_enqueue_script('farbtastic_js', plugins_url('/lib/farbtastic.js', __FILE__));
        wp_enqueue_script('newsletter2go_default', plugins_url('/lib/newsletter2go_default.js', __FILE__));
        wp_enqueue_script('newsletter2go', plugins_url('/lib/newsletter2go.js', __FILE__));
        wp_enqueue_style('farbtastic_css');
        wp_enqueue_style('nl2g_admin_css');
    }

    /**
     * Adds menu page.
     */
    public function adminMenu()
    {
        add_menu_page('Newsletter2Go API Settings', 'Newsletter2Go', 'manage_options', 'n2go-api',
            array(&$this, 'adminOptions'), 'https://www.newsletter2go.de/pr/150204_wordpress_icon.png', 30);
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->save_option('n2go_apikey', $_POST['apiKey']);
            $this->save_option('n2go_formUniqueCode', $_POST['formUniqueCode']);
            $widgetStyleConfig = $_POST['widgetStyleConfig'];
            $this->save_option('n2go_widgetStyleConfig', $widgetStyleConfig);

        }

        $curl_error = null;
        $apiKey = get_option('n2go_apikey');
        $formUniqueCode = get_option('n2go_formUniqueCode');
        $nl2gStylesConfigObject = stripslashes(get_option('n2go_widgetStyleConfig'));
        $response = $this->execute('attributes',array('key' => $apiKey));

        if (!strlen($formUniqueCode) > 0) {
            $errorMessage = "Please, enter the form unique code!";
        }

        require_once dirname(__FILE__) . '/adminView.php';
    }

    /**
     * Executes api call to newsletter2go server.
     *
     * @param string $action
     * @param string $post
     * @return array|mixed|object
     */
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

}
