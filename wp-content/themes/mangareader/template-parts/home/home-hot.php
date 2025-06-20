<?php $gpt = get_option('thotupdate'); if($gpt){ ?>
<h2 style="font-size: 18px; color: #f79c13; text-align: left; margin-left: 10%; margin-right: 10%; margin: 0; padding-bottom: 0.4em;"> อ่านการ์ตูนแปลไทย </h2>
<p style= "text-align: left; color: #ffffff; margin-left: 10%; margin-right: 10%; margin: 0; padding-bottom: 0.4em;">เว็บอ่านการ์ตูนแปลไทยฟรี และอ่านมังงะแปลไทยออนไลน์ ล่าสุดในปี 2025 ยอดนิยมอันดับ 1 ที่มีการคัดสรรมังงะมากมายทุกรูปแบบทุกแนวไม่ว่าจะเป็นมังงะจาก Webtoon หรือ Comico รวมถึง MangaPlus ก็มีมาให้เลือกรับชมกันอย่างจุใจ ไม่ต้องเสียเงินอ่าน ไม่ต้องเติมเงินเพื่ออ่านหรือใช้เหรียญในการอ่าน มีระบบทันสมัยล่าสุด จดจำตอนมังงะที่อ่านไว้ สามารถกลับมาอ่านตอนต่อได้แบบฟินๆ และยังสามารถเก็บการ์ตูนโปรดไว้กลับมาอ่านได้อีกด้วย รวมถึงยังมีมังงะจีน มังงะเกาหลี แปลไทย ภาพความละเอียดสูง คมชัด โหลดไว อ่านได้บนอุปกรณ์ทุกรุ่น ไม่ว่าจะ PC มือถือ หรือ แท็บเลต ก็อ่านกันได้ง่ายๆ</p>
<div class="hotslid">
<div class="bixbox hothome full">
		<div class="releases">
			<h2><?php echo GOV_lang::get('home_hot_series_update_label'); ?></h2>
		</div>
		<div class="listupd popularslider">
			<div class="popconslide">
				<?php 
				$featured = new WP_Query(array(
					'post_status' => 'publish',
					'post_type' => 'manga',
					'meta_key' => 'ts_today_view_count',
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					"showposts"     => $gpt,
					"ignore_sticky_posts" => 1,
					"no_found_rows"  => true,
					"update_post_term_cache" => false,
				)); 
				while($featured->have_posts()) : 
					$featured->the_post(); 
					get_template_part('template-parts/general/main'); 
				endwhile; ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>