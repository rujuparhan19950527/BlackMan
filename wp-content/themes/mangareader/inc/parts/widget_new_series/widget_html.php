<?php defined("ABSPATH") || die("!");?>
<li>
    <div class="imgseries">
        <a class="series" href="<?php the_permalink()?>" rel="<?php the_ID();?>"><?php echo $swi_thumbnail; ?></a>
    </div>
    <div class="leftseries">
        <h2>
            <a class="series" href="<?php the_permalink()?>" rel="<?php the_ID();?>"><?php the_title();?></a>
        </h2>
        <?php echo $swi_genres; ?>
        <?php if ($swi_author) { ?>
            <span><?php echo $swi_author; ?></span>
        <?php } ?>
    </div>
</li>