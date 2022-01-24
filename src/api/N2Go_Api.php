<?php

require_once dirname(__FILE__) . '/Nl2go_ResponseHelper.php';

class N2Go_Api
{

    public static function run()
    {
        $apikey = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] :  null;

        if (empty($apikey) === true) {
            $apikey = filter_input(INPUT_POST, 'apikey');
        }

        if (strlen($apikey) == 0) {
            $result = Nl2go_ResponseHelper::generateErrorResponse('api-key is missing', Nl2go_ResponseHelper::ERRNO_PLUGIN_CREDENTIALS_MISSING);
            echo $result;
            exit;
        }

        if (get_option('n2go_apikey') === $apikey) {
            $method = get_query_var('method');
            switch ($method) {
                case 'getPost':
                    $id = get_query_var('postId');
                    $post = self::getPost((int)$id);
                    if ($post === null) {
                        $result = Nl2go_ResponseHelper::generateErrorResponse('no post found', Nl2go_ResponseHelper::ERRNO_PLUGIN_OTHER);
                    } else {
                        $result = Nl2go_ResponseHelper::generateSuccessResponse(array('post' => $post));
                    }
                    break;
                case 'test':
                    $result = Nl2go_ResponseHelper::generateSuccessResponse();
                    //$result = array('success' => true, 'message' => 'API Connected!');
                    break;
                case 'getVersion':
                    if( !function_exists('get_plugin_data') ){
                        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    }
                    $pluginInfo = get_plugin_data(dirname(__DIR__) . '/newsletter2go.php');
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

        if (!headers_sent()) {
            header('HTTP/1.1 200 OK', true);
            header("Content-Type: application/json; charset=utf-8");
        }

        echo $result;
        exit;
    }

    private static function getPost($id)
    {
        global $wpdb;

        /** @var WP_Post $post */
        $post = $wpdb->get_row(
            $wpdb->prepare("
                SELECT 
                    p.ID,
                    p.post_content,
                    p.post_excerpt,
                    p.post_title, 
                    p.post_date,
                    u.display_name as post_author 
                FROM $wpdb->posts p 
                    LEFT JOIN $wpdb->users u ON p.post_author = u.ID 
                WHERE p.ID = %d
              ", $id)
        );
        $result = null;

        if ($post) {
            $basUrl = esc_url(home_url('/'));
            $content = apply_filters('the_content', $post->post_content);

            $result = array(
                'id' => $post->ID,
                'url' => $basUrl,
                'shortDescription' => $post->post_excerpt,
                'description' => $content,
                'title' => $post->post_title,
                'author' => $post->post_author,
                'date' => $post->post_date,
                'category' => array(),
                'tags' => array(),
                'images' => array_unique(array_merge(
                    self::extractImages($content),
                    self::getAttachedImages($post->ID),
                    self::getThumbnailImages($post->ID)
                )),
                'link' => substr(get_permalink($post->ID), strlen($basUrl)),
            );

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
                    if ($term->type === 'category') {
                        $result['category'][] = $term->name;
                    } else if ($term->type === 'post_tag') {
                        $result['tags'][] = $term->name;
                    }
                }
            }

            $meta = get_post_meta($post->ID);
            if ($meta !== false) {
                foreach ($meta as $key => $value) {
                    if (substr($key, 0, 1) !== '_') {
                        $result[$key] = current($value);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $html
     * @return string[]
     */
    private static function extractImages($html)
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        if (!$document->loadHTML($html)) {
            return [];
        }

        $xpath = new DOMXPath($document);
        if (!($list = $xpath->query('//img/@src'))) {
            return [];
        }

        libxml_clear_errors();

        return array_map(
            function (DOMNode $node) {
                return $node->nodeValue;
            },
            iterator_to_array($list)
        );
    }

    /**
     * @param int $postId
     * @return string[]
     */
    private static function getAttachedImages($postId)
    {
        return array_map(
            function($image){
                return wp_get_attachment_url($image->ID);
            },
            get_attached_media('image', $postId)
        );
    }

    /**
     * @param int $postId
     * @return string[]
     */
    private static function getThumbnailImages($postId)
    {
        $thumbnailTypes = [
            'thumbnail',
            'medium',
            'medium_large',
            'full'
        ];

        foreach ($thumbnailTypes as $type){
            $thumbnails[] = get_the_post_thumbnail_url( $postId, $type);
        }

        return $thumbnails;
    }
}
