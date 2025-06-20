<?php
namespace MangaAutoScraper\Admin;

class AdminSettings {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {}

    public function render_settings_page() {
        echo '<div class="wrap"><h1>Manga AutoScraper Settings</h1><p>Welcome to the settings page.</p></div>';
    }
}
