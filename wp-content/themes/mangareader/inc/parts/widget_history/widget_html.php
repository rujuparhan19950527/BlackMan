<?php defined("ABSPATH") || die("!");
extract($args); 
$name = isset($instance['name']) ? $instance['name'] : '';
$name = strip_tags($name);
$name = apply_filters('widget_title', $name);
?>

<?php echo $before_widget; ?>

<?php if (!empty($name)) {
    echo $before_title . $name . $after_title;
} ?>

<div class="textwidget custom-html-widget">
<div id="theHISTORY"></div>
</div>

<?php echo $after_widget; ?>
