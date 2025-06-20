<div class="swiper-slide">
	<a href="<?php the_permalink(); ?>">
		<div class="mainslider">
			<div class="limit">
				<div class="sliderinfo">
					<div class="sliderinfolimit">
						<span class="name"><?php the_title(); ?></span>
						<div class="meta">
							<?php $score = rwmb_get_value( 'ero_score' ); if($score){ ?>
								<span class="quality">
									<?php echo $score; ?>
								</span>
							<?php } ?>
							<?php $meta = rwmb_get_value( 'ero_type' ); if($meta){ ?>
								<span class="text">
									<?php echo GOV_lang::get('series_info_type_label')?>: <b><?php rwmb_the_value('ero_type'); ?></b>
								</span>
							<?php } ?>
							<span class="text"><?php echo GOV_lang::get('series_info_genres_label'); ?>: <?php echo strip_tags(get_the_term_list(get_the_ID(), 'genres', '', ', ', '')); ?></span>
						</div>
						<div class="desc"><?php the_excerpt(); ?></div>
					</div>
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
	</a>
</div>