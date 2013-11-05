<?php
/**
 * The template for displaying Search Results pages.
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
        <div><span>Search Results for:</span> <?php echo esc_attr(get_search_query()); ?></div>

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