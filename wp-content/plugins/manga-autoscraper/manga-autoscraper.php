<?php
/**
 * Plugin Name: Manga AutoScraper
 * Description: A full-featured manga scraper for WordPress with multi-source support, scheduling, and advanced post management.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: manga-autoscraper
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MAS_VERSION', '1.0.0');
define('MAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MAS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once MAS_PLUGIN_DIR . 'includes/class-mas-admin.php';
require_once MAS_PLUGIN_DIR . 'includes/class-mas-scraper.php';
require_once MAS_PLUGIN_DIR . 'includes/class-mas-scheduler.php';
require_once MAS_PLUGIN_DIR . 'includes/class-mas-post-manager.php';
require_once MAS_PLUGIN_DIR . 'includes/class-mas-logger.php';

// Initialize the plugin
function mas_init() {
    // Initialize admin
    new MAS_Admin();
    // Initialize scraper
    new MAS_Scraper();
    // Initialize scheduler
    new MAS_Scheduler();
    // Initialize post manager
    new MAS_Post_Manager();
    // Initialize logger
    new MAS_Logger();
}
add_action('plugins_loaded', 'mas_init');

// Activation hook
register_activation_hook(__FILE__, 'mas_activate');
function mas_activate() {
    // Create necessary database tables or options
    add_option('mas_version', MAS_VERSION);
    // Schedule default cron job
    if (!wp_next_scheduled('mas_scheduled_scrape')) {
        wp_schedule_event(time(), 'hourly', 'mas_scheduled_scrape');
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'mas_deactivate');
function mas_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('mas_scheduled_scrape');
}

// Uninstall hook
register_uninstall_hook(__FILE__, 'mas_uninstall');
function mas_uninstall() {
    // Clean up options and data
    delete_option('mas_version');
    delete_option('mas_settings');
}
