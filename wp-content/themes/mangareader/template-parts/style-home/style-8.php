<div class="utao styletwo stylegg">
					<div class="uta">
						<div class="imgu">
							<a rel="<?php the_id(); ?>" class="series" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo GOV_manga::get_post_thumbnail(get_the_ID(),'medium', 145, 100,array('title'=>get_the_title(),'alt'=>get_the_title())); ?>
							<?php 
								$pyear = get_post_meta(get_the_ID(),'ero_published',true);
								$serieslatest = GOV_manga::get_latest_chapters(get_the_ID());
								$type = get_post_meta(get_the_ID(),'ero_type',true);
								$status = get_post_meta(get_the_ID(),'ero_status',true);
								$istoday = get_the_modified_date('Ymd')===date('Ymd');
								if($istoday){
									$isnew = $pyear==date('Y') || (is_array($serieslatest) && sizeof($serieslatest)<=2);
								} else {
									$isnew = 0;
								}
								if($isnew){ echo '<span class="new">N</span>'; }
							?>
							<?php 
							if($type!=='Novel'){
								$ctr = get_option('tstypelabel'); 
								if($ctr=='1'){ ?>
									<span class="type <?php echo $type; ?>"></span>
								<?php } else if($ctr=='2'){ ?>
									<span class="typename <?php echo $type; ?>"><?php rwmb_the_value('ero_type'); ?></span>
							<?php } } ?>
							<?php $cht = get_option('tshotlabel'); if($cht){ $hot = rwmb_get_value( 'ero_hot' ); if($hot){ ?><span class="hotx"><i class="fab fa-hotjar"></i></span><?php } } ?>
							<?php if($type=='Novel') { ?><span class="novelabel"><i class="fas fa-book"></i> <?php echo GOV_lang::get('thumbnail_novel_label'); ?></span><?php } ?>
							</a>
						</div>
						<div class="luf">
							<a class="series" href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
								<h4><?php the_title(); ?></h4>
							</a>
							<?php if($serieslatest){
								echo '<ul class="'.$type.'">';
								$serieslatest = is_array($serieslatest)?$serieslatest:[];
								$serieslatest = array_slice($serieslatest, 0, 2);
								foreach ($serieslatest as $key => $value) {
									echo '<li><a href="'.chapter_url($value['permalink']).'"><span class="eggchap">'.GOV_lang::get('widget_chapter_label').' '.$value['chapter'].'</span><span class="eggtime">'.human_time_diff($value['time'], current_time('timestamp')) . " ".GOV_lang::get('ago').'</span></a></li>';
								}
								echo '</ul>';
							} ?>
							<span class="statusind <?php echo $status; ?>"><i class="fas fa-circle"></i> <?php rwmb_the_value('ero_status'); ?></span>
						</div>
					</div>
				</div>