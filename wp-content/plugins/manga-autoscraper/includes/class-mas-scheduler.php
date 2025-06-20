<?php
/**
 * Scheduler class for Manga AutoScraper
 */

class MAS_Scheduler {
    private $settings;

    public function __construct() {
        $this->settings = get_option('mas_settings');
        add_action('init', array($this, 'schedule_events'));
        add_action('mas_scheduled_scrape', array($this, 'run_scheduled_scrape'));
        add_filter('cron_schedules', array($this, 'add_cron_interval'));
    }

    public function schedule_events() {
        if (!wp_next_scheduled('mas_scheduled_scrape')) {
            $interval = get_option('mas_schedule_interval', 'hourly');
            wp_schedule_event(time(), $interval, 'mas_scheduled_scrape');
        }
    }

    public function add_cron_interval($schedules) {
        $schedules['hourly'] = array(
            'interval' => 3600,
            'display' => __('Every Hour')
        );
        $schedules['twicedaily'] = array(
            'interval' => 43200,
            'display' => __('Twice Daily')
        );
        $schedules['daily'] = array(
            'interval' => 86400,
            'display' => __('Daily')
        );
        return $schedules;
    }

    public function run_scheduled_scrape() {
        $source_url = isset($this->settings['source_url']) ? $this->settings['source_url'] : '';
        if (empty($source_url)) {
            error_log('Manga AutoScraper: Source URL is not set for scheduled scraping');
            return;
        }

        $scraper = new MAS_Scraper();
        $result = $scraper->scrape($source_url);
        if ($result) {
            error_log('Manga AutoScraper: Scheduled scraping completed successfully');
        } else {
            error_log('Manga AutoScraper: Scheduled scraping failed');
        }
    }

    public function update_schedule($interval) {
        wp_clear_scheduled_hook('mas_scheduled_scrape');
        wp_schedule_event(time(), $interval, 'mas_scheduled_scrape');
    }
} 