<?php
/**
 * The default template for displaying post content
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
    <footer class="entry-meta">
        <?php be_entry_meta(); ?>
        <?php edit_post_link(__('Edit', 'betheme'), '<span class="edit-link meta"><i class="fa fa-pencil"></i> ', '</span>'); ?>
        <?php if (is_singular() && get_the_author_meta('description') && is_multi_author()) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
            <div class="author-info">
                <div class="author-avatar">
                    <?php echo get_avatar(get_the_author_meta('user_email'), apply_filters('gravatar', 128)); ?>
                </div><!-- .author-avatar -->
                <div class="author-description">
                    <h2><?php printf(__('About %s', 'betheme'), get_the_author()); ?></h2>
                    <p><?php the_author_meta('description'); ?></p>
                    <div class="author-link">
                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="author">
                            <?php printf(__('View all posts by %s <span class="meta-nav">&rarr;</span>', 'betheme'), get_the_author()); ?>
                        </a>
                    </div><!-- .author-link	-->
                </div><!-- .author-description -->
            </div><!-- .author-info -->
        <?php endif; ?>
    </footer><!-- .entry-meta -->
    <?php comments_template(); ?>
</article>