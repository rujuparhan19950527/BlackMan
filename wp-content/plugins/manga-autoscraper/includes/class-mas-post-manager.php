<?php
/**
 * Post Manager class for Manga AutoScraper
 */

class MAS_Post_Manager {
    private $settings;

    public function __construct() {
        $this->settings = get_option('mas_settings');
    }

    public function create_post($title, $content, $category_id = null, $post_status = 'draft') {
        $post_data = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => $post_status,
            'post_type'     => 'manga',
        );

        if ($category_id) {
            $post_data['post_category'] = array($category_id);
        }

        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            error_log('Manga AutoScraper: Failed to create post - ' . $post_id->get_error_message());
            return false;
        }

        return $post_id;
    }

    public function attach_image($post_id, $image_url) {
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        if ($image_data === false) {
            error_log('Manga AutoScraper: Failed to download image from ' . $image_url);
            return false;
        }

        $filename = basename($image_url);
        $file = $upload_dir['path'] . '/' . $filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
        if (is_wp_error($attach_id)) {
            error_log('Manga AutoScraper: Failed to attach image - ' . $attach_id->get_error_message());
            return false;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }
} 