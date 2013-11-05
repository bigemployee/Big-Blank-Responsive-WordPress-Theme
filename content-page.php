<?php
/**
 * The default template for displaying page content
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php
        if (has_post_thumbnail()):
            the_post_thumbnail();
        endif;
        ?>
    </header>
    <section class="entry-content">
        <?php the_content(); ?>
        <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Pages:', 'betheme'), 'after' => '</div>', 'pagelink' => '<span>%</span>')); ?>
    </section>
</article>