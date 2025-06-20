<?php
/**
 * Admin Settings Handler
 *
 * @package MangaAutoScraper
 */

if (!defined('ABSPATH')) {
    exit;
}

class AdminSettings {
    /**
     * Plugin options
     *
     * @var array
     */
    private $options;

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
        add_action('wp_ajax_mas_update_sources', 'mas_update_sources_callback');
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
    }

    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_menu_page(
            __('Manga AutoScraper', 'manga-autoscraper'),
            __('Manga Scraper', 'manga-autoscraper'),
            'manage_options',
            'manga-autoscraper',
            array($this, 'renderSettingsPage'),
            'dashicons-update',
            30
        );
    }

    /**
     * Register settings
     */
    public function registerSettings() {
        register_setting('manga_autoscraper_settings', 'manga_autoscraper_settings', array($this, 'sanitizeSettings'));

        // Storage Settings
        add_settings_section(
            'manga_autoscraper_storage',
            __('Storage Settings', 'manga-autoscraper'),
            array($this, 'renderStorageSection'),
            'manga_autoscraper_settings'
        );

        // Source Settings
        add_settings_section(
            'manga_autoscraper_source',
            __('Source Settings', 'manga-autoscraper'),
            array($this, 'renderSourceSection'),
            'manga_autoscraper_settings'
        );

        // Schedule Settings
        add_settings_section(
            'manga_autoscraper_schedule',
            __('Schedule Settings', 'manga-autoscraper'),
            array($this, 'renderScheduleSection'),
            'manga_autoscraper_settings'
        );

        // Add settings fields
        $this->addSettingsFields();
    }

    /**
     * Add settings fields
     */
    private function addSettingsFields() {
        // Storage Fields
        add_settings_field(
            'use_sftp',
            __('Use SFTP', 'manga-autoscraper'),
            array($this, 'renderCheckboxField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_storage',
            array(
                'label_for' => 'use_sftp',
                'name' => 'use_sftp',
                'description' => __('Use SFTP instead of FTP for secure file transfer', 'manga-autoscraper'),
                'default' => false
            )
        );

        add_settings_field(
            'ftp_host',
            __('Host', 'manga-autoscraper'),
            array($this, 'renderTextField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_storage',
            array(
                'label_for' => 'ftp_host',
                'name' => 'ftp_host',
                'description' => __('FTP/SFTP server hostname', 'manga-autoscraper'),
                'default' => '89.163.146.144'
            )
        );

        add_settings_field(
            'ftp_username',
            __('Username', 'manga-autoscraper'),
            array($this, 'renderTextField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_storage',
            array(
                'label_for' => 'ftp_username',
                'name' => 'ftp_username',
                'description' => __('FTP/SFTP username', 'manga-autoscraper')
            )
        );

        add_settings_field(
            'ftp_password',
            __('Password', 'manga-autoscraper'),
            array($this, 'renderPasswordField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_storage',
            array(
                'label_for' => 'ftp_password',
                'name' => 'ftp_password',
                'description' => __('FTP/SFTP password', 'manga-autoscraper')
            )
        );

        add_settings_field(
            'ftp_path',
            __('Remote Path', 'manga-autoscraper'),
            array($this, 'renderTextField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_storage',
            array(
                'label_for' => 'ftp_path',
                'name' => 'ftp_path',
                'description' => __('Remote directory path for uploaded files', 'manga-autoscraper')
            )
        );

        add_settings_field(
            'ftp_url',
            __('Base URL', 'manga-autoscraper'),
            array($this, 'renderTextField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_storage',
            array(
                'label_for' => 'ftp_url',
                'name' => 'ftp_url',
                'description' => __('Base URL for accessing uploaded files', 'manga-autoscraper')
            )
        );

        // Source Fields
        add_settings_field(
            'manga_sources',
            __('Manga Sources', 'manga-autoscraper'),
            array($this, 'renderSourcesField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_source',
            array(
                'label_for' => 'manga_sources',
                'name' => 'manga_sources',
                'description' => __('Select manga sources to scrape', 'manga-autoscraper')
            )
        );

        // Schedule Fields
        add_settings_field(
            'update_interval',
            __('Update Interval', 'manga-autoscraper'),
            array($this, 'renderNumberField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_schedule',
            array(
                'label_for' => 'update_interval',
                'name' => 'update_interval',
                'description' => __('How often to check for updates (in hours)', 'manga-autoscraper'),
                'min' => 1,
                'max' => 24,
                'default' => 6
            )
        );

        add_settings_field(
            'last_run',
            __('Last Run', 'manga-autoscraper'),
            array($this, 'renderLastRunField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_schedule'
        );

        add_settings_field(
            'next_run',
            __('Next Run', 'manga-autoscraper'),
            array($this, 'renderNextRunField'),
            'manga_autoscraper_settings',
            'manga_autoscraper_schedule'
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueueAdminScripts($hook) {
        if ($hook !== 'toplevel_page_manga-autoscraper') {
            return;
        }

        wp_enqueue_style(
            'manga-autoscraper-admin',
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array(),
            MANGA_AUTOSCRAPER_VERSION
        );

        wp_enqueue_script(
            'manga-autoscraper-admin',
            plugin_dir_url(__FILE__) . 'js/admin.js',
            array('jquery'),
            MANGA_AUTOSCRAPER_VERSION,
            true
        );

        wp_localize_script('manga-autoscraper-admin', 'mangaAutoscraper', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('manga_autoscraper_nonce')
        ));
    }

    /**
     * Render settings page
     */
    public function renderSettingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'manga-autoscraper'));
        }

        $this->options = get_option('manga_autoscraper_settings', array());
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <?php settings_errors(); ?>

            <div class="manga-autoscraper-status">
                <h2><?php esc_html_e('Scraper Status', 'manga-autoscraper'); ?></h2>
                <p>
                    <?php
                    $last_run = get_option('manga_autoscraper_last_run');
                    $status = get_option('manga_autoscraper_last_run_status');
                    
                    if ($last_run) {
                        $date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_run);
                        $status_class = $status === 'success' ? 'success' : 'error';
                        $status_text = $status === 'success' ? 
                            esc_html__('Success', 'manga-autoscraper') : 
                            esc_html__('Failed', 'manga-autoscraper');
                        
                        printf(
                            esc_html__('Last run: %1$s (%2$s)', 'manga-autoscraper'),
                            '<strong>' . esc_html($date) . '</strong>',
                            '<span class="status-' . esc_attr($status_class) . '">' . $status_text . '</span>'
                        );
                    } else {
                        esc_html_e('Never run', 'manga-autoscraper');
                    }
                    ?>
                </p>
                <p>
                    <?php
                    $next_run = wp_next_scheduled('manga_autoscraper_cron');
                    if ($next_run) {
                        $date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_run);
                        printf(
                            esc_html__('Next run: %s', 'manga-autoscraper'),
                            '<strong>' . esc_html($date) . '</strong>'
                        );
                    } else {
                        esc_html_e('Not scheduled', 'manga-autoscraper');
                    }
                    ?>
                </p>
                <button type="button" class="button button-primary" id="run-scraper">
                    <?php esc_html_e('Run Scraper Now', 'manga-autoscraper'); ?>
                </button>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields('manga_autoscraper_settings');
                do_settings_sections('manga_autoscraper_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render storage section
     */
    public function renderStorageSection() {
        echo '<p>' . __('Configure your FTP/SFTP storage settings.', 'manga-autoscraper') . '</p>';
    }

    /**
     * Render source section
     */
    public function renderSourceSection() {
        echo '<p>' . __('Select and configure manga sources to scrape.', 'manga-autoscraper') . '</p>';
    }

    /**
     * Render schedule section
     */
    public function renderScheduleSection() {
        echo '<p>' . __('Configure how often the scraper should run automatically.', 'manga-autoscraper') . '</p>';
    }

    /**
     * Render checkbox field
     */
    public function renderCheckboxField($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : false;
        ?>
        <label>
            <input type="checkbox" name="manga_autoscraper_settings[<?php echo esc_attr($name); ?>]" value="1" <?php checked($value); ?>>
            <?php echo esc_html($args['description']); ?>
        </label>
        <?php
    }

    /**
     * Render text field
     */
    public function renderTextField($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : '';
        ?>
        <input type="text" 
               id="<?php echo esc_attr($args['label_for']); ?>"
               name="manga_autoscraper_settings[<?php echo esc_attr($name); ?>]"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    /**
     * Render password field
     */
    public function renderPasswordField($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->decryptPassword($this->options[$name]) : '';
        ?>
        <input type="password" 
               id="<?php echo esc_attr($args['label_for']); ?>"
               name="manga_autoscraper_settings[<?php echo esc_attr($name); ?>]"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
               autocomplete="new-password">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    /**
     * Render number field
     */
    public function renderNumberField($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : $args['default'];
        ?>
        <input type="number" 
               id="<?php echo esc_attr($args['label_for']); ?>"
               name="manga_autoscraper_settings[<?php echo esc_attr($name); ?>]"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min']); ?>"
               max="<?php echo esc_attr($args['max']); ?>"
               class="small-text">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    /**
     * Render sources field
     */
    public function renderSourcesField($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : array();
        
        // Get available sources
        $sources = $this->getAvailableSources();
        ?>
        <div class="manga-sources">
            <?php foreach ($sources as $source_id => $source) : ?>
                <label class="source-item">
                    <input type="checkbox" 
                           name="manga_autoscraper_settings[<?php echo esc_attr($name); ?>][]"
                           value="<?php echo esc_attr($source_id); ?>"
                           <?php checked(in_array($source_id, $value)); ?>>
                    <?php echo esc_html($source['name']); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    /**
     * Render last run field
     */
    public function renderLastRunField() {
        $last_run = get_option('manga_autoscraper_last_run');
        $status = get_option('manga_autoscraper_last_run_status');
        
        if ($last_run) {
            $date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_run);
            $status_class = $status === 'success' ? 'success' : 'error';
            $status_text = $status === 'success' ? __('Success', 'manga-autoscraper') : __('Failed', 'manga-autoscraper');
            
            echo '<p>';
            echo '<strong>' . $date . '</strong><br>';
            echo '<span class="status-' . $status_class . '">' . $status_text . '</span>';
            echo '</p>';
        } else {
            echo '<p>' . __('Never', 'manga-autoscraper') . '</p>';
        }
    }

    /**
     * Render next run field
     */
    public function renderNextRunField() {
        $next_run = wp_next_scheduled('manga_autoscraper_cron');
        
        if ($next_run) {
            echo '<p>' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_run) . '</p>';
        } else {
            echo '<p>' . __('Not scheduled', 'manga-autoscraper') . '</p>';
        }
    }

    /**
     * Get available sources
     */
    private function getAvailableSources() {
        return array(
            'mangakakalot' => array(
                'name' => 'MangaKakalot',
                'url' => 'https://mangakakalot.com'
            ),
            'manganelo' => array(
                'name' => 'MangaNelo',
                'url' => 'https://manganelo.com'
            ),
            'mangadex' => array(
                'name' => 'MangaDex',
                'url' => 'https://mangadex.org'
            )
        );
    }

    /**
     * Sanitize settings
     */
    public function sanitizeSettings($input) {
        $sanitized = array();
        $sanitized['use_sftp'] = isset($input['use_sftp']) ? (bool) $input['use_sftp'] : false;
        $sanitized['ftp_host'] = sanitize_text_field($input['ftp_host']);
        $sanitized['ftp_username'] = sanitize_text_field($input['ftp_username']);
        if (!empty($input['ftp_password'])) {
            $sanitized['ftp_password'] = $this->encryptPassword($input['ftp_password']);
        } else {
            $sanitized['ftp_password'] = $this->options['ftp_password'] ?? '';
        }
        $sanitized['ftp_path'] = trailingslashit(sanitize_text_field($input['ftp_path']));
        $sanitized['ftp_url'] = esc_url_raw($input['ftp_url']);
        $sanitized['manga_sources'] = array();
        if (!empty($input['manga_sources'])) {
            foreach ($input['manga_sources'] as $source) {
                $sanitized['manga_sources'][] = esc_url_raw($source);
            }
        }
        $sanitized['update_interval'] = absint($input['update_interval']);
        return $sanitized;
    }

    /**
     * Reschedule cron job
     */
    private function rescheduleCron($interval) {
        // Clear existing schedule
        wp_clear_scheduled_hook('manga_autoscraper_cron');

        // Add new schedule
        if (!wp_next_scheduled('manga_autoscraper_cron')) {
            wp_schedule_event(time(), 'manga_autoscraper_interval', 'manga_autoscraper_cron');
        }
    }
}

function mas_update_sources_callback() {
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

    if (update_option('mas_sources', $sources)) {
        wp_send_json_success('Source updated successfully.');
    } else {
        wp_send_json_error('Failed to update source.');
    }
}
 