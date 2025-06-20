<?php 
/*
Template Name: Front
*/
defined("ABSPATH") || die("!");
get_header(); 
?>

<div class="tsfront">
	<div class="headfront">
		<div class="fsearch">
			<form action="<?php bloginfo('url'); ?>/" id="form" method="get" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
				<meta itemprop="target" content="<?php bloginfo('url'); ?>/?s={query}"/>
				<input id="s" itemprop="query-input" type="text" placeholder="<?php echo GOV_lang::get('search_placeholder')?>" name="s" autocomplete="off"/>
				<button type="submit" id="submit"><i class="fas fa-search" aria-hidden="true"></i></button>
			</form>
		</div>
		<div class="fdesc">
			<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?>
		</div>
		<?php 
		$staticpage = get_option( 'page_for_posts' );
		if($staticpage){ ?>
			<div class="fhomebutton">
				<a href="<?php echo get_permalink($staticpage); ?>"><?php echo GOV_lang::get('front_homepage_label');?></a>
			</div>
		<?php } ?>
	</div>
	<div class="frontcontainer">     
		<?php if (have_posts()) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
	</div>
</div>

<?php get_footer(); ?>