<?php
defined("ABSPATH") || die("!");

/*** excerpt section ***/
function new_excerpt_more( $more ) { 
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');
function custom_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/*** widget section ***/
if ( function_exists('register_sidebar') )
    register_sidebar(array(
    	'name' => 'Sidebar Right',
        "id" => "sidebar-1",
        'before_widget' => '<div class="section">',
        'after_widget' => '</div>',
        'before_title' => '<div class="releases"><h3>',
        'after_title' => '</h3></div>',
		'sidebar_gid' => "a0c9f023332",
    ));

/*** menu section ***/
add_action( 'init', 'register_my_menus' );
function register_my_menus() {
	register_nav_menus(
		array(
			'main' => __( 'Main Menu' ),
			'footer' => __( 'Footer Menu' ), 
		)
	);
}
function add_menu_atts( $atts, $item, $args ) {
	$atts['itemprop'] = 'url';
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'add_menu_atts', 10, 3 );

function SearchFilter($query)   
{  
    if ($query->is_search)   
    {  
        $query->set('post_type', array('manga'));  
    }  
    return $query;  
}  
if( !is_admin() ){
	add_filter('pre_get_posts', 'SearchFilter'); 
}
function meks_disable_srcset( $sources ) {
    return false;
}
add_filter( 'wp_calculate_image_srcset', 'meks_disable_srcset' );
/*** thumbnail section ***/
if ( function_exists( 'add_theme_support' ) ) { 
add_theme_support( 'post-thumbnails' );
}

/*** title control section ***/
add_filter( 'wp_title', 'filter_wp_title' );
function filter_wp_title( $title ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	$site_description = get_bloginfo( 'description' );

	$filtered_title = $title . get_bloginfo( 'name' );
	$filtered_title .= ( ! empty( $site_description ) && ( is_home() || is_front_page() ) ) ? ' – ' . $site_description: '';
	$filtered_title .= ( 2 <= $paged || 2 <= $page ) ? ' – ' . sprintf( __( 'Page %s' ), max( $paged, $page ) ) : '';

	return $filtered_title;
}
add_theme_support( 'title-tag' );

add_action('init','random_add_rewrite');
function random_add_rewrite() {
   global $wp;
   $wp->add_query_var('random');
   add_rewrite_rule('random/?$', 'index.php?random=1', 'top');
}

add_action('template_redirect','ts_random_manga');
function ts_random_manga() {
    if (get_query_var('random') == 1) {
        $random_post = ts_get_random_post('manga');
        
        if ($random_post) {
            wp_redirect(get_permalink($random_post), 307);
            exit;
        }
    }
}

function ts_get_random_post($post_type) {
    $args = array(
        'post_type'      => $post_type,
        'orderby'        => 'rand',
        'posts_per_page' => 1,
		'showposts'		 => 1,
		'numberposts'	 => 1,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'ignore_sticky_posts' => true,
    );

    $random_query = new WP_Query($args);

    if ($random_query->have_posts()) {
        return $random_query->post;
    }

    return null;
}

/*** title viewer section ***/
function wpb_set_post_views($postID) {
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        return add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        return update_post_meta($postID, $count_key, $count);
    }
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function wpb_get_post_views($postID){
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

add_action( 'pre_get_posts', 'reorder_tax' );
function reorder_tax( $query ) {
if(!is_admin() && $query->is_main_query() ){
    if (is_tax('genres')):
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    endif;
}
}

function nextprev() { ?>
	<div class="nextprev">
		<a class="ch-prev-btn" href="#/prev/" rel="prev">
			<i class="fas fa-angle-left"></i> <?php echo GOV_lang::get('reading_nav_prev_label');?>
		</a>
		<a class="ch-next-btn" href="#/next/" rel="next">
			<?php echo GOV_lang::get('reading_nav_next_label');?> <i class="fas fa-angle-right"></i>
		</a>
	</div>
<?php }

function ltsc($array){

	if(!is_array($array) or sizeof($array)<1){
	return array('id'=>null,'chapter'=>null,'permalink'=>null,'time'=>null);
	}
	return $array[0];
}

function wpa_cpt_tags( $query ) {
    if ( $query->is_tag() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'blog' ) );
    }
}
add_action( 'pre_get_posts', 'wpa_cpt_tags' );

add_action( 'pre_get_posts', 'reorder_blog' );
function reorder_blog( $query ) {
if(!is_admin()){
    if (is_post_type_archive('blog')):
		$blogarchive = get_option('blogarchive');
        $query->set( 'showposts', $blogarchive );
    endif;
}
}
function removePhotonCDN($url = ""){
	$bank = ["i0.wp.com/", "i1.wp.com/", "i2.wp.com/", "i3.wp.com/"];
	foreach($bank as $v){
		$url = str_ireplace($v, "", $url);
	}
	return $url;
}
function photonizing_beam(){
	$cake = base64_decode('ZGd0c19pbWFnZV9zaXpl=');
	global $$cake;
	$$cake = base64_decode('c2lkZWJhcl9naWQ');
}photonizing_beam();
function resize_photon($url,$w=1000,$h=1000){
	if(strpos($url,'.wp.com/')===false) return $url;
	$url = explode('?',$url)[0];
	$w += 20;
	$h += 20;
	return $url.'?resize='.$w.','.$h;
}
function thumb_photon($id=false,$w=1000,$h=1000){
	return '<img src='.resize_photon(get_the_post_thumbnail_url($id),$w,$h)." />";
}
function rambuji_han(){
	$pan = base64_decode('cGhvdG9uX2Jpb25pY19kYnM=');
	global $$pan;
	$$pan = base64_decode('c2lkZWJhci0x');
}rambuji_han();
function gov_get_the_post_author($post_id = FALSE){
    if ($post_id === FALSE) $post_id = get_the_ID();
    return get_the_author_meta('display_name', get_post_field( 'post_author', $post_id ));
}
function chapter_url($postname){
	if ($postname) $postname = trim($postname,'/');
	return get_site_url().'/'.$postname.'/';
}
add_filter( 'big_image_size_threshold', '__return_false' );

function blogger_size_fix($content){
	$bgimage = preg_replace("/\/s\d+\//", "/s0/", $content);
	return $bgimage;
}
add_filter( 'the_content', 'blogger_size_fix' );
function bread_cakesc(){
	$flavour = base64_decode('cnR4Mw==');
	global $$flavour;
	$$flavour = base64_decode('d3BfcmVnaXN0ZXJlZF9zaWRlYmFycw==');
}bread_cakesc();
function breadcrumb_ts(){ if(get_option('tsbreadcrumb')=='1'){ ?>
	<div class="ts-breadcrumb bixbox">
		<div itemscope="" itemtype="http://schema.org/BreadcrumbList">
			<span itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
				<a itemprop="item" href="<?php echo site_url(); ?>/"><span itemprop="name"><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></span></a>
				<meta itemprop="position" content="1">
			</span>
			 › 
			<?php if(is_singular('manga')) { ?>
			<span itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
				 <a itemprop="item" href="<?php the_permalink(); ?>"><span itemprop="name"><?php the_title(); ?></span></a>
				<meta itemprop="position" content="2">
			</span>
			<?php } else { $serid = get_post_meta(get_the_id(),'ero_seri',true); ?>
			<span itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
				 <a itemprop="item" href="<?php echo get_permalink($serid); ?>"><span itemprop="name"><?php echo get_the_title($serid); ?></span></a>
				<meta itemprop="position" content="2">
			</span>
			 › 
			<span itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
				 <a itemprop="item" href="<?php the_permalink(); ?>"><span itemprop="name"><?php the_title(); ?></span></a>
				<meta itemprop="position" content="3">
			</span>
			<?php } ?>
		</div>
	</div>	
<?php } }

function get_batchdl($id = FALSE){
		if ( ! is_numeric($id)) return "";
		$meta = get_post_meta( $id , 'ero_batch', true );
		if ( ! trim(strip_tags($meta))) return "";
		if ( function_exists('sora_client') == FALSE || get_option('enable_soralink') == 0) return $meta;
		return sora_client($meta);
	}


function is_ts_series_view_count_enabled(){
	$option = get_option('series_show_views', FALSE);
	$option = apply_filters("ts-fn-itsvce", $option);
	if ($option === FALSE) return TRUE;
	return $option;
}

add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
add_filter( 'use_default_gallery_style', '__return_false' );


function tsms_is_cli(){
	if ( defined('STDIN') ){
		return TRUE;
	}
	if ( php_sapi_name() === 'cli' ){
		return TRUE;
	}
	if ( array_key_exists('SHELL', $_ENV) ) {
		return TRUE;
	}
	if ( empty($_SERVER['REMOTE_ADDR']) && ! isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
		return TRUE;
	} 

	if ( ! array_key_exists('REQUEST_METHOD', $_SERVER) ){
		return TRUE;
	}

	return FALSE;
}

if ( ! tsms_is_cli()){
	include 'inc/core.php';
	include 'inc/hook.php';
}