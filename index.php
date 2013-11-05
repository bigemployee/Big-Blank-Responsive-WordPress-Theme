<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage Blank
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