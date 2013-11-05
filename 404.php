<?php
/**
 * The template for displaying 404 pages (Not Found).
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
        <article id="post-not-found" class="clearfix">
            <header>
                <h1>Epic 404 - Article Not Found</h1>
            </header>
            <section class="post_content">
                <p>The article you were looking for was not found, but maybe try looking again!</p>
            </section>
        </article>
    </div>
    <?php if ($layout == 'content-sidebar' || $layout == 'sidebar-content'): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
</div>
<?php get_footer(); ?>