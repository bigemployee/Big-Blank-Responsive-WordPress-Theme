<?php
/**
 * The template for displaying image attachments.
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
    <div id="main">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><a href="<?php echo get_permalink($post->post_parent); ?>"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h1>
                    </header>
                    <section class="entry-content">
                        <p class="attachment"><a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image($post->ID, 'medium'); ?></a></p>
                        <p class="caption"><?php if (!empty($post->post_excerpt)) the_excerpt(); // this is the "caption"      ?></p>
                        <?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
                    </section>
                    <footer>
                        <nav class="prev-next-links">
                            <ul>
                                <li><?php previous_image_link() ?></li>
                                <li><?php next_image_link() ?></li>
                            </ul>
                        </nav>
                    </footer>
                </article>
                <?php
            endwhile;
        else:
            ?>
            <div class="help">
                <p>Sorry, no attachments matched your criteria.</p>
            </div>
        <?php endif; ?>
    </div> <!-- end #main -->
    <?php if ($layout == 'content-sidebar' || $layout == 'sidebar-content'): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
</div>
<?php get_footer(); ?>