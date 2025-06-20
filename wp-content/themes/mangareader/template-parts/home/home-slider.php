<?php
GOV_cache::fragment_cache("home_slider", 86400, function () {
$gpt = get_option('sliderx');
	if ($gpt) { 
		$sliderstyle = get_option('tsliderstyle');
			if ($sliderstyle == '') {
				$sliderstyle = '1';
			} 
	$featured = new WP_Query( 
			array( 
				'post_type' => 'manga',
				'showposts' => $gpt,
				'meta_key' => 'ero_slider',
				'meta_value' => 1,
				'orderby' =>  'rand',
				'no_found_rows' => 'true',
				'ignore_sticky_posts' => 1
			) ); if ($featured->have_posts()) : ?>
		<div class="slider-wrapper wrapernum<?php echo $sliderstyle; ?>">
			<div class="big-slider swiper-container slidernom<?php echo $sliderstyle; ?>">
				<div class="swiper-wrapper">
				<?php
				while ( $featured->have_posts() ) : $featured->the_post();
					get_template_part('template-parts/style-slider/style', $sliderstyle);
				endwhile; ?>
				</div>
				<div class="paging">
					<div class="centerpaging">
						<div class="swiper-pagination"></div>
					</div>
				</div>
			</div>
			<?php
			$gpt = get_option('homegenre');if ($gpt) { 
				$slugseries = tsrs_get_manga_slug(); 
				if( ! $slugseries){ 
					$slugseries = 'manga'; 
				} ?>
			<div class="home-genres gennom<?php echo $sliderstyle; ?>">
				<span class="genre-listx">
					<?php
						$taxonomy = 'genres';
						$tax_terms = get_terms($taxonomy, 'number=8');
						foreach ($tax_terms as $tax_term) {
							echo '<a href="' . esc_attr(get_term_link($tax_term, $taxonomy)) . '" title="' . sprintf(__("View all series in %s"), $tax_term->name) . '" ' . '>' . $tax_term->name . '</a>';
						}
					?>
				</span>
				<span class="alman">
					<a href="<?php echo home_url(); ?>/<?php echo $slugseries; ?>"><?php echo GOV_lang::get('home_genre_label')?></a>
				</span>
			</div>
<?php } echo '</div>'; endif; } } ); ?>