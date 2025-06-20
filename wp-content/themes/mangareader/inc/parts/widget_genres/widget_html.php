<?php defined("ABSPATH") || die("!");?>
<li>
    <a href="<?php echo esc_attr(get_term_link($tax_item, $taxonomy)); ?>" title="<?php echo esc_attr(GOV_lang::get('widget_genre_title_view_label')); ?> <?php echo esc_attr($tax_item->name); ?>"><?php echo esc_html($tax_item->name); ?></a>
</li>