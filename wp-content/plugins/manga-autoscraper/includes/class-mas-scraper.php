<?php
/**
 * Core scraper class for Manga AutoScraper
 */
class MAS_Scraper {
    private $sources = [
        'manga1688' => [
            'url' => 'https://manga1688.com',
            'cloudflare' => true
        ],
        'gomanga' => [
            'url' => 'https://go-manga.com',
            'cloudflare' => true
        ],
        'niceoppai' => [
            'url' => 'https://niceoppai.net',
            'cloudflare' => true
        ]
    ];

    private $ftp_config;
    private $logger;

    public function __construct() {
        $this->logger = new MAS_Logger();
        $this->load_ftp_config();
        add_action('mas_scheduled_scrape', array($this, 'run_scheduled_scrape'));
    }

    private function load_ftp_config() {
        $this->ftp_config = get_option('mas_ftp_config', array(
            'host' => '',
            'port' => 21,
            'username' => '',
            'password' => '',
            'path' => ''
        ));
    }

    public function run_scheduled_scrape() {
        foreach ($this->sources as $source_id => $source) {
            try {
                $this->scrape_source($source_id, $source);
            } catch (Exception $e) {
                $this->logger->log_error("Error scraping {$source_id}: " . $e->getMessage());
            }
        }
    }

    private function scrape_source($source_id, $source) {
        $html = $this->fetch_page($source['url']);
        if (!$html) {
            throw new Exception("Failed to fetch page from {$source['url']}");
        }

        // Parse manga list based on source
        $manga_list = $this->parse_manga_list($html, $source_id);
        
        foreach ($manga_list as $manga) {
            $this->process_manga($manga, $source_id);
        }
    }

    private function fetch_page($url) {
        $args = array(
            'timeout' => 30,
            'sslverify' => false,
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            )
        );

        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            throw new Exception("HTTP request failed: " . $response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        
        // Check for Cloudflare challenge
        if (strpos($body, 'cf-browser-verification') !== false) {
            $body = $this->bypass_cloudflare($url);
        }

        return $body;
    }

    private function bypass_cloudflare($url) {
        // Implement Cloudflare bypass using 2captcha or similar service
        // This is a placeholder - actual implementation would depend on the specific bypass method
        $api_key = get_option('mas_2captcha_key', '');
        if (empty($api_key)) {
            throw new Exception("2captcha API key not configured");
        }

        // TODO: Implement actual Cloudflare bypass logic
        return '';
    }

    private function parse_manga_list($html, $source_id) {
        $manga_list = array();
        
        switch ($source_id) {
            case 'manga1688':
                // Parse manga1688.com specific HTML structure
                break;
            case 'gomanga':
                // Parse go-manga.com specific HTML structure
                break;
            case 'niceoppai':
                // Parse niceoppai.net specific HTML structure
                break;
        }

        return $manga_list;
    }

    private function process_manga($manga_data, $source_id) {
        // Check if manga already exists
        $existing_post = $this->find_existing_manga($manga_data['title']);
        
        if ($existing_post) {
            $this->update_manga($existing_post, $manga_data);
        } else {
            $this->create_manga($manga_data);
        }
    }

    private function find_existing_manga($title) {
        $args = array(
            'post_type' => 'wp-manga',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_manga_title',
                    'value' => $title,
                    'compare' => '='
                )
            )
        );

        $query = new WP_Query($args);
        return $query->have_posts() ? $query->posts[0] : null;
    }

    private function create_manga($manga_data) {
        $post_data = array(
            'post_title' => $manga_data['title'],
            'post_content' => $manga_data['description'],
            'post_status' => 'publish',
            'post_type' => 'wp-manga'
        );

        $post_id = wp_insert_post($post_data);
        
        if (!is_wp_error($post_id)) {
            $this->save_manga_meta($post_id, $manga_data);
            $this->upload_manga_images($post_id, $manga_data['images']);
        }
    }

    private function update_manga($post, $manga_data) {
        $post_data = array(
            'ID' => $post->ID,
            'post_content' => $manga_data['description']
        );

        wp_update_post($post_data);
        $this->save_manga_meta($post->ID, $manga_data);
        
        // Update images if new ones are available
        if (!empty($manga_data['images'])) {
            $this->upload_manga_images($post->ID, $manga_data['images']);
        }
    }

    private function save_manga_meta($post_id, $manga_data) {
        update_post_meta($post_id, '_manga_title', $manga_data['title']);
        update_post_meta($post_id, '_manga_author', $manga_data['author']);
        update_post_meta($post_id, '_manga_status', $manga_data['status']);
        update_post_meta($post_id, '_manga_source', $manga_data['source']);
        // Add more meta fields as needed
    }

    private function upload_manga_images($post_id, $images) {
        if (empty($this->ftp_config['host'])) {
            $this->logger->log_error("FTP configuration not set");
            return;
        }

        $ftp = ftp_connect($this->ftp_config['host'], $this->ftp_config['port']);
        if (!$ftp) {
            throw new Exception("Could not connect to FTP server");
        }

        if (!ftp_login($ftp, $this->ftp_config['username'], $this->ftp_config['password'])) {
            throw new Exception("FTP login failed");
        }

        foreach ($images as $image) {
            $local_path = download_url($image['url']);
            if (is_wp_error($local_path)) {
                continue;
            }

            $remote_path = $this->ftp_config['path'] . '/' . $image['filename'];
            if (ftp_put($ftp, $remote_path, $local_path, FTP_BINARY)) {
                update_post_meta($post_id, '_manga_image_' . $image['type'], $remote_path);
            }

            unlink($local_path);
        }

        ftp_close($ftp);
    }
} 