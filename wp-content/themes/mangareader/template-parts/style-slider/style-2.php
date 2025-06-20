<div class="swiper-slide">
		<div class="mainslider">
			<div class="limit">
				<div class="sliderinfo">
					<div class="sliderinfolimit">
						<a href="<?php the_permalink(); ?>">
							<span class="name"><?php the_title(); ?></span>
						</a>
						<div class="meta">
							<?php $score = rwmb_get_value( 'ero_score' ); if($score){ ?>
							<div class="metas-slider-score">
								<span class="text">
									<?php echo GOV_lang::get('slider_score')?>
								</span>
								<span class="meta-score-values">
									<?php echo $score; ?>
								</span>
							</div>
							<?php } ?>
							<?php $meta = rwmb_get_value( 'ero_type' ); if($meta){ ?>
							<div class="metas-slider-type">
								<span class="text">
									<?php echo GOV_lang::get('series_info_type_label')?>
								</span>
								<span class="meta-type-values <?php echo $meta; ?>">
									<?php rwmb_the_value('ero_type'); ?>
								</span>
							</div>
							<?php } ?>
							<div class="clear"></div>
							<?php echo get_the_term_list( get_the_ID(), 'genres', '<div class="metas-slider-genres"><span class="metas-genres-values">', ' ', '</span></div>' ); ?>
						</div>
						<div class="desc"><?php the_excerpt(); ?></div>
						<div class="start-reading">
							<a href="<?php the_permalink(); ?>">
								<span><?php echo GOV_lang::get('slider_start_reading')?> <i class="fas fa-long-arrow-alt-right"></i></span>
							</a>
						</div>
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
</div>