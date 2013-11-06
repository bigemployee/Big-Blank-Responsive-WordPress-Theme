<?php
/* * *
 * Big Employee Responsive Blank theme functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */

if ( ! isset( $content_width ) )
	$content_width = 980;


add_action('after_setup_theme', 'be_setup');

if (!function_exists('be_setup')) {

    function be_setup() {
        /** local language file  */
        load_theme_textdomain('betheme', get_template_directory() . '/languages');

        require(get_template_directory() . '/includes/theme-options.php');
        /** 
         * Add custom theme Widgets 
         * 
         **/
        /** add menus register_nav_menu */
        register_nav_menus(array(
            'main_menu' => 'Main Menu',
            'footer_menu' => 'Footer Menu'
        ));
        
        /** Add custom theme header, background, post formats, and etc... 
         * http://codex.wordpress.org/Function_Reference/add_theme_support
         * **/
        
	/*
	 * This theme does not style the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
         * Uncomment line below to add your own styles to the editor.
	 */
         add_editor_style( array( 'css/editor-style.css') );
        
	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );
        
        // Switches default core markup for search form, comment form, and comments
        // to output valid HTML5.
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));

        /*
         * This theme uses a custom image size for featured images, displayed on
         * "standard" posts and pages.
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(690, 300, true);

        // This theme uses BigGallery instead of WordPress default gallerys.
        add_filter('use_default_gallery_style', '__return_false');

        // add custom metaboxs and save the data
        add_action('add_meta_boxes', 'be_add_custom_box');
        add_action('save_post', 'be_save_post');
    }

}

/** remove unwanted elements from <head> */
function be_head_cleanup() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_generator');
}

add_action('init', 'be_head_cleanup');

/** remove script versions to enable caching */
function be_remove_script_version($src) {
    $parts = explode('?', $src);
    return $parts[0];
}

add_filter('script_loader_src', 'be_remove_script_version');
add_filter('style_loader_src', 'be_remove_script_version');

/**
 *  register scripts and styles
 */
function be_register_scripts_and_styles() {
    if (!is_admin()) {
        wp_register_style('style', get_stylesheet_uri(), false, '1.0', 'all');
// Compressed all JS files in scripts.min.js for fewer http calls:  
// load the latest jQuery from theme library
//        wp_deregister_script('jquery');
//        wp_register_script('jquery', get_template_directory_uri() . '/js/jquery.js', false, '2.0.3', true);
// or load from Google CDN        
//        wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js', false, false, true);
// or load from WP included library
        wp_register_script('jquery', false, false, false, true);
// Read more about wp_register_script at: http://codex.wordpress.org/Function_Reference/wp_register_script
        wp_register_script('scripts', get_template_directory_uri() . '/js/scripts.min.js', array('jquery'), false, true);
        wp_register_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery', 'scripts'), '1.0', true);
        wp_register_script('comment-reply', false, false, false, true);
    }
}

add_action('init', 'be_register_scripts_and_styles');

/**
 * enqueue scripts
 */
function be_enqueue_scripts_and_styles() {
    if (!is_admin()) {
        wp_enqueue_style('style');
        wp_enqueue_script('jquery');
        wp_enqueue_script('scripts');
        wp_enqueue_script('custom');
        if (is_singular() AND comments_open() AND (get_option('thread_comments'))) {
            wp_enqueue_script('comment-reply');
        }
    }
}

add_action('wp_enqueue_scripts', 'be_enqueue_scripts_and_styles');

/**
 *  filter p tags
 */
function filter_ptags_on_images($content) {
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

add_filter('the_content', 'filter_ptags_on_images');

// set post excerpt length
function be_excerpt_length($length) {
    return 140;
}

add_filter('excerpt_length', 'be_excerpt_length');

/**
 *  set continue reading for excerpt
 */
function be_continue_reading_link() {
    return '... <a href="' . esc_url(get_permalink()) . '" title="Read ' .
            get_the_title(get_the_ID()) . '>' . __('Read more &raquo;', 'betheme') . '</a>';
}

/**
 *  add ... and return excerpt more
 */
function be_excerpt_more($more) {
    return ' &hellip;' . be_continue_reading_link();
}

add_filter('excerpt_more', 'be_excerpt_more');

/**
 * widget area and widgets
 */
function be_widgets_init() {
    register_sidebar(array(
        'id' => 'sidebar',
        'name' => 'Sidebar',
        'description' => 'Sidebars appears next to Content',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));
}

add_action('widgets_init', 'be_widgets_init');

/**
 * add custom metaboxs
 */
function be_add_custom_box() {
    add_meta_box('be_layout_box', __('Post Layout', 'betheme'), 'be_layout_metabox', 'post', 'side', 'core');
    add_meta_box('be_layout_box', __('Page Layout', 'betheme'), 'be_layout_metabox', 'page', 'side', 'core');
}

/**
 *  post layout box
 */
function be_layout_metabox($post) {

    wp_nonce_field('post_layout_nonce', '_wpnonce_post_layout');
    $post_layout = get_post_meta($post->ID, 'be_post_layout', true);
    ?>
    <div class="layout image-radio-option theme-layout">
        <label class="description">
            <input type="radio" name="be_post_layout"
                   value="default_layout" <?php checked($post_layout, false); ?> />
            Use Theme Default Layout
        </label>
    </div>
    <br />
    <?php
    foreach (be_layouts() as $layout):
        ?>
        <div class="layout image-radio-option theme-layout">
            <label class="description">
                <input type="radio" name="be_post_layout"
                       value="<?php echo esc_attr($layout['value']); ?>" <?php checked($post_layout, $layout['value']); ?> />
                <span>
                    <?php echo $layout['label']; ?>
                    <br />
                    <img src="<?php echo esc_url($layout['thumbnail']); ?>" width="136" height="122" alt="" />
                </span>
            </label>
        </div>
        <?php
    endforeach;
}

/**
 * save post metabox data
 */
function be_save_post($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (isset($_POST['_wpnonce_post_layout']) && !wp_verify_nonce($_POST['_wpnonce_post_layout'], 'post_layout_nonce'))
        return;

    if (isset($_POST['post_type']) && 'page' == $_POST['post_type'])
        if (!current_user_can('edit_page', $post_id))
            return;
        else
        if (!current_user_can('edit_post', $post_id))
            return;

    $post_layout = isset($_POST['be_post_layout']) ? $_POST['be_post_layout'] : '';
    if (array_key_exists($post_layout, be_layouts())) {
        update_post_meta($post_id, 'be_post_layout', $post_layout);
    } elseif ($post_layout == 'default_layout')
        delete_post_meta($post_id, 'be_post_layout');
}

/**
 * Blog navigation links
 */
if (!function_exists('be_content_nav')) {

    function be_content_nav($nav_id) {
        global $wp_query;

        if ($wp_query->max_num_pages > 1) :
            ?>
            <nav id="<?php echo $nav_id; ?>">
                <?php next_posts_link(__('&larr; Older posts', 'betheme')); ?>
                <?php previous_posts_link(__('Newer posts &rarr;', 'betheme')); ?>
            </nav>
            <?php
        endif;
    }

}

if (!function_exists('be_entry_meta')) :

    /**
     * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
     */
    function be_entry_meta() {
        // Translators: used between list items, there is a space after the comma.
        $categories_list = get_the_category_list(__(', ', 'betheme'));

        // Translators: used between list items, there is a space after the comma.
        $tag_list = get_the_tag_list('', __(', ', 'betheme'));

        $date = sprintf('<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>', esc_url(get_permalink()), esc_attr(get_the_time()), esc_attr(get_the_date('c')), esc_html(get_the_date())
        );

        $author = sprintf('<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(__('View all posts by %s', 'betheme'), get_the_author())), get_the_author()
        );

        // Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
        $utility_text = "";
        if ($categories_list) {
            $utility_text .= __('<span class="category meta"><i class="fa fa-bookmark"></i> %1$s </span> ', 'betheme');
        }
        if ($tag_list) {
            $utility_text .= __('<span class="tags meta"><i class="fa fa-tag"></i> %2$s </span> ', 'betheme');
        }
        $utility_text .= __('<span class="published-date meta"><i class="fa fa-clock-o"></i> %3$s </span> <span class="article-author meta"><i class="fa fa-user"></i> %4$s</span>', 'betheme');

        printf(
                $utility_text, $categories_list, $tag_list, $date, $author
        );
    }

endif;


if (!function_exists('be_comments')) {

    function be_comments($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent' ); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <footer class="comment-meta">
                <div class="comment-author vcard">
                    <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
                    <?php printf(__('%s', 'betheme'), sprintf('<b class="fn">%s</b>', get_comment_author_link())); ?>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php printf(_x('%1$s at %2$s', '1: date, 2: time', 'betheme'), get_comment_date(), get_comment_time()); ?>
                        </time>
                    </a>
                    <?php edit_comment_link(__('Edit', 'betheme'), '<span class="edit-link"><i class="fa fa-pencil"></i>', '</span>', 'betheme'); ?>
                </div><!-- .comment-metadata -->

                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'betheme'); ?></p>
                <?php endif; ?>
            </footer><!-- .comment-meta -->

            <div class="comment-content">
                <?php comment_text(); ?>
            </div><!-- .comment-content -->

            <div class="reply">
                <?php comment_reply_link(array_merge($args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
            </div><!-- .reply -->
        </article><!-- .comment-body -->
        <?php
    }

}

function be_main_menu() {
    wp_nav_menu(array(
        'menu' => 'main_menu',
        'theme_location' => 'main_menu',
        'container' => 'nav',
        'depth' => 2,
        'fallback_cb' => 'be_menu_fallback',
        'container_id' => 'nav'
    ));
}

function be_footer_menu() {
    $walker = new be_footer_menu_walker;
    wp_nav_menu(array(
        'menu' => 'footer_menu',
        'theme_location' => 'footer_menu',
        'container' => 'nav',
        'container_id' => 'footer-nav',
        'depth' => 1,
        'fallback_cb' => 'be_menu_fallback',
        'walker' => $walker
    ));
}

function be_menu_fallback() {
// please define menu
    return;
}

class be_footer_menu_walker extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth != 0)
            return;
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth != 0)
            return;
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        if ($depth != 0)
            return;
        global $wp_query;
        $indent = ( $depth ) ? str0_repeat("\t", $depth) : '';
        $class_names = join(' ', apply_filters(
                        'nav_menu_css_class', array_filter(
                                empty($item->classes) ?
                                        array() :
                                        (array) $item->classes), $item));

        $output .= $indent . '<li class="' . apply_filters('the_title', $item->title, $item->ID) . ' ' . esc_attr($class_names) . '">';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    function end_el(&$output, $item, $depth = 0, $args = array()) {
        if ($depth != 0)
            return;
        $output .= "</li>\n";
    }

}

/**
 * Big Gallery replaces WordPress default gallery
 */
function be_big_gallery($output, $attr) {
    global $post;

    static $instance = 0;
    $instance++;

    // Allow plugins/themes to override the default gallery template.
    // $output = apply_filters('post_gallery', '', $attr);
    if ($output != '')
        return $output;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }

    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'gallerytag' => 'ul',
        'itemtag' => 'li',
        'captiontag' => 'p',
        'size' => 'full',
        'include' => '',
        'exclude' => ''
                    ), $attr));

    $id = intval($id);
    if ('RAND' == $order)
        $orderby = 'none';

    if (!empty($include)) {
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($exclude)) {
        $exclude = preg_replace('/[^0-9,]+/', '', $exclude);
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    } else {
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    }

    if (empty($attachments))
        return '';

    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment)
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $selector = "gallery-{$instance}";

    $gallery_div = "<!-- Start BE Gallery -->\n\t\t<{$gallerytag} id='$selector' class='gallery galleryid-{$id} be-slider'>";
    $output = apply_filters('big_gallery', $gallery_div);

    $i = 0;
    foreach ($attachments as $id => $attachment) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
        ++$i;
        $output .= "\n\t\t\t<{$itemtag} class='gallery-item";
        ($i == 1) ? $output .= " active" : "";
        $output .= "'>";
        $output .= "\n\t\t\t\t$link\n";
        if ($captiontag && trim($attachment->post_excerpt)) {
            $output .= "\t\t\t\t<{$captiontag} class='be-caption  be-bottom'>" . wptexturize($attachment->post_excerpt) . "</{$captiontag}>\n";
        }
        $output .= "\t\t\t</{$itemtag}>";
    }

    $output .= "\n\t\t</{$gallerytag}>\n\t\t<!-- End BE Gallery -->";

    return $output;
}

add_filter("post_gallery", "be_big_gallery", 10, 2);


/**
 * A simple button shortcode, with option to pass link and additional class
 * [button link="http://bigemployee.com/" class="big"]Big Employee[/button]
 * Output:
 * <a href="http://bigemployee.com/" class="button big">Big Employee</a>
 */
function be_add_shortcode_button($atts, $content = null) {
    extract(
            shortcode_atts(
                    array(
        'link' => '#',
        'class' => 'button',
                    ), $atts));
    $content = parse_shortcode_content($content);
    return be_render_button($content, $link, false, $class);
}

function be_render_button($content = 'new link', $link = '#', $echo = true, $class = 'button') {
    $class = strip_tags(trim($class));
    if (strpos($class, 'button') === FALSE) {
        $class = 'button ' . $class;
    }
    if (!$echo)
        return '<a href="' . $link . '" class="' . $class . '">' . do_shortcode($content) . '</a>';
    echo '<a href="' . $link . '" class="' . $class . '">' . do_shortcode($content) . '</a>';
}

add_shortcode('button', 'be_add_shortcode_button');