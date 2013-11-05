<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
?>
<?php get_header(); ?>
<?php
$options = be_get_theme_options();
$layout = $options['theme_layout'];
?>
<div class="site-content <?php echo $layout; ?>">
    <div id="main" role="main">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php get_template_part('content'); ?>
            <?php endwhile; ?>
            <?php be_content_nav('nav-below'); ?>
        <?php else : ?>
            <?php get_template_part('content', 'missing'); ?>
        <?php endif; ?>
    </div>
    <?php if ($layout == 'content-sidebar' || $layout == 'sidebar-content'): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
</div>
<?php get_footer(); ?>