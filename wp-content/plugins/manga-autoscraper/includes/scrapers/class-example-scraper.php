<?php
/**
 * Example Manga Scraper Implementation
 *
 * @package MangaAutoScraper
 */

if (!defined('ABSPATH')) {
    exit;
}

class ExampleScraper extends BaseScraper {
    /**
     * Base URL of the manga site
     *
     * @var string
     */
    protected $base_url = 'https://example-manga-site.com';

    /**
     * Get manga list from the site
     *
     * @return array Array of manga data
     */
    public function getMangaList() {
        $manga_list = array();
        $page = 1;
        $has_next = true;

        while ($has_next) {
            try {
                $url = $this->base_url . '/manga-list/page/' . $page;
                $response = $this->makeRequest($url);
                
                if (!$response) {
                    break;
                }

                // Parse manga list from the page
                $manga_items = $this->parseMangaList($response);
                
                if (empty($manga_items)) {
                    $has_next = false;
                    break;
                }

                $manga_list = array_merge($manga_list, $manga_items);
                
                // Check if there's a next page
                $has_next = $this->hasNextPage($response);
                $page++;

                // Add delay between requests
                sleep(2);
            } catch (Exception $e) {
                $this->logError('Error fetching manga list: ' . $e->getMessage());
                break;
            }
        }

        return $manga_list;
    }

    /**
     * Get manga details including chapters
     *
     * @param string $manga_url Manga URL
     * @return array Manga details
     */
    public function getMangaDetails($manga_url) {
        try {
            $response = $this->makeRequest($manga_url);
            
            if (!$response) {
                return false;
            }

            return array(
                'title' => $this->parseTitle($response),
                'description' => $this->parseDescription($response),
                'cover_image' => $this->parseCoverImage($response),
                'chapters' => $this->parseChapters($response),
                'genres' => $this->parseGenres($response),
                'status' => $this->parseStatus($response),
                'author' => $this->parseAuthor($response)
            );
        } catch (Exception $e) {
            $this->logError('Error fetching manga details: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get chapter images
     *
     * @param string $chapter_url Chapter URL
     * @return array Array of image URLs
     */
    public function getChapterImages($chapter_url) {
        try {
            $response = $this->makeRequest($chapter_url);
            
            if (!$response) {
                return false;
            }

            return $this->parseChapterImages($response);
        } catch (Exception $e) {
            $this->logError('Error fetching chapter images: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse manga list from HTML response
     *
     * @param string $html HTML content
     * @return array Array of manga items
     */
    protected function parseMangaList($html) {
        $manga_items = array();
        
        // Use DOMDocument to parse HTML
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        // Example XPath query - adjust based on site structure
        $manga_nodes = $xpath->query('//div[contains(@class, "manga-item")]');

        foreach ($manga_nodes as $node) {
            $title_node = $xpath->query('.//h2[@class="manga-title"]', $node)->item(0);
            $url_node = $xpath->query('.//a[@class="manga-link"]', $node)->item(0);

            if ($title_node && $url_node) {
                $manga_items[] = array(
                    'title' => trim($title_node->textContent),
                    'url' => $this->base_url . $url_node->getAttribute('href')
                );
            }
        }

        return $manga_items;
    }

    /**
     * Parse manga title from HTML
     *
     * @param string $html HTML content
     * @return string Manga title
     */
    protected function parseTitle($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $title_node = $xpath->query('//h1[@class="manga-title"]')->item(0);
        return $title_node ? trim($title_node->textContent) : '';
    }

    /**
     * Parse manga description from HTML
     *
     * @param string $html HTML content
     * @return string Manga description
     */
    protected function parseDescription($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $desc_node = $xpath->query('//div[@class="manga-description"]')->item(0);
        return $desc_node ? trim($desc_node->textContent) : '';
    }

    /**
     * Parse cover image URL from HTML
     *
     * @param string $html HTML content
     * @return string Cover image URL
     */
    protected function parseCoverImage($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $img_node = $xpath->query('//div[@class="manga-cover"]//img')->item(0);
        return $img_node ? $img_node->getAttribute('src') : '';
    }

    /**
     * Parse chapter list from HTML
     *
     * @param string $html HTML content
     * @return array Array of chapter data
     */
    protected function parseChapters($html) {
        $chapters = array();
        
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $chapter_nodes = $xpath->query('//div[contains(@class, "chapter-item")]');

        foreach ($chapter_nodes as $node) {
            $title_node = $xpath->query('.//span[@class="chapter-title"]', $node)->item(0);
            $url_node = $xpath->query('.//a[@class="chapter-link"]', $node)->item(0);
            $date_node = $xpath->query('.//span[@class="chapter-date"]', $node)->item(0);

            if ($title_node && $url_node) {
                $chapters[] = array(
                    'title' => trim($title_node->textContent),
                    'url' => $this->base_url . $url_node->getAttribute('href'),
                    'date' => $date_node ? trim($date_node->textContent) : ''
                );
            }
        }

        return $chapters;
    }

    /**
     * Parse genres from HTML
     *
     * @param string $html HTML content
     * @return array Array of genres
     */
    protected function parseGenres($html) {
        $genres = array();
        
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $genre_nodes = $xpath->query('//div[@class="manga-genres"]//a');

        foreach ($genre_nodes as $node) {
            $genres[] = trim($node->textContent);
        }

        return $genres;
    }

    /**
     * Parse manga status from HTML
     *
     * @param string $html HTML content
     * @return string Manga status
     */
    protected function parseStatus($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $status_node = $xpath->query('//div[@class="manga-status"]')->item(0);
        return $status_node ? trim($status_node->textContent) : '';
    }

    /**
     * Parse author from HTML
     *
     * @param string $html HTML content
     * @return string Author name
     */
    protected function parseAuthor($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $author_node = $xpath->query('//div[@class="manga-author"]')->item(0);
        return $author_node ? trim($author_node->textContent) : '';
    }

    /**
     * Parse chapter images from HTML
     *
     * @param string $html HTML content
     * @return array Array of image URLs
     */
    protected function parseChapterImages($html) {
        $images = array();
        
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $img_nodes = $xpath->query('//div[@class="chapter-images"]//img');

        foreach ($img_nodes as $node) {
            $images[] = $node->getAttribute('src');
        }

        return $images;
    }

    /**
     * Check if there's a next page
     *
     * @param string $html HTML content
     * @return bool True if next page exists
     */
    protected function hasNextPage($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $next_node = $xpath->query('//a[@class="next-page"]')->item(0);
        return $next_node !== null;
    }
} 