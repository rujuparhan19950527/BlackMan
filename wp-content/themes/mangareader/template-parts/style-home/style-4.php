<?php 
$ep = GOV_manga::get_latest_chapters(get_the_ID());
$latest = ltsc($ep)['chapter'];
if ($latest == "") $latest = " ?";
$latestid = ltsc($ep)['id'];
if ($latestid == "") $latestid = "";
if(!is_home()){
	$latestid = get_the_ID();
}
$ldate = ltsc($ep)['time'];
if ($ldate == "") $ldate = get_the_time('U',get_the_ID());
$type = rwmb_get_value( 'ero_type' ); 
$st = rwmb_get_value( 'ero_status' ); 
$colored = rwmb_get_value( 'ero_colored' );
?>
<div class="bs styletere stylefor">
	<div class="bsx">
		
		<a href="<?php echo get_permalink($latestid); ?>" title="<?php echo get_the_title($latestid); ?>">
		<div class="limit">
			<div class="ply"></div>
			<?php $sts = get_option('tscompletelabel'); if($sts){ if($st=='Completed'){ ?><span class="status <?php echo $st; ?>"><?php rwmb_the_value('ero_status'); ?></span><?php } } ?>
			<?php 
			if($type!=='Novel'){
				$ctr = get_option('tstypelabel'); 
				if($ctr=='1'){ ?>
					<span class="type <?php echo $type; ?>"></span>
				<?php } else if($ctr=='2'){ ?>
					<span class="typename <?php echo $type; ?>"><?php rwmb_the_value('ero_type'); ?></span>
			<?php } } ?>
			<?php 
			$clr = get_option('tscolorlabel'); 
			if($clr){
				if($type!=='Novel') {
					if($colored=='1'){ ?>
						<span class="colored"><i class="fas fa-palette"></i> <?php echo GOV_lang::get('thumbnail_color_label'); ?></span>
					<?php } else if($colored!=='0'){
					if($type!=='Manga'){ ?>
						<span class="colored"><i class="fas fa-palette"></i> <?php echo GOV_lang::get('thumbnail_color_label'); ?></span>
					<?php } } } 
			} ?>
			<?php if($type=='Novel') { ?><span class="novelabel"><i class="fas fa-book"></i> <?php echo GOV_lang::get('thumbnail_novel_label'); ?></span><?php } ?>
			<?php $cht = get_option('tshotlabel'); if($cht){ $hot = rwmb_get_value( 'ero_hot' ); if($hot){ ?><span class="hotx"><i class="fab fa-hotjar"></i></span><?php } } ?>
			<?php echo GOV_manga::get_post_thumbnail(get_the_ID(),'medium', 225, 165,array('title'=>get_the_title($latestid),'alt'=>get_the_title($latestid))); ?>
		</div>
		<div class="bigor">
			<div class="tt">
				<?php the_title(); ?>
			</div>
			<div class="epxdate"><?php echo human_time_diff($ldate, current_time('timestamp')) . " ".GOV_lang::get('ago'); ?></div>
			<div class="adds">
				<div class="epxs"><?php echo GOV_lang::get('widget_chapter_label'); ?> <?php echo $latest; ?></div>
			</div>
		</div>
		</a>
	</div>
</div>