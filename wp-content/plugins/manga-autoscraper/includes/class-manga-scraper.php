<?php
namespace MangaAutoScraper;

class MangaScraper {
    private $cloudflare;
    private $storage;
    private $post_manager;
    private $settings;
    private $scrapers = [];
    
    public function __construct() {
        $this->settings = get_option('manga_autoscraper_settings');
        $this->cloudflare = new CloudflareBypass();
        $this->storage = new StorageManager($this->settings);
        $this->post_manager = new MangaPostManager();
        
        $this->initializeScrapers();
    }
    
    /**
     * Initialize scrapers for each configured source
     */
    private function initializeScrapers() {
        if (empty($this->settings['sources'])) {
            return;
        }
        
        foreach ($this->settings['sources'] as $source) {
            $scraper_class = $this->getScraperClass($source['name']);
            if ($scraper_class) {
                $this->scrapers[] = new $scraper_class($this->cloudflare, $this->storage, $this->post_manager);
            }
        }
    }
    
    /**
     * Get scraper class name for a source
     * @param string $source_name The source name
     * @return string|false The scraper class name or false if not found
     */
    private function getScraperClass($source_name) {
        $class_name = 'MangaAutoScraper\\Scrapers\\Scraper' . str_replace(' ', '', ucwords($source_name));
        return class_exists($class_name) ? $class_name : false;
    }
    
    /**
     * Run the scraping process
     */
    public function run() {
        if (empty($this->scrapers)) {
            error_log('Manga AutoScraper: No scrapers configured');
            return;
        }
        
        foreach ($this->scrapers as $scraper) {
            try {
                $this->processScraper($scraper);
            } catch (\Exception $e) {
                error_log('Manga AutoScraper Error: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Process a single scraper
     * @param BaseScraper $scraper The scraper instance
     */
    private function processScraper($scraper) {
        // Get manga list
        $manga_list = $scraper->getMangaList();
        
        if (empty($manga_list)) {
            error_log('Manga AutoScraper: No manga found for source');
            return;
        }
        
        // Process each manga
        foreach ($manga_list as $manga) {
            try {
                $manga_details = $scraper->getMangaDetails($manga['url']);
                
                if (!$manga_details) {
                    continue;
                }
                
                // Process manga data
                $scraper->processManga($manga_details);
                
                // Add delay between requests
                sleep(2);
            } catch (\Exception $e) {
                error_log('Manga AutoScraper Error: ' . $e->getMessage());
                continue;
            }
        }
    }
    
    /**
     * Get the last run time
     * @return int|false The last run timestamp or false if never run
     */
    public function getLastRun() {
        return get_option('manga_autoscraper_last_run');
    }
    
    /**
     * Update the last run time
     */
    public function updateLastRun() {
        update_option('manga_autoscraper_last_run', time());
    }
    
    /**
     * Get the next scheduled run time
     * @return int|false The next run timestamp or false if not scheduled
     */
    public function getNextRun() {
        return wp_next_scheduled('manga_autoscraper_cron');
    }
    
    /**
     * Reschedule the cron job
     */
    public function reschedule() {
        wp_clear_scheduled_hook('manga_autoscraper_cron');
        
        if (!wp_next_scheduled('manga_autoscraper_cron')) {
            wp_schedule_event(time(), 'manga_autoscraper_interval', 'manga_autoscraper_cron');
        }
    }
} 