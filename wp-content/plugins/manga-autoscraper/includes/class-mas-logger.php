<?php
/**
 * Logger class for Manga AutoScraper
 */

class MAS_Logger {
    private $log_dir;
    private $log_file;

    public function __construct() {
        $this->log_dir = MAS_PLUGIN_DIR . 'logs';
        $this->log_file = $this->log_dir . '/scraper.log';
        
        if (!file_exists($this->log_dir)) {
            wp_mkdir_p($this->log_dir);
        }
    }

    public function log_info($message) {
        $this->log('INFO', $message);
    }

    public function log_error($message) {
        $this->log('ERROR', $message);
    }

    public function log_warning($message) {
        $this->log('WARNING', $message);
    }

    private function log($level, $message) {
        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = sprintf("[%s] [%s] %s\n", $timestamp, $level, $message);
        
        error_log($log_entry, 3, $this->log_file);
    }

    public function get_logs($lines = 100) {
        if (!file_exists($this->log_file)) {
            return array();
        }

        $logs = array();
        $file = new SplFileObject($this->log_file, 'r');
        $file->seek(PHP_INT_MAX);
        $last_line = $file->key();
        
        $start = max(0, $last_line - $lines);
        $file->seek($start);
        
        while (!$file->eof()) {
            $logs[] = $file->fgets();
        }
        
        return $logs;
    }

    public function clear_logs() {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
    }
} 