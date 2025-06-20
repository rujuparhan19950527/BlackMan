<?php
/**
 * Cloudflare Bypass Handler
 *
 * @package MangaAutoScraper
 */

if (!defined('ABSPATH')) {
    exit;
}

class CloudflareBypass {
    /**
     * Node.js script path for cloudscraper
     *
     * @var string
     */
    private $node_script_path;

    /**
     * Puppeteer script path
     *
     * @var string
     */
    private $puppeteer_script_path;

    /**
     * Constructor
     */
    public function __construct() {
        $this->node_script_path = plugin_dir_path(dirname(__FILE__)) . 'scripts/cloudscraper.js';
        $this->puppeteer_script_path = plugin_dir_path(dirname(__FILE__)) . 'scripts/puppeteer-bypass.js';
    }

    /**
     * Check if URL is protected by Cloudflare
     *
     * @param string $url Target URL
     * @return bool True if protected
     */
    public function isProtected($url) {
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'verify' => false,
                'allow_redirects' => false
            ]);

            $response = $client->get($url);
            $headers = $response->getHeaders();

            // Check for Cloudflare headers
            return (
                isset($headers['Server']) && strpos($headers['Server'][0], 'cloudflare') !== false ||
                isset($headers['cf-ray']) ||
                isset($headers['cf-cache-status'])
            );
        } catch (Exception $e) {
            // If request fails, assume it might be protected
            return true;
        }
    }

    /**
     * Bypass Cloudflare protection
     *
     * @param string $url Target URL
     * @return string|false Response content or false on failure
     */
    public function bypass($url) {
        // Try different methods in order of preference
        $methods = array(
            'puppeteer',
            'cloudscraper',
            'curl'
        );

        foreach ($methods as $method) {
            $result = $this->tryBypassMethod($method, $url);
            if ($result !== false) {
                return $result;
            }
        }

        return false;
    }

    /**
     * Try a specific bypass method
     *
     * @param string $method Bypass method
     * @param string $url Target URL
     * @return string|false Response content or false on failure
     */
    private function tryBypassMethod($method, $url) {
        switch ($method) {
            case 'puppeteer':
                return $this->bypassWithPuppeteer($url);
            case 'cloudscraper':
                return $this->bypassWithCloudscraper($url);
            case 'curl':
                return $this->bypassWithCurl($url);
            default:
                return false;
        }
    }

    /**
     * Bypass using Puppeteer
     *
     * @param string $url Target URL
     * @return string|false Response content or false on failure
     */
    private function bypassWithPuppeteer($url) {
        try {
            // Check if Node.js is available
            exec('node --version', $output, $return_var);
            if ($return_var !== 0) {
                throw new Exception('Node.js is not installed');
            }

            // Execute Puppeteer script
            $command = sprintf(
                'node %s "%s"',
                escapeshellarg($this->puppeteer_script_path),
                escapeshellarg($url)
            );

            $output = array();
            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new Exception('Puppeteer script failed');
            }

            return implode("\n", $output);
        } catch (Exception $e) {
            $this->logError('Puppeteer bypass failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bypass using cloudscraper
     *
     * @param string $url Target URL
     * @return string|false Response content or false on failure
     */
    private function bypassWithCloudscraper($url) {
        try {
            // Check if Node.js is available
            exec('node --version', $output, $return_var);
            if ($return_var !== 0) {
                throw new Exception('Node.js is not installed');
            }

            // Execute cloudscraper script
            $command = sprintf(
                'node %s "%s"',
                escapeshellarg($this->node_script_path),
                escapeshellarg($url)
            );

            $output = array();
            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new Exception('Cloudscraper script failed');
            }

            return implode("\n", $output);
        } catch (Exception $e) {
            $this->logError('Cloudscraper bypass failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bypass using cURL with custom headers
     *
     * @param string $url Target URL
     * @return string|false Response content or false on failure
     */
    private function bypassWithCurl($url) {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_HTTPHEADER => array(
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Cache-Control: max-age=0'
                )
            ));

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code !== 200) {
                throw new Exception('HTTP request failed with code: ' . $http_code);
            }

            return $response;
        } catch (Exception $e) {
            $this->logError('cURL bypass failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log error message
     *
     * @param string $message Error message
     */
    private function logError($message) {
        error_log('[Manga AutoScraper] ' . $message);
    }
} 