<?php
defined("ABSPATH") || die("!");
$gpt = get_option('tlatestepisode'); if($gpt){ 
$style = get_option('stylelatest');
if($style==''){
	$style='1';
}
$slugseries = tsrs_get_manga_slug(); 
if( ! $slugseries){ 
	$slugseries = 'manga'; 
}
$home_page_num = get_query_var("paged")?:1;
$staticpage = get_option( 'page_for_posts' );
?>
<?php $kln = get_option('tople'); if($kln) { ?><div class="kln"><?php echo $kln; ?></div><br/><?php } ?>
<div class="bixbox">
	<?php if ($wp_query->have_posts()) : ?>
	<div class="releases"><h2><?php echo GOV_lang::get('home_latest_release_label');?></h2><a class="vl" href="<?php bloginfo('url'); ?>/<?php echo $slugseries; ?>/?order=update"><?php echo GOV_lang::get('view_all_label');?></a></div>
	<div class="listupd<?php if($style=='7' || $style=='8'){ echo ' stsven'; } ?>">
		<?php 
				while($wp_query->have_posts()) : 
					$wp_query->the_post(); 
					get_template_part('template-parts/style-home/style', $style); 
				endwhile; ?>
			<div class="hpage">
			<?php if($wp_query->post_count && $home_page_num>1){ ?>
					<a href="<?php if($staticpage) { echo get_permalink($staticpage).'page'; } else { echo home_url('page'); } ?>/<?php echo $home_page_num-1; ?>" class="l"><i class="fa fa-chevron-left" aria-hidden="true"></i> <?php echo GOV_lang::get('previous');?></a>
				<?php } ?>
				<?php if($wp_query->post_count>=$gpt){ ?>
					<a href="<?php if($staticpage) { echo get_permalink($staticpage).'page'; } else { echo home_url('page'); } ?>/<?php echo $home_page_num+1; ?>" class="r"><?php echo GOV_lang::get('next');?> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
			<?php } ?>
			</div>
	</div>
	 <?php endif; ?>
</div>
<?php } ?>