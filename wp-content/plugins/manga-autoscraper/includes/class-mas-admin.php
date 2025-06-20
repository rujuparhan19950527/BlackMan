<?php
/**
 * Admin settings class for Manga AutoScraper
 */
class MAS_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_mas_update_sources', function() {
            // Check nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mas_admin_nonce')) {
                wp_send_json_error('Invalid nonce.');
            }
            // Check permissions
            if (!current_user_can('manage_options')) {
                wp_send_json_error('Unauthorized.');
            }

            $sources = get_option('mas_sources', array());
            $source = sanitize_text_field($_POST['source']);
            $enabled = filter_var($_POST['enabled'], FILTER_VALIDATE_BOOLEAN);

            if ($enabled) {
                $sources[$source] = 1;
            } else {
                unset($sources[$source]);
            }

            if (update_option('mas_sources', $sources) !== false) {
                wp_send_json_success('Source updated successfully.');
            } else {
                wp_send_json_error('Failed to update source.');
            }
        });
        // Add AJAX handler for manual scraping
        add_action('wp_ajax_mas_scrape_now', function() {
            // Check nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mas_admin_nonce')) {
                wp_send_json_error('Invalid nonce.');
            }
            // Check permissions
            if (!current_user_can('manage_options')) {
                wp_send_json_error('Unauthorized.');
            }

            try {
                $scraper = new MAS_Scraper();
                $scraper->run_scheduled_scrape();
                wp_send_json_success('Scraping completed successfully.');
            } catch (Exception $e) {
                wp_send_json_error('Scraping failed: ' . $e->getMessage());
            }
        });
        // Add AJAX handler for updating schedule interval
        add_action('wp_ajax_mas_update_schedule', function() {
            // Check nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mas_admin_nonce')) {
                wp_send_json_error('Invalid nonce.');
            }
            // Check permissions
            if (!current_user_can('manage_options')) {
                wp_send_json_error('Unauthorized.');
            }
            // Validate interval
            $valid_intervals = array('hourly', 'twicedaily', 'daily');
            $interval = isset($_POST['interval']) ? sanitize_text_field($_POST['interval']) : '';
            if (!in_array($interval, $valid_intervals, true)) {
                wp_send_json_error('Invalid interval.');
            }
            // Update option
            if (!update_option('mas_schedule_interval', $interval)) {
                wp_send_json_error('Failed to update schedule option.');
            }
            // Reschedule cron
            if (class_exists('MAS_Scheduler')) {
                $scheduler = new MAS_Scheduler();
                $scheduler->update_schedule($interval);
            }
            wp_send_json_success('Schedule updated successfully.');
        });
    }

    public function add_admin_menu() {
        add_menu_page(
            'Manga AutoScraper',
            'Manga Scraper',
            'manage_options',
            'manga-autoscraper',
            array($this, 'render_admin_page'),
            'dashicons-book-alt',
            30
        );
    }

    public function register_settings() {
        register_setting('mas_settings', 'mas_ftp_config');
        register_setting('mas_settings', 'mas_2captcha_key');
        register_setting('mas_settings', 'mas_schedule_interval');
        register_setting('mas_settings', 'mas_sources');
    }

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_manga-autoscraper' !== $hook) {
            return;
        }

        wp_enqueue_style('mas-admin-style', MAS_PLUGIN_URL . 'admin/css/admin.css', array(), MAS_VERSION);
        wp_enqueue_script('mas-admin-script', MAS_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), MAS_VERSION, true);
        
        wp_localize_script('mas-admin-script', 'masAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mas_admin_nonce')
        ));
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Show a custom 'Settings saved.' message if settings were updated
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
        }

        $ftp_config = get_option('mas_ftp_config', array());
        $captcha_key = get_option('mas_2captcha_key', '');
        $schedule_interval = get_option('mas_schedule_interval', 'hourly');
        $sources = get_option('mas_sources', array());
        ?>
        <div class="wrap">
            <h1>Manga AutoScraper Settings</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('mas_settings'); ?>
                
                <h2>FTP Configuration</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">FTP Host</th>
                        <td>
                            <input type="text" name="mas_ftp_config[host]" value="<?php echo esc_attr($ftp_config['host'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">FTP Port</th>
                        <td>
                            <input type="number" name="mas_ftp_config[port]" value="<?php echo esc_attr($ftp_config['port'] ?? '21'); ?>" class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">FTP Username</th>
                        <td>
                            <input type="text" name="mas_ftp_config[username]" value="<?php echo esc_attr($ftp_config['username'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">FTP Password</th>
                        <td>
                            <input type="password" name="mas_ftp_config[password]" value="<?php echo esc_attr($ftp_config['password'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">FTP Path</th>
                        <td>
                            <input type="text" name="mas_ftp_config[path]" value="<?php echo esc_attr($ftp_config['path'] ?? ''); ?>" class="regular-text">
                            <p class="description">Path where manga images will be stored on the FTP server</p>
                        </td>
                    </tr>
                </table>

                <h2>Cloudflare Bypass</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">2captcha API Key</th>
                        <td>
                            <input type="text" name="mas_2captcha_key" value="<?php echo esc_attr($captcha_key); ?>" class="regular-text">
                            <p class="description">API key from 2captcha.com for bypassing Cloudflare protection</p>
                        </td>
                    </tr>
                </table>

                <h2>Schedule Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Scraping Interval</th>
                        <td>
                            <select name="mas_schedule_interval">
                                <option value="hourly" <?php selected($schedule_interval, 'hourly'); ?>>Hourly</option>
                                <option value="twicedaily" <?php selected($schedule_interval, 'twicedaily'); ?>>Twice Daily</option>
                                <option value="daily" <?php selected($schedule_interval, 'daily'); ?>>Daily</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <h2>Manga Sources</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable Sources</th>
                        <td>
                            <label>
                                <input type="checkbox" name="mas_sources[manga1688]" value="1" <?php checked(isset($sources['manga1688'])); ?>>
                                manga1688.com
                            </label><br>
                            <label>
                                <input type="checkbox" name="mas_sources[gomanga]" value="1" <?php checked(isset($sources['gomanga'])); ?>>
                                go-manga.com
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <div class="mas-manual-scrape">
                <h2>Manual Scrape</h2>
                <button type="button" class="button button-primary" id="mas-scrape-now">Scrape Now</button>
                <span class="spinner"></span>
                <div id="mas-scrape-status"></div>
            </div>
        </div>
        <?php
    }
} 