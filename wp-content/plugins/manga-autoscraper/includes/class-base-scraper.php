<?php
namespace MangaAutoScraper\Scrapers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class BaseScraper {
    /**
     * Cloudflare bypass instance
     *
     * @var CloudflareBypass
     */
    protected $cloudflare;

    /**
     * Storage manager instance
     *
     * @var StorageManager
     */
    protected $storage;

    /**
     * Manga post manager instance
     *
     * @var MangaPostManager
     */
    protected $post_manager;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cloudflare = new CloudflareBypass();
        $this->storage = new StorageManager();
        $this->post_manager = new MangaPostManager();
    }

    /**
     * Make HTTP request to the target URL
     *
     * @param string $url Target URL
     * @return string|false Response content or false on failure
     */
    protected function makeRequest($url) {
        try {
            // Check if we need to bypass Cloudflare
            if ($this->cloudflare->isProtected($url)) {
                $response = $this->cloudflare->bypass($url);
            } else {
                // Use Guzzle for regular requests
                $client = new \GuzzleHttp\Client([
                    'timeout' => 30,
                    'verify' => false,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.5',
                        'Connection' => 'keep-alive',
                        'Upgrade-Insecure-Requests' => '1',
                        'Cache-Control' => 'max-age=0'
                    ]
                ]);

                $response = $client->get($url)->getBody()->getContents();
            }

            return $response;
        } catch (Exception $e) {
            $this->logError('Request failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Download image from URL
     *
     * @param string $url Image URL
     * @return string|false Local file path or false on failure
     */
    protected function downloadImage($url) {
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 30,
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Connection' => 'keep-alive',
                    'Referer' => $url
                ]
            ]);

            $response = $client->get($url);
            $content_type = $response->getHeaderLine('Content-Type');

            if (strpos($content_type, 'image/') === false) {
                throw new Exception('Invalid content type: ' . $content_type);
            }

            $temp_file = wp_tempnam();
            file_put_contents($temp_file, $response->getBody());

            return $temp_file;
        } catch (Exception $e) {
            $this->logError('Image download failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log error message
     *
     * @param string $message Error message
     */
    protected function logError($message) {
        error_log('[Manga AutoScraper] ' . $message);
    }

    /**
     * Get manga list from the site
     *
     * @return array Array of manga data
     */
    abstract public function getMangaList();

    /**
     * Get manga details including chapters
     *
     * @param string $manga_url Manga URL
     * @return array Manga details
     */
    abstract public function getMangaDetails($manga_url);

    /**
     * Get chapter images
     *
     * @param string $chapter_url Chapter URL
     * @return array Array of image URLs
     */
    abstract public function getChapterImages($chapter_url);

    /**
     * Process and save manga data
     * @param array $manga_data The manga data to process
     * @return bool Success status
     */
    public function processManga($manga_data) {
        // Validate manga data
        if (!$this->validateMangaData($manga_data)) {
            return false;
        }
        
        // Create or update manga post
        $post_id = $this->post_manager->createOrUpdateManga($manga_data);
        
        if (!$post_id) {
            return false;
        }
        
        // Process chapters
        foreach ($manga_data['chapters'] as $chapter) {
            $this->processChapter($post_id, $chapter);
        }
        
        return true;
    }
    
    /**
     * Process a single chapter
     * @param int $post_id The manga post ID
     * @param array $chapter_data The chapter data
     * @return bool Success status
     */
    protected function processChapter($post_id, $chapter_data) {
        // Get chapter images
        $images = $this->getChapterImages($chapter_data['url']);
        
        if (empty($images)) {
            return false;
        }
        
        // Upload images to storage
        $uploaded_images = [];
        foreach ($images as $image_url) {
            $uploaded_path = $this->storage->uploadImage($image_url);
            if ($uploaded_path) {
                $uploaded_images[] = $uploaded_path;
            }
        }
        
        // Create chapter post
        return $this->post_manager->createChapter($post_id, $chapter_data, $uploaded_images);
    }
    
    /**
     * Validate manga data
     * @param array $manga_data The manga data to validate
     * @return bool Validation status
     */
    protected function validateMangaData($manga_data) {
        $required_fields = ['title', 'description', 'cover_url', 'chapters'];
        
        foreach ($required_fields as $field) {
            if (!isset($manga_data[$field])) {
                return false;
            }
        }
        
        return true;
    }
} 