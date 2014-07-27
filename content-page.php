<?php
/**
 * The template used for displaying page content
 *
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php schema(); ?>>
    <?php
    // Page thumbnail and title.
    bigblank_post_thumbnail();
    the_title('<header class="entry-header"><h1 class="entry-title" ' . schema('name', false, false) . '>', '</h1></header><!-- .entry-header -->');
    ?>
    <div class="entry-content" <?php schema('mainContentOfPage'); ?>>
        <?php
        the_content();
        wp_link_pages(array(
            'before' => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'bigblank') . '</span>',
            'after' => '</div>',
            'link_before' => '<span>',
            'link_after' => '</span>',
        ));
        ?>
    </div><!-- .entry-content -->
    <footer class="entry-meta">
        <?php 
        // Uncomment the following line to use Social Media Share Buttons
        // bigblank_share_post(get_permalink(), get_the_title()); 
        ?>
        <?php edit_post_link(__('Edit', 'bigblank')); ?>
    </footer><!-- .entry-meta -->
</article><!-- #post-## -->
