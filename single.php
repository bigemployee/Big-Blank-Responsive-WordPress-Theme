<?php
/**
 * The Template for displaying all single posts.
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
?>
<?php get_header(); ?>
<?php
$layout = get_post_meta(get_the_ID(), 'be_post_layout', true);
if (!$layout) {
    $options = be_get_theme_options();
    $layout = $options['theme_layout'];
}
?>
<div class="site-content <?php echo $layout; ?>">
    <div id="main">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php get_template_part('content', 'single'); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <?php get_template_part('content', 'missing'); ?>
        <?php endif; ?>
    </div>
    <?php if ($layout == 'content-sidebar' || $layout == 'sidebar-content'): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
</div>
<?php get_footer(); ?>