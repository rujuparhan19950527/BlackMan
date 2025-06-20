<div class='socialts'>
	<a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>&t=<?php the_title(); ?>" target="_blank" class="fb" aria-label="Share on Facebook">
		<i class="fab fa-facebook-f"></i>
		<span>Facebook</span>
	</a>
	<a href="https://www.twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" class="twt" aria-label="Share on Twitter">
		<i class="fab fa-twitter"></i>
		<span>Twitter</span>
	</a>
	<a href="whatsapp://send?text=<?php the_title(); ?> <?php the_permalink(); ?>" target="_blank" class="wa" aria-label="Share on WhatsApp">
		<i class="fab fa-whatsapp"></i>
		<span>WhatsApp</span>
	</a>
	<a href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo get_the_post_thumbnail_url(); ?>&description=<?php the_title(); ?>" target="_blank" class="pntrs" aria-label="Share on Pinterest">
		<i class="fab fa-pinterest-p"></i>
		<span>Pinterest</span>
	</a>
	<a href="https://t.me/share/url?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" class="tlg" aria-label="Share on Telegram">
		<i class="fab fa-telegram-plane"></i>
		<span>Telegram</span>
	</a>
</div>