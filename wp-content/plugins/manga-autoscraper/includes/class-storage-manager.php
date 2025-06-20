<?php
/**
 * Storage Manager for External FTP/SFTP
 *
 * @package MangaAutoScraper
 */

if (!defined('ABSPATH')) {
    exit;
}

class StorageManager {
    /**
     * FTP/SFTP connection
     *
     * @var resource
     */
    private $connection;

    /**
     * Storage settings
     *
     * @var array
     */
    private $settings;

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('manga_autoscraper_settings', array());
    }

    /**
     * Upload file to external storage
     *
     * @param string $local_path Local file path
     * @param string $remote_path Remote file path (optional)
     * @return string|WP_Error Remote URL on success, WP_Error on failure
     */
    public function uploadFile($local_path, $remote_path = '') {
        try {
            // Validate local file
            if (!file_exists($local_path)) {
                throw new Exception('Local file does not exist: ' . $local_path);
            }

            // Generate remote path if not provided
            if (empty($remote_path)) {
                $remote_path = $this->generateRemotePath($local_path);
            }

            // Connect to server
            if (!$this->connect()) {
                throw new Exception('Failed to connect to storage server');
            }

            // Upload file
            if (!$this->upload($local_path, $remote_path)) {
                throw new Exception('Failed to upload file');
            }

            // Close connection
            $this->disconnect();

            // Return remote URL
            return $this->getRemoteUrl($remote_path);
        } catch (Exception $e) {
            $this->logError('Upload failed: ' . $e->getMessage());
            return new WP_Error('upload_failed', $e->getMessage());
        }
    }

    /**
     * Connect to storage server
     *
     * @return bool True on success, false on failure
     */
    private function connect() {
        try {
            if (empty($this->settings['use_sftp'])) {
                // FTP connection
                $this->connection = ftp_connect(
                    $this->settings['ftp_host'],
                    $this->settings['ftp_port'] ?? 21,
                    $this->settings['ftp_timeout'] ?? 30
                );

                if (!$this->connection) {
                    throw new Exception('Failed to connect to FTP server');
                }

                // Set timeout
                ftp_set_option($this->connection, FTP_TIMEOUT_SEC, $this->settings['ftp_timeout'] ?? 30);

                // Login
                if (!ftp_login(
                    $this->connection,
                    $this->settings['ftp_username'],
                    $this->decryptPassword($this->settings['ftp_password'])
                )) {
                    throw new Exception('Failed to login to FTP server');
                }

                // Enable passive mode
                ftp_pasv($this->connection, true);
            } else {
                // SFTP connection
                if (!function_exists('ssh2_connect')) {
                    throw new Exception('SFTP extension not installed');
                }

                $this->connection = ssh2_connect(
                    $this->settings['ftp_host'],
                    $this->settings['ftp_port'] ?? 22,
                    array(
                        'hostkey' => 'ssh-rsa,ssh-dss',
                        'timeout' => $this->settings['ftp_timeout'] ?? 30
                    )
                );

                if (!$this->connection) {
                    throw new Exception('Failed to connect to SFTP server');
                }

                // Authenticate
                if (!ssh2_auth_password(
                    $this->connection,
                    $this->settings['ftp_username'],
                    $this->decryptPassword($this->settings['ftp_password'])
                )) {
                    throw new Exception('Failed to authenticate with SFTP server');
                }

                // Initialize SFTP subsystem
                $this->connection = ssh2_sftp($this->connection);

                if (!$this->connection) {
                    throw new Exception('Failed to initialize SFTP subsystem');
                }
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Connection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload file to server
     *
     * @param string $local_path Local file path
     * @param string $remote_path Remote file path
     * @return bool True on success, false on failure
     */
    private function upload($local_path, $remote_path) {
        try {
            // Validate local file
            if (!is_readable($local_path)) {
                throw new Exception('Local file is not readable: ' . $local_path);
            }

            // Validate file size
            $max_size = wp_max_upload_size();
            if (filesize($local_path) > $max_size) {
                throw new Exception('File size exceeds maximum allowed size');
            }

            // Validate file type
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            $file_type = wp_check_filetype(basename($local_path));
            if (!in_array($file_type['ext'], $allowed_types)) {
                throw new Exception('File type not allowed');
            }

            // Create remote directory if it doesn't exist
            $remote_dir = dirname($remote_path);
            $this->createRemoteDirectory($remote_dir);

            if (empty($this->settings['use_sftp'])) {
                // FTP upload
                if (!ftp_put($this->connection, $remote_path, $local_path, FTP_BINARY)) {
                    throw new Exception('Failed to upload file via FTP');
                }
            } else {
                // SFTP upload
                $stream = fopen("ssh2.sftp://{$this->connection}{$remote_path}", 'w');
                if (!$stream) {
                    throw new Exception('Failed to open remote file for writing');
                }

                $data = file_get_contents($local_path);
                if ($data === false) {
                    fclose($stream);
                    throw new Exception('Failed to read local file');
                }

                if (fwrite($stream, $data) === false) {
                    fclose($stream);
                    throw new Exception('Failed to write to remote file');
                }

                fclose($stream);
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Upload failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create remote directory
     *
     * @param string $path Directory path
     * @return bool True on success, false on failure
     */
    private function createRemoteDirectory($path) {
        try {
            // Validate path
            $path = $this->sanitizePath($path);
            
            if (empty($this->settings['use_sftp'])) {
                // FTP directory creation
                $current = '';
                $dirs = explode('/', trim($path, '/'));

                foreach ($dirs as $dir) {
                    $current .= '/' . $dir;
                    if (!@ftp_chdir($this->connection, $current)) {
                        if (!ftp_mkdir($this->connection, $current)) {
                            throw new Exception('Failed to create directory: ' . $current);
                        }
                        // Set permissions
                        ftp_chmod($this->connection, 0755, $current);
                    }
                }
            } else {
                // SFTP directory creation
                $current = '';
                $dirs = explode('/', trim($path, '/'));

                foreach ($dirs as $dir) {
                    $current .= '/' . $dir;
                    if (!@ssh2_sftp_stat($this->connection, $current)) {
                        if (!ssh2_sftp_mkdir($this->connection, $current, 0755, true)) {
                            throw new Exception('Failed to create directory: ' . $current);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Directory creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sanitize path
     *
     * @param string $path Path to sanitize
     * @return string Sanitized path
     */
    private function sanitizePath($path) {
        // Remove any null bytes
        $path = str_replace(chr(0), '', $path);
        
        // Remove any directory traversal attempts
        $path = str_replace(array('../', '..\\'), '', $path);
        
        // Remove any double slashes
        $path = preg_replace('#/+#', '/', $path);
        
        // Ensure path starts with /
        $path = '/' . ltrim($path, '/');
        
        // Validate against base path
        $base_path = rtrim($this->settings['ftp_path'], '/');
        if (strpos($path, $base_path) !== 0) {
            $path = $base_path . $path;
        }
        
        return $path;
    }

    /**
     * Generate remote path for file
     *
     * @param string $local_path Local file path
     * @return string Remote path
     */
    private function generateRemotePath($local_path) {
        // Get file info
        $file_info = pathinfo($local_path);
        $extension = strtolower($file_info['extension']);

        // Generate unique filename
        $filename = wp_unique_filename(
            $this->settings['ftp_path'],
            $file_info['filename'] . '.' . $extension
        );

        // Build remote path
        $remote_path = rtrim($this->settings['ftp_path'], '/') . '/' . $filename;

        return $remote_path;
    }

    /**
     * Test FTP/SFTP connection
     *
     * @return bool True on success, false on failure
     */
    public function testConnection() {
        try {
            if (!$this->connect()) {
                return false;
            }
            $this->disconnect();
            return true;
        } catch (Exception $e) {
            $this->logError('Connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get remote URL for a file
     *
     * @param string $remote_path Remote file path
     * @return string Remote URL
     */
    private function getRemoteUrl($remote_path) {
        $base_url = $this->settings['ftp_url'] ?? '';
        if (empty($base_url)) {
            return '';
        }

        // Remove leading slash if present
        $remote_path = ltrim($remote_path, '/');
        
        // Ensure base URL doesn't end with slash
        $base_url = rtrim($base_url, '/');
        
        return $base_url . '/' . $remote_path;
    }

    /**
     * Disconnect from server
     */
    private function disconnect() {
        if (!empty($this->connection)) {
            if (empty($this->settings['use_sftp'])) {
                ftp_close($this->connection);
            }
            $this->connection = null;
        }
    }

    /**
     * Log error message
     *
     * @param string $message Error message
     */
    private function logError($message) {
        // Sanitize message before logging
        $message = sanitize_text_field($message);
        
        // Log to WordPress error log
        error_log('[Manga AutoScraper] ' . $message);
        
        // Log to plugin's error log if enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $log_file = WP_CONTENT_DIR . '/manga-autoscraper.log';
            $timestamp = current_time('mysql');
            $log_message = sprintf("[%s] %s\n", $timestamp, $message);
            @file_put_contents($log_file, $log_message, FILE_APPEND);
        }
    }

    /**
     * Decrypt password
     *
     * @param string $password Encrypted password
     * @return string Decrypted password
     */
    private function decryptPassword($password) {
        // Implement your decryption logic here
        return $password;
    }
} 