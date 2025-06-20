<?php
/**
 * Manga Post Manager
 *
 * @package MangaAutoScraper
 */

if (!defined('ABSPATH')) {
    exit;
}

class MangaPostManager {
    /**
     * Post type for manga
     */
    const POST_TYPE = 'wp-manga';

    /**
     * Post type for chapters
     */
    const CHAPTER_POST_TYPE = 'wp-manga-chapter';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'registerPostTypes'));
        add_action('add_meta_boxes', array($this, 'addMetaBoxes'));
        add_action('save_post', array($this, 'saveMetaBoxes'));
        add_action('admin_menu', array($this, 'addMangaSubmenu'));
    }

    /**
     * Register custom post types
     */
    public function registerPostTypes() {
        // Register manga post type if not already registered by theme
        if (!post_type_exists(self::POST_TYPE)) {
            register_post_type(self::POST_TYPE, array(
                'labels' => array(
                    'name' => __('Manga', 'manga-autoscraper'),
                    'singular_name' => __('Manga', 'manga-autoscraper')
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
                'menu_icon' => 'dashicons-book-alt'
            ));
        }

        // Register chapter post type if not already registered by theme
        if (!post_type_exists(self::CHAPTER_POST_TYPE)) {
            register_post_type(self::CHAPTER_POST_TYPE, array(
                'labels' => array(
                    'name' => __('Chapters', 'manga-autoscraper'),
                    'singular_name' => __('Chapter', 'manga-autoscraper')
                ),
                'public' => true,
                'show_in_menu' => false,
                'supports' => array('title')
            ));
        }
    }

    /**
     * Add meta boxes for manga details
     */
    public function addMetaBoxes() {
        add_meta_box(
            'manga_details',
            __('Manga Details', 'manga-autoscraper'),
            array($this, 'renderMangaDetailsMetaBox'),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Render manga details meta box
     *
     * @param WP_Post $post Post object
     */
    public function renderMangaDetailsMetaBox($post) {
        wp_nonce_field('manga_details_meta_box', 'manga_details_meta_box_nonce');

        $manga_details = get_post_meta($post->ID, '_manga_details', true);
        $manga_details = wp_parse_args($manga_details, array(
            'alternative_names' => '',
            'author' => '',
            'artist' => '',
            'status' => '',
            'genres' => array(),
            'type' => '',
            'release_year' => '',
            'rating' => ''
        ));

        ?>
        <div class="manga-details-meta-box">
            <p>
                <label for="manga_alternative_names"><?php _e('Alternative Names', 'manga-autoscraper'); ?></label>
                <input type="text" id="manga_alternative_names" name="manga_details[alternative_names]" 
                       value="<?php echo esc_attr($manga_details['alternative_names']); ?>" class="widefat">
            </p>
            <p>
                <label for="manga_author"><?php _e('Author', 'manga-autoscraper'); ?></label>
                <input type="text" id="manga_author" name="manga_details[author]" 
                       value="<?php echo esc_attr($manga_details['author']); ?>" class="widefat">
            </p>
            <p>
                <label for="manga_artist"><?php _e('Artist', 'manga-autoscraper'); ?></label>
                <input type="text" id="manga_artist" name="manga_details[artist]" 
                       value="<?php echo esc_attr($manga_details['artist']); ?>" class="widefat">
            </p>
            <p>
                <label for="manga_status"><?php _e('Status', 'manga-autoscraper'); ?></label>
                <select id="manga_status" name="manga_details[status]" class="widefat">
                    <option value="ongoing" <?php selected($manga_details['status'], 'ongoing'); ?>><?php _e('Ongoing', 'manga-autoscraper'); ?></option>
                    <option value="completed" <?php selected($manga_details['status'], 'completed'); ?>><?php _e('Completed', 'manga-autoscraper'); ?></option>
                    <option value="hiatus" <?php selected($manga_details['status'], 'hiatus'); ?>><?php _e('Hiatus', 'manga-autoscraper'); ?></option>
                </select>
            </p>
            <p>
                <label for="manga_type"><?php _e('Type', 'manga-autoscraper'); ?></label>
                <select id="manga_type" name="manga_details[type]" class="widefat">
                    <option value="manga" <?php selected($manga_details['type'], 'manga'); ?>><?php _e('Manga', 'manga-autoscraper'); ?></option>
                    <option value="manhwa" <?php selected($manga_details['type'], 'manhwa'); ?>><?php _e('Manhwa', 'manga-autoscraper'); ?></option>
                    <option value="manhua" <?php selected($manga_details['type'], 'manhua'); ?>><?php _e('Manhua', 'manga-autoscraper'); ?></option>
                </select>
            </p>
            <p>
                <label for="manga_release_year"><?php _e('Release Year', 'manga-autoscraper'); ?></label>
                <input type="number" id="manga_release_year" name="manga_details[release_year]" 
                       value="<?php echo esc_attr($manga_details['release_year']); ?>" class="widefat">
            </p>
            <p>
                <label for="manga_rating"><?php _e('Rating', 'manga-autoscraper'); ?></label>
                <input type="number" id="manga_rating" name="manga_details[rating]" 
                       value="<?php echo esc_attr($manga_details['rating']); ?>" min="0" max="5" step="0.1" class="widefat">
            </p>
        </div>
        <?php
    }

    /**
     * Save meta box data
     *
     * @param int $post_id Post ID
     */
    public function saveMetaBoxes($post_id) {
        if (!isset($_POST['manga_details_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['manga_details_meta_box_nonce'], 'manga_details_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['manga_details'])) {
            $manga_details = array_map('sanitize_text_field', $_POST['manga_details']);
            update_post_meta($post_id, '_manga_details', $manga_details);
        }
    }

    /**
     * Create or update manga post
     *
     * @param array $manga_data Manga data
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    public function createOrUpdateManga($manga_data) {
        // Check if manga already exists
        $existing_post = $this->findMangaByTitle($manga_data['title']);

        if ($existing_post) {
            return $this->updateManga($existing_post->ID, $manga_data);
        }

        return $this->createManga($manga_data);
    }

    /**
     * Find manga by title
     *
     * @param string $title Manga title
     * @return WP_Post|null Post object if found, null otherwise
     */
    private function findMangaByTitle($title) {
        $posts = get_posts(array(
            'post_type' => self::POST_TYPE,
            'title' => $title,
            'posts_per_page' => 1
        ));

        return !empty($posts) ? $posts[0] : null;
    }

    /**
     * Create new manga post
     *
     * @param array $manga_data Manga data
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private function createManga($manga_data) {
        // Prepare post data
        $post_data = array(
            'post_title' => $manga_data['title'],
            'post_content' => $manga_data['description'],
            'post_status' => 'publish',
            'post_type' => self::POST_TYPE
        );

        // Insert post
        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Set featured image
        if (!empty($manga_data['cover_image'])) {
            $this->setFeaturedImage($post_id, $manga_data['cover_image']);
        }

        // Save manga details
        $manga_details = array(
            'alternative_names' => isset($manga_data['alternative_names']) ? $manga_data['alternative_names'] : '',
            'author' => isset($manga_data['author']) ? $manga_data['author'] : '',
            'artist' => isset($manga_data['artist']) ? $manga_data['artist'] : '',
            'status' => isset($manga_data['status']) ? $manga_data['status'] : 'ongoing',
            'genres' => isset($manga_data['genres']) ? $manga_data['genres'] : array(),
            'type' => isset($manga_data['type']) ? $manga_data['type'] : 'manga',
            'release_year' => isset($manga_data['release_year']) ? $manga_data['release_year'] : '',
            'rating' => isset($manga_data['rating']) ? $manga_data['rating'] : ''
        );

        update_post_meta($post_id, '_manga_details', $manga_details);

        // Create chapters
        if (!empty($manga_data['chapters'])) {
            $this->createChapters($post_id, $manga_data['chapters']);
        }

        return $post_id;
    }

    /**
     * Update existing manga post
     *
     * @param int $post_id Post ID
     * @param array $manga_data Manga data
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private function updateManga($post_id, $manga_data) {
        // Update post data
        $post_data = array(
            'ID' => $post_id,
            'post_title' => $manga_data['title'],
            'post_content' => $manga_data['description']
        );

        $result = wp_update_post($post_data);

        if (is_wp_error($result)) {
            return $result;
        }

        // Update featured image if provided
        if (!empty($manga_data['cover_image'])) {
            $this->setFeaturedImage($post_id, $manga_data['cover_image']);
        }

        // Update manga details
        $manga_details = array(
            'alternative_names' => isset($manga_data['alternative_names']) ? $manga_data['alternative_names'] : '',
            'author' => isset($manga_data['author']) ? $manga_data['author'] : '',
            'artist' => isset($manga_data['artist']) ? $manga_data['artist'] : '',
            'status' => isset($manga_data['status']) ? $manga_data['status'] : 'ongoing',
            'genres' => isset($manga_data['genres']) ? $manga_data['genres'] : array(),
            'type' => isset($manga_data['type']) ? $manga_data['type'] : 'manga',
            'release_year' => isset($manga_data['release_year']) ? $manga_data['release_year'] : '',
            'rating' => isset($manga_data['rating']) ? $manga_data['rating'] : ''
        );

        update_post_meta($post_id, '_manga_details', $manga_details);

        // Update chapters
        if (!empty($manga_data['chapters'])) {
            $this->updateChapters($post_id, $manga_data['chapters']);
        }

        return $post_id;
    }

    /**
     * Set featured image for manga
     *
     * @param int $post_id Post ID
     * @param string $image_url Image URL
     * @return int|false Attachment ID on success, false on failure
     */
    private function setFeaturedImage($post_id, $image_url) {
        // Download image
        $temp_file = download_url($image_url);

        if (is_wp_error($temp_file)) {
            return false;
        }

        // Prepare file data
        $file_array = array(
            'name' => basename($image_url),
            'tmp_name' => $temp_file
        );

        // Upload file
        $attachment_id = media_handle_sideload($file_array, $post_id);

        if (is_wp_error($attachment_id)) {
            @unlink($temp_file);
            return false;
        }

        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);

        return $attachment_id;
    }

    /**
     * Create chapters for manga
     *
     * @param int $manga_id Manga post ID
     * @param array $chapters Chapter data
     */
    private function createChapters($manga_id, $chapters) {
        foreach ($chapters as $chapter) {
            $this->createChapter($manga_id, $chapter);
        }
    }

    /**
     * Create single chapter
     *
     * @param int $manga_id Manga post ID
     * @param array $chapter_data Chapter data
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private function createChapter($manga_id, $chapter_data) {
        // Check if chapter already exists
        $existing_chapter = $this->findChapterByTitle($manga_id, $chapter_data['title']);

        if ($existing_chapter) {
            return $this->updateChapter($existing_chapter->ID, $chapter_data);
        }

        // Create chapter post
        $post_data = array(
            'post_title' => $chapter_data['title'],
            'post_status' => 'publish',
            'post_type' => self::CHAPTER_POST_TYPE,
            'post_parent' => $manga_id
        );

        $chapter_id = wp_insert_post($post_data);

        if (is_wp_error($chapter_id)) {
            return $chapter_id;
        }

        // Save chapter data
        $chapter_meta = array(
            'chapter_number' => isset($chapter_data['number']) ? $chapter_data['number'] : '',
            'chapter_date' => isset($chapter_data['date']) ? $chapter_data['date'] : '',
            'chapter_images' => isset($chapter_data['images']) ? $chapter_data['images'] : array()
        );

        update_post_meta($chapter_id, '_chapter_data', $chapter_meta);

        return $chapter_id;
    }

    /**
     * Update chapters for manga
     *
     * @param int $manga_id Manga post ID
     * @param array $chapters Chapter data
     */
    private function updateChapters($manga_id, $chapters) {
        foreach ($chapters as $chapter) {
            $this->createChapter($manga_id, $chapter);
        }
    }

    /**
     * Find chapter by title
     *
     * @param int $manga_id Manga post ID
     * @param string $title Chapter title
     * @return WP_Post|null Post object if found, null otherwise
     */
    private function findChapterByTitle($manga_id, $title) {
        $posts = get_posts(array(
            'post_type' => self::CHAPTER_POST_TYPE,
            'post_parent' => $manga_id,
            'title' => $title,
            'posts_per_page' => 1
        ));

        return !empty($posts) ? $posts[0] : null;
    }

    /**
     * Update existing chapter
     *
     * @param int $chapter_id Chapter post ID
     * @param array $chapter_data Chapter data
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private function updateChapter($chapter_id, $chapter_data) {
        // Update chapter post
        $post_data = array(
            'ID' => $chapter_id,
            'post_title' => $chapter_data['title']
        );

        $result = wp_update_post($post_data);

        if (is_wp_error($result)) {
            return $result;
        }

        // Update chapter data
        $chapter_meta = array(
            'chapter_number' => isset($chapter_data['number']) ? $chapter_data['number'] : '',
            'chapter_date' => isset($chapter_data['date']) ? $chapter_data['date'] : '',
            'chapter_images' => isset($chapter_data['images']) ? $chapter_data['images'] : array()
        );

        update_post_meta($chapter_id, '_chapter_data', $chapter_meta);

        return $chapter_id;
    }

    /**
     * Add Manga submenu under Manga AutoScraper
     */
    public function addMangaSubmenu() {
        add_submenu_page(
            'manga-autoscraper', // Parent slug (main plugin menu)
            __('Manage Manga', 'manga-autoscraper'), // Page title
            __('Manage Manga', 'manga-autoscraper'), // Menu title
            'manage_options', // Capability
            'edit.php?post_type=' . self::POST_TYPE, // Menu slug (link to manga post type listing)
            null // No callback needed for built-in post type list
        );
    }
} 