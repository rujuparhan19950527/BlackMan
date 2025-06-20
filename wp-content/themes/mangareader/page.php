<?php get_header(); ?>

<div class="postbody">
	<div class="bixbox">
		<?php if (have_posts()) : ?>
		<div class="releases"><h1><span><?php the_title(); ?></span></h1></div>
		<div class="page">
			<?php while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
		</div>
	</div>
	<?php if(comments_open()){ ?>
	<div id="comments" class="bixbox comments-area">
		<div class="releases"><h2><span><?php echo GOV_lang::get('comment_label');?></span></h2></div>
		<div class="cmt commentx">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<?php echo get_post_meta(get_the_ID(), "manga", true); ?>
				<?php comments_template(); ?>
			<?php endwhile; endif; ?>
		</div>
	</div>
	<?php } ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>