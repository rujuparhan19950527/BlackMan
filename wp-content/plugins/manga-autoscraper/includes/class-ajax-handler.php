<?php
/**
 * AJAX Handler for Manga AutoScraper
 *
 * @package MangaAutoScraper
 */

if (!defined('ABSPATH')) {
    exit;
}

class MangaAutoScraper_AjaxHandler {
    /**
     * Initialize the AJAX handler
     */
    public function __construct() {
        add_action('wp_ajax_manga_autoscraper_run', array($this, 'handleManualRun'));
    }

    /**
     * Handle manual scraper run request
     */
    public function handleManualRun() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'manga_autoscraper_run')) {
            wp_send_json_error('Invalid nonce');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        try {
            // Get scraper instance
            $scraper = new MangaScraper();
            
            // Run scraper
            $scraper->run();
            
            wp_send_json_success('Scraper started successfully');
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
} 