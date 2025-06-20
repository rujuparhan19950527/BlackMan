<?php
$serieslatest = GOV_manga::get_latest_chapters(get_the_ID());
$type = rwmb_get_value( 'ero_type' );
$st = rwmb_get_value( 'ero_status' );
$colored = rwmb_get_value( 'ero_colored' );
$score = rwmb_get_value( 'ero_score' );
$ctr = get_option('tstypelabel');
$colored = rwmb_get_value( 'ero_colored' );
$status = rwmb_get_value( 'ero_status' );
?>
<div class="stylesven">
	<div class="sveninner">
		<div class="sventop">
			<a rel="<?php the_id(); ?>" class="series" href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
				<div class="sventhumb">
					<?php echo GOV_manga::get_post_thumbnail(get_the_ID(),'medium', 145, 100,array('title'=>get_the_title(),'alt'=>get_the_title())); ?>
				</div>
				<div class="sveninfo">
					<div class="sventitle">
						<h4><?php the_title(); ?></h4>
					</div>
					<div class="svendetop">
						<?php if($score){ echo '<div class="svenmeta"><i class="fas fa-star"></i> '.$score.'</div>'; } ?>
						<div class="svenmeta">
							<?php if($type!=='Novel'){ if($ctr=='1'){ ?><span class="type <?php echo $type; ?>"></span><?php } } ?>
							<?php rwmb_the_value('ero_type'); ?>
						</div>
						<?php 
						if($type!=='Novel') {
							if($colored=='1'){ ?>
								<div class="svenmeta"><i class="fas fa-palette"></i> <?php echo GOV_lang::get('thumbnail_color_label'); ?></div>
							<?php } else if($colored!=='0'){
							if($type!=='Manga'){ ?>
								<div class="svenmeta"><i class="fas fa-palette"></i> <?php echo GOV_lang::get('thumbnail_color_label'); ?></div>
							<?php } 
							} 
						}
						?>
					</div>
				</div>
			</a>
		</div>
		<div class="svenbottom">
			<div class="svenmetabot">
				<span class="statusind <?php echo $status; ?>"><i class="fas fa-circle"></i> <?php rwmb_the_value('ero_status'); ?></span>
				<?php if($serieslatest){
					echo '<ul class="svenchapters '.$type.'">';
					$serieslatest = is_array($serieslatest)?$serieslatest:[];
					foreach ($serieslatest as $key => $value) {
						echo '<li><a href="'.chapter_url($value['permalink']).'">'.GOV_lang::get('mini_ch').' '.$value['chapter'].'</a><span>'.human_time_diff($value['time'], current_time('timestamp')) . " ".GOV_lang::get('ago').'</span></li>';
					}
					echo '</ul>';
				} ?>
			</div>
		</div>
	</div>
</div>