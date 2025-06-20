<?php
function themesia_assets(){
    wp_enqueue_style('style', get_stylesheet_uri(), [], THEMESIA_VERSION);
	if (get_option('tsrtl') == '1') {
		wp_enqueue_style('ts-rtl', get_template_directory_uri() . '/assets/css/rtl.css', [], THEMESIA_VERSION, 'all');
	}
    wp_enqueue_style('ts-lightstyle', get_template_directory_uri() . '/assets/css/lightmode.css', [], THEMESIA_VERSION, 'all');
	wp_enqueue_script('ts-history_script',get_template_directory_uri() . '/assets/js/history.js',array( 'jquery' ), THEMESIA_VERSION);
	wp_enqueue_script('tsfn_scripts', get_template_directory_uri() . '/assets/js/function.js',array( 'jquery' ),THEMESIA_VERSION);

    if (is_singular('manga')) {
		wp_enqueue_script('tsmedia', get_template_directory_uri() . '/assets/js/tsmedia.js', array('jquery'), THEMESIA_VERSION, false);
		if (get_option('gallerymanga') == '1') {
			wp_enqueue_style('ts-blueimp', get_template_directory_uri() . '/assets/css/blueimp-gallery.min.css', [], '2.38.0', 'all');
		}
	}
	
    wp_deregister_script('jquery');
    wp_register_script('jquery', get_template_directory_uri() . '/assets/js/jquery.min.js', [], '3.5.1');
    wp_enqueue_script('jquery');
	if(is_home()){
		wp_enqueue_style( 'ts-swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css',[],'4.5.1','all');
	}
    
	if (is_singular("post")){
		wp_enqueue_script('ts-reading-options', get_template_directory_uri() . '/assets/js/reading-options.js', array('tsfn_scripts'), THEMESIA_VERSION, false);
		if (get_option('tslazyload')) {
			wp_enqueue_script('tslazyloadpf', 'https://cdn.jsdelivr.net/npm/intersection-observer@0.7.0/intersection-observer.min.js', array('ts-reading-options'), '7.0', false);
			wp_enqueue_script('tslazyload', 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.1.2/dist/lazyload.min.js', array('tslazyloadpf'), '17.1.2', false);
		}
	}
	if (is_singular("manga") || is_singular("post")){
		wp_enqueue_script('ts-nsfw_scripts', get_template_directory_uri() . '/assets/js/nsfw.js', array('tsfn_scripts'), THEMESIA_VERSION, false);
	}
    if (is_singular('manga')) {
		if (get_option('gallerymanga') == '1') {
			wp_enqueue_script('ts-blueimp', get_template_directory_uri() . '/assets/js/blueimp-gallery.min.js', array('jquery'), '2.38.0', false);
		}
		wp_enqueue_script('ts-chapter-search', get_template_directory_uri() . '/assets/js/chapter-search.js', array('tsfn_scripts'), THEMESIA_VERSION, false);
	}
    if (is_home()) {
		if (get_option('homerecommend') == '1') {
			wp_enqueue_script('ts-tabs', get_template_directory_uri() . '/assets/js/tabs.js', array('jquery'), THEMESIA_VERSION, false);
		}
	}
    wp_enqueue_script('ts-filter', get_template_directory_uri() . '/assets/js/filter.js', array('jquery'), THEMESIA_VERSION, true);

}
add_action( 'wp_enqueue_scripts', 'themesia_assets' );

function remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' );
	wp_dequeue_style( 'global-styles' );
} 
add_action( 'wp_enqueue_scripts', 'remove_wp_block_library_css', 100 );

function themesia_footer_assets() {
	if (is_home() || is_singular('manga')) {
		wp_enqueue_script('ts-owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array('jquery'), '2.3.4', false);
	}
	wp_enqueue_style('ts-fontawesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', [], '5.13.0', 'all');
	if (is_home() || is_singular('manga')) {
		wp_enqueue_style('ts-owl-carousel', get_template_directory_uri() . '/assets/css/owl.carousel.css', [], '1.0.0', 'all');
	}
}
add_action( 'get_footer', 'themesia_footer_assets' );

add_action('wp_head', function(){ ?>
	<script>
		var baseurl = "<?php echo esc_url( home_url( '/' ) ); ?>";
		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' )?>";
		<?php if (GOV_bookmark::is_enabled()) { ?> 
		var max_bookmark = <?php echo addcslashes(strip_tags(get_option('bookmark')), '"')?:"50"; ?>;
		<?php } ?> 
		var max_history = <?php echo addcslashes(strip_tags(get_option('max_history')), '"')?:"10"; ?>;
		var defaultTheme = "<?php echo addcslashes(strip_tags(get_option('defaulttheme')), '"'); ?>";
	</script>
<?php }, 8);
add_action ('wp_head','themesiaHeader');
function themesiaHeader(){ ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<?php echo get_option('tsscriptheader'); ?>
<script>
	$(document).ready(function(){
		$(".shme").click(function(){
			$(".mm").toggleClass("shwx");
		});
		$(".srcmob").click(function(){
			$(".minmb").toggleClass("minmbx");
		});
	});
</script>
<script type="text/javascript">
$(document).ready(function(){
	
	//Check to see if the window is top if not then display button
	$(window).scroll(function(){
		if ($(this).scrollTop() > 100) {
			$('.scrollToTop').fadeIn();
		} else {
			$('.scrollToTop').fadeOut();
		}
	});
	
	//Click event to scroll to top
	$('.scrollToTop').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});
	
});
</script>
<?php $tc = get_option('themecolor'); if($tc) { ?>
<style>
	.th, .serieslist.pop ul li.topone .limit .bw .ctr,.releases .vl,.scrollToTop,#sidebar #bm-history li a:hover,.hpage a,#footer .footermenu,.footer-az .az-list li a,.main-info .info-desc .spe span:before,.bxcl ul li span.dt a,.bookmark,.commentx #submit,.radiox input:checked ~ .checkmarkx,.advancedsearch button.searchz,.lightmode .nav_apb a:hover,.lista a,.lightmode .lista a:hover,.nextprev a,.disqusmen #commentform #submit, .blogbox .btitle .vl, .bigblogt span a,.big-slider .paging .centerpaging .swiper-pagination span.swiper-pagination-bullet-active {background:<?php echo $tc; ?>} .pagination span.page-numbers.current,.quickfilter .filters .filter.submit button,#sidebar .section .ts-wpop-series-gen .ts-wpop-nav-tabs li.active span,#gallery.owl-loaded .owl-dots .owl-dot.active span,.bs.stylefiv .bsx .chfiv li a:hover,.bs.stylesix .bsx .chfiv li a:hover {background:<?php echo $tc; ?> !important} 
	#sidebar .section #searchform #searchsubmit,.series-gen .nav-tabs li.active a,.lastend .inepcx a,.nav_apb a:hover,#top-menu li a:hover,.readingnav.rnavbot .readingnavbot .readingbar .readingprogress,.lightmode .main-info .info-desc .wd-full .mgen a:hover,.lightmode .bxcl ul li .chbox:hover,.lightmode ul.taxindex li a:hover,.comment-list .comment-body .reply a:hover,.topmobile,.bxcl ul::-webkit-scrollbar-thumb,.lightmode .slider:before,.quickfilter .filters .filter .genrez::-webkit-scrollbar-thumb,.hothome .releases,.lightmode .seriestucon .seriestucont .seriestucontr .seriestugenre a:hover,.bloglist .blogbox .innerblog .thumb .btags,.slidernom2 .mainslider .limit .sliderinfo .sliderinfolimit .start-reading span:hover,.lightmode .bixbox .bvlcen .bvl,.tsfront .headfront .fsearch #submit,.tsfront .headfront .fhomebutton a,.section .wp-block-search .wp-block-search__button {background:<?php echo $tc; ?>} 
	.lightmode #sidebar .section h4, .lightmode .serieslist ul li .ctr,.listupd .utao .uta .luf ul li,.lightmode .bs .bsx:hover .tt,.soralist ul,a:hover,.lightmode .blogbox .btitle h3,.lightmode .blogbox .btitle h1,.bxcl ul li .lchx a:visited, .listupd .utao .uta .luf ul li a:visited,.lightmode .pagination a:hover,.lightmode a:hover,#sidebar .serieslist ul li .leftseries h2 a:hover,.bs.styletere .epxs,.bxcl ul li .dt a,.lightmode .main-info .info-desc .wd-full .mgen a,.lightmode #sidebar .serieslist ul li .leftseries h2 a:hover,.comment-list .comment-body .reply a,.bxcl ul li .eph-num a:visited,.headpost .allc a,.lightmode .seriestucon .seriestucont .seriestucontr .seriestugenre a,.bs.stylesix .bsx .chfiv li a,.bs.stylefor .bsx a:visited .bigor .adds .epxs,.bs.stylefiv .bsx .chfiv li a:visited .fivchap,.stylesven .sveninner .svenbottom .svenmetabot .svenchapters li a:visited,.listupd .utao.stylegg .uta .luf ul li a:visited .eggchap,.lightmode .listupd .utao.stylegg .uta .luf ul li a:visited .eggchap,.lightmode .serieslist ul li .leftseries span a {color:<?php echo $tc; ?>} 
	.bxcl ul li .lchx a:visited, .listupd .utao .uta .luf ul li a:visited,.bs.stylesix .bsx .chfiv li a {color:<?php echo $tc; ?> !important} 
	.lightmode .serieslist ul li .ctr,.advancedsearch button.searchz,.lista a,.lightmode .lista a:hover,.blogbox .boxlist .bma .bmb .bmba, .page.blog .thumb,#sidebar .section #searchform #searchsubmit,.lightmode .main-info .info-desc .wd-full .mgen a,.lightmode .bxcl ul li .chbox:hover, .comment-list .comment-body .reply a,.lightmode .seriestucon .seriestucont .seriestucontr .seriestugenre a,.slidernom2 .mainslider .limit .sliderinfo .sliderinfolimit .start-reading span:hover,.section .wp-block-search .wp-block-search__button {border-color:<?php echo $tc; ?>}
	.bs.stylesix .bsx .chfiv li a:before {content: "";background: <?php echo $tc; ?>;opacity: 0.2;position: absolute;display: block;left: 0;right: 0;top: 0;bottom: 0;border-radius: 10px;}
	.bs.stylesix .bsx .chfiv li a{background:none !important;}
	.slider.round:before{background: #333;}
	.hpage a:hover,.bs.stylefiv .bsx .chfiv li a:hover,.bs.stylesix .bsx .chfiv li a:hover{color:#FFF !important;}
	@media only screen and (max-width:800px) {
		.lightmode.black .th, .lightmode .th, .th, .surprise{background:<?php echo $tc; ?>} 
		#main-menu {background: rgba(28,28,28,0.95);}
	}
</style>
<?php } ?>
<?php $hc = get_option('homegenrecolor'); if($hc) { ?>
<style>
	.home-genres,.home-genres.gennom2 .alman a,.lightmode .home-genres.gennom2{background:<?php echo $hc; ?>;}
	.home-genres .alman a{color:<?php echo $hc; ?>;}
	.home-genres.gennom2{border-color:<?php echo $hc; ?>;}
</style>
<?php } 
$mfloat = get_option('floatmobile'); if($mfloat!=='1') { ?>
	<style>
	@media only screen and (max-width:570px) {
		#floatcenter,#teaser1,#teaser2,#teaserbottom,#teaser4{display:none;}
	}
	</style>
<?php }
}

add_action ('wp_footer','themesiaFooter');
function themesiaFooter(){ ?>
<span class="scrollToTop"><span class="fas fa-angle-up"></span></span>

<?php if(is_home()){ ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
	 centeredSlides: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
	  loop: true,
      pagination: {
        el: '.swiper-pagination',
		clickable: true,
      },
    });
</script>
<?php } ?>

<?php if(get_option('gallerymanga')=='1'){ if(is_singular('manga')){ ?>
<script>
$(document).ready(function() {
	$('.owl-carousel').owlCarousel({
		stagePadding: 50,
		loop:true,
		margin:10,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:4
			},
			1000:{
				items:4
			}
		}
	});
});

	var isGalleryDragging = false;
	$("#gallery").on("mousedown touchstart", function() {
		isGalleryDragging = false;
	}).on("mousemove touchmove", function() {
		isGalleryDragging = true;
	}).on("mouseup touchend", function(event) {
		event.preventDefault();
		var wasGallerDragging = isGalleryDragging;
		isGalleryDragging = false;
		if (!wasGallerDragging) {
			event = event || window.event;
			var target = event.target || event.srcElement;
			var link = target.src ? target.parentNode : target;
			var options = { index: link, event: event };
			var links = this.getElementsByTagName('a');
			blueimp.Gallery(links, options);
		}
	});
	$("#gallery a").on("click", function(){return false;});
</script>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
      <div class="slides"></div>
      <h3 class="title"></h3>
      <span class="prev">‹</span>
      <span class="next">›</span>
      <span class="close">×</span>
      <span class="play-pause"></span>
      <ol class="indicator"></ol>
</div>
<?php } } ?>
<?php 
if(get_option('contentwarning')=='1'){				
	if(is_singular(array('manga','post'))){	
		wp_reset_postdata();
		if(is_singular('manga')){ 
			$srid = get_the_ID(); 
		} else { 
			$srid = get_post_meta(get_the_ID(),'ero_seri',true); 
		}
		if(is_object_in_term($srid,'genres', array('adult','mature','smut'))) { ?>
		<div class="restrictcontainer" data-id="<?php echo $srid;?>">
			<script>ts_restricted_warning.quickHide();</script>
			<div class="restrictcheck">
				<div class="restrictmess">
					<div class="restitle">
						<?php echo GOV_lang::get('series_nsfw_warning')?>
					</div>
					<div class="resdescp">
						<?php echo GOV_lang::get('series_nsfw', ['manga_title' => get_the_title()]);?>
					</div>
					<div class="resconfirm">
						<div class="rescb enterx"><?php echo GOV_lang::get('warning_enter_label')?></div>
						<div class="rescb exitx"><?php echo GOV_lang::get('warning_exit_label')?></div>
					</div>
				</div>
			</div>
		</div>
	<?php } 
	} 
} ?>
<?php floating(); ?>
<script>ts_darkmode.listen();</script>
<?php echo get_option('tsscriptfooter'); ?>

<script>
jQuery.event.special.touchstart = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
    }
};
jQuery.event.special.touchmove = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
    }
};
jQuery.event.special.wheel = {
    setup: function( _, ns, handle ){
        this.addEventListener("wheel", handle, { passive: true });
    }
};
jQuery.event.special.mousewheel = {
    setup: function( _, ns, handle ){
        this.addEventListener("mousewheel", handle, { passive: true });
    }
};
</script>
<?php } 
function ts_list_mode_add_rewrite_rules(){
	$slug = tsrs_get_manga_slug(); if($slug===''){ $slug = 'manga'; }
	add_rewrite_rule("^{$slug}/list-mode/?$", "index.php?themesia-mangalist=true", "top");
}
add_action("init", "ts_list_mode_add_rewrite_rules");

function ts_list_mode_add_var( $query_vars ) {
	$query_vars[] = "themesia-mangalist";
	return $query_vars;
}
add_filter( "query_vars", "ts_list_mode_add_var" );

add_action( 'template_include', 'ts_list_mode_catch_page' );
function ts_list_mode_catch_page($original){
    if($page = get_query_var( 'themesia-mangalist' )){
		remove_filter( 'wp_title', 'filter_wp_title' );
		add_filter('wp_title', function($title){
			return "List Mode - " . get_bloginfo('name');
		},99999);
		add_filter('pre_get_document_title', function($title){
			return "List Mode - " . get_bloginfo('name');
		},99999);
		$_GET['list'] = "list";
		return get_template_directory(). "/archive-manga.php";
    }
	return $original;
}
function tsia_after_body() {
	do_action('tsia_after_body');
}
add_action("tsia_after_body", function(){ ?>
	<script>ts_darkmode.init();</script>
<?php });

add_action( 'pre_get_posts', function( $query ) {
	if(get_option('excludeproject')=='1'){
		if ( $query->is_home() && $query->is_main_query() ) { // Run only on the homepage 
			$query->query_vars['meta_key'] = "ero_project";
			$query->query_vars['meta_value'] = 0;
		}
	}
});

add_filter('manage_post_posts_columns', 'add_ts_views_column');
add_filter('manage_manga_posts_columns', 'add_ts_views_column');
add_action('manage_post_posts_custom_column', 'add_ts_views_column_content');
add_action('manage_manga_posts_custom_column', 'add_ts_views_column_content');
function add_ts_views_column($defaults) {
	$defaults['ts-views'] = __( 'Views', 'ts_post_views' );
	return $defaults;
}

function add_ts_views_column_content($column_name) {
	if ($column_name === 'ts-views' ) {
		echo get_post_meta(get_the_ID(),'wpb_post_views_count',true);
	}
}

add_filter( 'manage_edit-post_sortable_columns', 'sort_ts_view_column');
add_filter( 'manage_edit-manga_sortable_columns', 'sort_ts_view_column' );
function sort_ts_view_column( $defaults ) {
	$defaults['ts-views'] = 'views';
	return $defaults;
}
add_action('pre_get_posts', 'sort_ts_views');
function sort_ts_views($query) {
	if ( ! is_admin() ) {
		return;
	}
	$orderby = $query->get('orderby');
	if ( 'views' === $orderby ) {
		$query->set( 'meta_key', 'wpb_post_views_count' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}

function ts_disable_image_sizes($sizes, $meta, $id) {
		
		$parent = wp_get_post_parent_id( $id );
		if ( ! $parent || ! is_numeric($parent)) return $sizes;
		$parent = get_post($parent);
		if ($parent->post_type != "post") return $sizes;

		if (isset($sizes['thumbnail'])) unset($sizes['thumbnail']);
		if (isset($sizes['medium'])) unset($sizes['medium']);
		if (isset($sizes['large'])) unset($sizes['large']);
		if (isset($sizes['medium_large'])) unset($sizes['medium_large']);
		if (isset($sizes['1536x1536'])) unset($sizes['1536x1536']);
		if (isset($sizes['2048x2048'])) unset($sizes['2048x2048']);

		return $sizes;
}

add_action('intermediate_image_sizes_advanced', 'ts_disable_image_sizes', 10, 3);

function ts_remove_image_dimensions($html) {
    if (class_exists('DOMDocument')) {
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $image->removeAttribute('width');
            $image->removeAttribute('height');
        }

        $html = $dom->saveHTML();
    } else {
        $html = preg_replace('/(width|height)="\d*"\s/', '', $html);
    }

    return $html;
}

add_filter('image_send_to_editor', 'ts_remove_image_dimensions', 10);

ts_core_hooks_default();

ts_core_hooks_ole();

ts_core_hooks_one();