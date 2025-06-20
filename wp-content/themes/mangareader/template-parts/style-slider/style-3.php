<?php
$ep = GOV_manga::get_latest_chapters(get_the_ID());
$latest = ltsc($ep)['chapter'];
?>
<div class="swiper-slide">
		<div class="mainslider">
			<div class="limit">
				<div class="sliderinfo">
					<div class="sliderinfolimit">
						<?php if($latest) { ?>
						<div class="slidlc">
							<?php echo GOV_lang::get('widget_chapter_label'); ?>: <?php echo $latest; ?>
						</div>
						<?php } ?>
						<a href="<?php the_permalink(); ?>">
							<span class="name"><?php the_title(); ?></span>
							<div class="desc"><?php the_excerpt(); ?></div>
						</a>
						<div class="meta">
							<?php echo get_the_term_list( get_the_ID(), 'genres', '<div class="metas-slider-genres"><span class="metas-genres-values">', ' ', '</span></div>' ); ?>
						</div>
						<div class="start-reading">
							<a href="<?php the_permalink(); ?>">
								<span><?php echo GOV_lang::get('slider_start_reading')?> <i class="fas fa-long-arrow-alt-right"></i></span>
							</a>
						</div>
					</div>
				</div>
				<div class="slidtrithumb">
					<?php echo the_post_thumbnail('full', array('title'=>get_the_title(),'alt'=>get_the_title())); ?>
				</div>
				<?php $images = rwmb_meta( 'ero_cover',array( 'size' => 'full' ) ); 
				if ( !empty( $images ) ) {
				foreach ( $images as $image ) { ?>
					<div class="bigbanner" style="background-image: url('<?php echo esc_url( $image['url'] ); ?>');"></div>
				<?php }
			} else { ?>
					<div class="bigbanner img-blur" style="background-image: url('<?php echo get_the_post_thumbnail_url(); ?>');"></div>
			<?php } ?>
			</div>
		</div>
</div>