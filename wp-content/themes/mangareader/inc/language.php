<?php defined("ABSPATH") || die("!");
function gov_language(){
	return [
		//general
		"comment_label" => "แสดงความเห็น",
		"recommended_series" => "แนะนำมังงะเรื่องอื่นๆ",
		"wpb_get_post_views_label" => "{{count}} Views",
		"view_all_label" => "ดูทั้งหมด",
		"previous" => "ก่อนหน้า",
		"next" => "ถัดไป",
		"surprise_me_label" => "Surprise Me!",
		"ago" => "ก่อน",
		//home 
		"home_hot_series_update_label" => "มังงะใหม่ยอดฮิต",
		"home_project_release_label" => "Project Update",
		"home_blog_release_label" => "บทความ",
		"home_latest_release_label" => "มังงะอัพเดทล่าสุด",
		"mini_ch" => "ตอนที่ ",
		//home slider
		"home_slider_trending_label" => "{{blogname}} <b>ยอดนิยม</b> ประจำสัปดาห์",
		"home_slider_summary_label" => "Summary",
		"home_recommended" => "แนะนำ",
		//bookmark
		"bookmark_head" => "คุณสามารถบันทึกรายเพื่ออ่านย้อนหลังได้ถึง {{max}} เรื่อง รายชื่อจะถูกเก็บไว้ในเครื่องที่คุณใช้งานอยู่เท่านั้น!!",
		"bookmark_no_item" => "ยังไม่มีรายการมังงะที่ถูกบันทึก",
		"delete" =>  "ลบ",

		//chapter single page 
		"chapter_to_chapter_list_link" => 'อ่านมังงะเรื่อง <a href="{{link}}">{{text}}</a> แปลไทย ทุกตอน อัพเดทตอนล่าสุด',
		"chapter_before_readerarea_text" => 'อ่านมังงะเรื่อง <b> {{chapter_title}} แปลไทย</b> อัพเดทล่าสุดที่ <b> {{blogname}} </b> และติดตามอัพเดทมังงะตอนล่าสุดได้ที่ <b> {{blogname}} </b> เว็บอ่านการ์ตูนมังงะ 2025 ภาพชัด โหลดไว อ่านฟรี ไม่มีโฆษณา',
		//chapter single page dropdown chapter selection
		"chapter_drop_down_select_chapter_label" => "เลือกตอน",

		"chapter_download_label" => "Download",
		"chapter_nav_previous_label" => "ก่อนหน้า",
		"chapter_nav_next_label" => "ถัดไป",
		"chapter_after_readerarea_tags" => 'อ่านมังงะ {{chapter_title}}, อ่านการ์ตูน {{chapter_title}} แปลไทย, อ่าน {{chapter_title}} ออนไลน์, มังงะภาพชัด โหลดไว อัพเดทครบทุกตอน ไม่มีโฆษณา',
		"chapter_after_readerarea_novel_tags" => 'read novel {{chapter_title}}, novel {{chapter_title}}, read {{chapter_title}} online, {{chapter_title}} chapter, {{chapter_title}} high quality, {{chapter_title}} light novel',
		//chapter single page, reader area 
		"chapter_readerarea_no_images" => '<center><h4>ยังไม่มีรูปภาพ</h4></center>',
		"chapter_readerarea_default_server_label" => "Server 1",

		//series single page
		"series_info_alt_label" => "ชื่ออื่น",
		"series_info_synopsis_label" => 'เรื่องย่อ {{post_title}}',
		"series_info_genres_label" => "หมวดหมู่",
		"series_info_author_label" => "นักเขียน",
		"series_info_artist_label" => "นักวาด",
		"series_info_posted_on_label" => "เผยแพร่เมื่อ",
		"series_info_status_label" => "สถานะ",
		"series_info_released_label" => "วันที่ปล่อย",
		"series_info_type_label" => "ประเภท",
		"series_info_posted_by_label" => "อัพเดทโดย",
		"series_info_updated_on_label" => "อัพเดทเมื่อ",
		"series_info_serialization_label" => "Serialization",
		"series_info_rating_label" => "คะแนน {{rating}}",
		"series_bottom_keyword_text" => '<strong>Keywords: </strong> <a href="{{link}}">read {{post_title}}</a>, <a href="{{link}}">{{post_title}} english</a>, <a href="{{link}}">{{post_title}} eng sub</a>, <a href="{{link}}">download {{post_title}} eng sub</a>, <a href="{{link}}">streaming {{post_title}}</a>',
		"series_bookmarked_by" => "กำลังติดตาม {{count}} คน",
		"series_keywords_text" => "read {{manga_title}}, {{manga_title}} english, {{manga_title}} eng, download {{manga_title}} eng, read {{manga_title}} online",
		"series_nsfw" => "Warning, the series titled \"{{manga_title}}\" may contain violence, blood or sexual content that is not appropriate for minors.",
		"series_gallery_label" => "Gallery {{manga_title}}",
		"series_chapter_list" => "มังงะ {{manga_title}} แปลไทย ครบทุกตอน",
		"series_read_button_label" => "อ่าน",
		"widget_chapter_label" => "ตอนที่",
		"search_chapter_placeholder" => "ใส่เลขเพื่อหาตอน เช่น: 1 หรือ 20",
		"series_chapter_number" => "ตอนที่",
		"series_chapter_date" => "Date",
		"series_chapter_download" => "Download",
		"series_first_chapter_label" => "ตอนแรก",
		"series_new_chapter_label" => "ตอนล่าสุด",
		"series_chapter_search_no_result" => "ไม่มีตอน",

		//blog 
		"blog_meta" => "เผยแพร่โดย {{author}} เมื่อ {{time}}",

		//advanced search series
		"filter_all_label" => "ทั้งหมด",
		"filter_default_label" => "ปกติ",
		"advanced_search_manga_list_label" => "Manga Lists",
		"advanced_search_filter_button_label" => "Filter",
		"advanced_search_series_title_label" => "Title",
		"advanced_search_series_year_label" => "Year",
		"advanced_search_series_status_label" => "Status",
		"advanced_search_series_status_all_label" => "All",
		"advanced_search_series_status_ongoing_label" => "Ongoing",
		"advanced_search_series_status_completed_label" => "Completed",
		"advanced_search_series_type_label" => "Type",
		"advanced_search_series_type_all_label" => "All",
		"advanced_search_series_order_by_label" => "Order by",
		"advanced_search_series_order_by_az_label" => "A-Z",
		"advanced_search_series_order_by_za_label" => "Z-A",
		"advanced_search_series_order_by_latest_update_label" => "Update",
		"advanced_search_series_order_by_latest_added_label" => "Added",
		"advanced_search_series_order_by_popular_label" => "Popular",
		"advanced_search_series_genre_label" => "Genre",
		"advanced_search_series_show_genre_label" => "Show Genre",
		"advanced_search_series_text_mode_label" => "Text Mode",
		"advanced_search_series_image_mode_label" => "Image Mode",
		"advanced_search_series_search_button_label" => "ค้นหา",
		
		//widget
		"widget_popular_weekly" => "รายสัปดาห์",
		"widget_popular_monthly" => "รายเดือน",
		"widget_popular_alltime" => "ตลอดกาล",

		//footer 
		"footer_az_heading" => "รายชื่อมังงะ A-Z",
		"footer_az_text" => "ค้นหามังงะตามอักษร A ถึง Z",
		"footer_disclaimer" => "Copyright © 2025 Manga-Neko All rights reserved",
		
		"search_page_title" => "ผลการค้นหา '{{title}}'",
		"search_page_notfound" => "ไม่พบมังงะเรื่องที่ต้องการ",
		"search_placeholder" => "ค้นหา",
		"series_info_keywords_label" => "Keywords: ",
		"select_chapter_label" => "ตอนที่",
		"home_genre_label" => "มังงะทั้งหมด",
		"thumbnail_color_label" => "Color",
		"darkmode_label" => "Dark?",
		"related_blog_label" => "Recommendations",
		"reading_mode_full_label" => "อ่านแบบยาว",
		"reading_mode_single_label" => "อ่านทีละหน้า",
		"reading_nav_next_label" => "ถัดไป",
		"reading_nav_prev_label" => "ก่อนหน้า",
		"readerarea_tags_label" => "Tags:",
		"thumbnail_novel_label" => "นิยาย",
		"seriestu_first_chapter_label" => "อ่านตอนแรก :",
		"seriestu_new_chapter_label" => "อ่านตอนล่าสุด :",
		
		//2.1.1
		"warning_enter_label" => "เข้า",
		"warning_exit_label" => "ออก",
		"widget_genre_title_view_label" => "View all series in",
		"series_info_additional_label" => "เนื้อหาเพิ่มเติม",
		'series_history_title' => "อ่านล่าสุด",
		'series_history_prefixAgo' => "",
		'series_history_prefixFromNow' => "",
		'series_history_suffixAgo' => "ก่อน",
		'series_history_suffixFromNow' => "ตอนนี้",
		'series_history_seconds' => "น้อยกว่าหนึ่งนาที",
		'series_history_minute' => "ประมาณหนึ่งนาที",
		'series_history_minutes' => "{{num}} นาที",
		'series_history_hour' => "ประมาณชั่วโมง",
		'series_history_hours' => "ประมาณ {{num}} ชั่วโมง",
		'series_history_day' => "ประมาณวัน",
		'series_history_days' => "{{num}} วัน",
		'series_history_month' => "ประมาณเดือน",
		'series_history_months' => "{{num}} เดือน",
		'series_history_year' => "ประมาณปี",
		'series_history_years' => "{{num}} ปี",
		
		//2.1.2
		'bookmark_disabled' => "BOOKMARK FEATURE IS DISABLED",

		//2.1.4
		"series_nsfw_warning" => "Content Warning",
		"slider_score" => "Score",
		"slider_start_reading" => "เริ่มอ่าน",
		"view_all_bottom_label" => "ดูทั้งหมด {{title}}",

		//2.1.5
		"series_info_views_label" => "คนดู",

		//2.1.6
		"front_homepage_label" => "ไปยังหน้าแรก",
		"views_thousand_label" => "K",
		"views_million_label" => "M",
		"views_billion_label" => "B",
		"views_trillion_label" => "T",
		"views_quadrillion_label" => "Q",

		//2.1.7
		"series_archive_page_no_result" => "-- No Post Found --",
	];
};