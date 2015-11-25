<?php

require_once dirname(__FILE__).'/Nl2go_ResponseHelper.php';
require_once ABSPATH . '/wp-admin/includes/plugin.php';

class N2GoApi
{

    public static function run()
    {
		$apikey = $_SERVER['PHP_AUTH_USER'];

		if (empty($apikey) === true) {
			$apikey = filter_input(INPUT_POST, 'apikey');
		}

        if(strlen($apikey) == 0){
            $result = Nl2go_ResponseHelper::generateErrorResponse('api-key is missing', Nl2go_ResponseHelper::ERRNO_PLUGIN_CREDENTIALS_MISSING);
            echo $result;
            exit;
        }

        if (get_option('n2go_apikey') === $apikey) {
            $method = get_query_var('method');
            switch ($method) {
                case 'getPost':
                    $id = get_query_var('postId');
                    $post = self::getPost($id);
                    if($post === null){
                        $result = Nl2go_ResponseHelper::generateErrorResponse('no post found', Nl2go_ResponseHelper::ERRNO_PLUGIN_OTHER);
                    }else{
                        $result = Nl2go_ResponseHelper::generateSuccessResponse(array('post' => $post));
                    }
                    break;
                case 'test':
                    $result = Nl2go_ResponseHelper::generateSuccessResponse();
                    //$result = array('success' => true, 'message' => 'API Connected!');
                    break;
                case 'getVersion':
                    $pluginInfo = get_plugin_data(WP_PLUGIN_DIR . '/newsletter2go/newsletter2go.php');
                    $result = Nl2go_ResponseHelper::generateSuccessResponse(array('version' => str_replace('.', '', $pluginInfo['Version'])));
                    //$result = array('success' => true, 'message' => 'OK', 'version' => get_option('n2go_plugin_version'));
                    break;
                default:
                    $result = Nl2go_ResponseHelper::generateErrorResponse('Invalid method call', Nl2go_ResponseHelper::ERRNO_PLUGIN_OTHER);

                    break;
            }
        } else {
            $result = Nl2go_ResponseHelper::generateErrorResponse('API Key is invalid', Nl2go_ResponseHelper::ERRNO_PLUGIN_CREDENTIALS_WRONG);
        }

        $charset = get_option('blog_charset');
        if (!headers_sent()) {
            header('HTTP/1.1 200 OK', true);
            header("Content-Type: application/json; charset=$charset", true);
        }

        echo $result;
        exit;
    }

    private static function getPost($id)
    {
        global $wpdb;
        $result = array();
        $post = $wpdb->get_row(
            $wpdb->prepare("
                SELECT 
                    p.ID as ID, 
                    p.guid as guid, 
                    p.post_content as description,
                    p.post_excerpt as shortDescription,
                    p.post_title as title, 
                    p.post_date as date,
                    u.display_name as author 
                FROM $wpdb->posts p 
                    LEFT JOIN $wpdb->users u ON p.post_author = u.ID 
                WHERE p.ID = %d AND p.post_parent = 0 AND post_type = 'post'
              ", $id)
        );

        if ($post) {
            $result= array(
                'id' => $post->ID,
                'url' => esc_url(home_url('/')),
                'shortDescription' => $post->shortDescription,
                'description' => $post->description,
                'title' => $post->title,
                'author' => $post->author,
                'date' => $post->date,
                'category' => array(),
                'tags' => array(),
                'images' => array(),
            );
            $result['link'] = substr($post->guid, strlen($result['url']));

            //images
            $images = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_mime_type LIKE 'image%' AND post_parent = $id;");
            if ($images) {
                foreach ($images as $image) {
                    $result['images'][] = $image->guid;
                }
            }

            //terms
            $terms = $wpdb->get_results($wpdb->prepare("
                    SELECT ts.name as name, tx.taxonomy as type  
                    FROM $wpdb->term_relationships rs 
                        LEFT JOIN $wpdb->term_taxonomy tx ON tx.term_taxonomy_id = rs.term_taxonomy_id 
                        LEFT JOIN $wpdb->terms ts ON tx.term_id = ts.term_id 
                    WHERE rs.object_id = %d"
                    , $id));
            
            if ($terms) {
                foreach ($terms as $term) {
                    if ($term->type == 'category') {
                        $result['category'][] = $term->name;
                    } else if ($term->type == 'post_tag') {
                        $result['tags'][] = $term->name;
                    }
                }
            }

            $meta = get_post_meta($post->ID);
            if($meta !== false){
                foreach($meta as $key => $value){
                    if(substr($key,0, 1) !== '_'){
                        $result[$key] = current($value);
                    }
                }
            }

            //$result['success'] = true;
        } else {
           // $result['success'] = false;
            //$result['message'] = "Post with ID = $id NOT FOUND!";
            $result = null;
        }

        return $result;
    }
}
