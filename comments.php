<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 */
/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>
<div id="comments" class="comments-area" <?php schema(false, 'Comment'); ?>>
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            printf(_n('One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'bigblank'), number_format_i18n(get_comments_number()), get_the_title());
            ?>
        </h2>
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
                <h3 class="screen-reader-text"><?php _e('Comment navigation', 'bigblank'); ?></h3>
                <span class="nav-prev"><?php previous_comments_link(__('&larr; Older Comments', 'bigblank')); ?></span>
                <span class="nav-next"><?php next_comments_link(__('Newer Comments &rarr;', 'bigblank')); ?></span>
            </nav><!-- #comment-nav-above -->
        <?php endif; // Check for comment navigation.  ?>
        <ol id="comment-list" class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'type' => 'all', /* Type of comments to list. Default 'all'. Accepts 'all', 'comment', 'pingback', 'trackback', 'pings'. */
                'short_ping' => true,
                'avatar_size' => 128,
            ));
            ?>
        </ol><!-- .comment-list -->
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
                <h3 class="screen-reader-text"><?php _e('Comment navigation', 'bigblank'); ?></h3>
                <span class="nav-prev"><?php previous_comments_link(__('&larr; Older Comments', 'bigblank')); ?></span>
                <span class="nav-next"><?php next_comments_link(__('Newer Comments &rarr;', 'bigblank')); ?></span>
            </nav><!-- #comment-nav-below -->
        <?php endif; // Check for comment navigation.  ?>
        <?php if (!comments_open()) : ?>
            <p class="no-comments"><?php _e('Comments are closed.', 'bigblank'); ?></p>
        <?php endif; ?>
    <?php endif; // have_comments() ?>
    <?php comment_form(); ?>
</div><!-- #comments -->
