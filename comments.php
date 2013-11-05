<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to be_comments() which is
 * located in the functions.php file.
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
?>
<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die('Please do not load this page directly. Thanks!');
?>
<div id="comments">
    <?php
    if (post_password_required()) :
        ?>
        <p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'betheme'); ?></p>
        <?php
        return;
    endif;
    ?>
    <?php if (have_comments()) : ?>
        <h3 id="comments-title"><?php printf(_n('One Response', '%1$s Responses', get_comments_number(), 'betheme'), number_format_i18n(get_comments_number())); ?></h3>
        <ol id="commentlist" class="commentlist">
            <?php
                wp_list_comments(array(
                    'callback' => 'be_comments',
                    'style' => 'ol',
                    'avatar_size' => 128,
                ));
            ?>
        </ol>
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav">
                <?php previous_comments_link() ?>
                <?php next_comments_link() ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = $req_text = '';
    if ($req) {
        $aria_req = " aria-required='true' required";
        $text_req = ' (' . __('Required', 'betheme') . ')';
    }
    $fields = array(
        'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'betheme') . $text_req . '</label> ' .
        '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" placeholder="' . __('Your Name', 'betheme') . '" tabindex="1"' . $aria_req . ' /></p>',
        'email' => '<p class="comment-form-email"><label for="email">' . __('Email', 'betheme') . $text_req . '</label> ' .
        '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="' . __('Your Email', 'betheme') . '" tabindex="2"' . $aria_req . ' /></p>',
        'url' => '<p class="comment-form-url"><label for="url">' . __('Website', 'betheme') . '</label>' .
        '<input id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" placeholder="' . __('Your Website', 'betheme') . '" tabindex="3" /></p>',
    );
    $comment_form_args = array(
        'fields' => $fields,
        'comment_field' => '<textarea name="comment" id="comment" placeholder="' . __('Your Comment Here', 'betheme') . '&hellip;' . '" tabindex="4"></textarea>',
        'comment_notes_before' => '',
        'comment_notes_after' => '<p id="allowed_tags" class="form-allowed-tags">' . sprintf(__('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'betheme'), ' <code>' . allowed_tags() . '</code>') . '</p>',
        'id_submit' => 'comment-button',
    );
    ?>
    <?php comment_form($comment_form_args); ?>
</div>
