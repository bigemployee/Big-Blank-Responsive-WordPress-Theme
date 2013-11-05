<?php
/**
 * Big Employee Responsive Blank Theme Options
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */

/**
 * enqueue styles and scripts for admin option page
 */
function be_admin_enqueue_scripts($hook_suffix) {
    wp_enqueue_style('be-theme-options', get_template_directory_uri() . '/includes/theme-options.css', false);
    wp_enqueue_script('be-theme-options', get_template_directory_uri() . '/includes/theme-options.js', array('farbtastic', 'media-upload', 'thickbox'), '1.0', true);
    wp_enqueue_style('farbtastic');
}

add_action('admin_print_styles-appearance_page_theme_options', 'be_admin_enqueue_scripts');

/**
 * register form setting for options array
 */
function be_theme_options_init() {
    register_setting('be_options', 'be_theme_options', 'be_theme_options_validate');

    // register settings field group
    add_settings_section('general', __('General Layout Settings', 'betheme'), '__return_false', 'theme_options');
    add_settings_section('footer', __('Footer Settings', 'betheme'), '__return_false', 'theme_options');

    add_settings_field('layout', __('Default Layout', 'betheme'), 'be_settings_field_layout', 'theme_options', 'general');
    add_settings_field('footer-copyright', __('Footer Copyright', 'betheme'), 'be_settings_field_footer_copyright', 'theme_options', 'footer', array('label_for' => 'footer-copyright'));
    add_settings_field('footer-text', __('Footer Text', 'betheme'), 'be_settings_field_footer_text', 'theme_options', 'footer', array('label_for' => 'footer-text'));
}

add_action('admin_init', 'be_theme_options_init');

/**
 * change capability to save be_options
 */
function be_options_page_capability($capability) {
    return 'edit_theme_options';
}

add_filter('option_page_capability_be_options', 'be_options_page_capability');

/**
 * Add options page
 */
function be_theme_options_add_page() {
    $theme_page = add_theme_page(
            __('Theme Options', 'betheme'), // name of page
            __('Theme Options', 'betheme'), // label in menu
            'edit_theme_options', // capability required
            'theme_options', // unique menu slug
            'be_theme_options_render_page' // render function
    );
    if (!$theme_page)
        return;
    add_action("load-$theme_page", 'be_theme_options_help');
}

add_action('admin_menu', 'be_theme_options_add_page');

/**
 * @betodo: add options page help tabs
 */
function be_theme_options_help() {

}


/**
 * returns an array of layout options
 */
function be_layouts() {
    $layout_options = array(
        'content-sidebar' => array(
            'value' => 'content-sidebar',
            'label' => __('Content on left', 'betheme'),
            'thumbnail' => get_template_directory_uri() . '/includes/images/content-sidebar.png'
        ),
        'sidebar-content' => array(
            'value' => 'sidebar-content',
            'label' => __('Content on right', 'betheme'),
            'thumbnail' => get_template_directory_uri() . '/includes/images/sidebar-content.png'
        ),
        'content' => array(
            'value' => 'content',
            'label' => __('One-column, no sidebar', 'betheme'),
            'thumbnail' => get_template_directory_uri() . '/includes/images/content.png'
        )
    );
    return apply_filters('be_layouts', $layout_options);
}

/**
 * return default theme options
 */
function be_get_default_theme_options() {
    $default_theme_options = array(
        'logo' => '',
        'color_scheme' => '',
        'link_color' => '',
        'link_color_hover' => '',
        'theme_layout' => 'content-sidebar',
        'footer_copyright' => 'Copyright &copy; 2012 <a href="' . site_url() . '">' . get_bloginfo('name') . '</a>',
        'footer_text' => 'Powered by <a href="http://wordpress.org">WordPress</a>'
    );
    return apply_filters('be_default_theme_options', $default_theme_options);
}

/**
 * @return array of theme options
 */
function be_get_theme_options() {
    return get_option('be_theme_options', be_get_default_theme_options());
}


/**
 * render the default layout setting field
 */
function be_settings_field_layout() {
    $options = be_get_theme_options();
    foreach (be_layouts() as $layout):
        ?>
        <div class="layout image-radio-option theme-layout">
            <label class="description">
                <input type="radio" name="be_theme_options[theme_layout]"
                       value="<?php echo esc_attr($layout['value']); ?>" <?php checked($options['theme_layout'], $layout['value']); ?> />
                <span>
                    <img src="<?php echo esc_url($layout['thumbnail']); ?>" width="136" height="122" alt="" />
                    <?php echo $layout['label']; ?>
                </span>
            </label>
        </div>
        <?php
    endforeach;
}

/**
 * render the footer copyright settings field
 */
function be_settings_field_footer_copyright() {
    $options = be_get_theme_options();
    ?>
    <input type="text" class="large-text" id="footer-copyright" name="be_theme_options[footer_copyright]" value="<?php echo esc_attr($options['footer_copyright']); ?>" />
    <?php
}

/**
 * render the footer text settings field
 */
function be_settings_field_footer_text() {
    $options = be_get_theme_options();
    ?>
    <input type="text" class="large-text" id="footer-text" name="be_theme_options[footer_text]" value="<?php echo esc_attr($options['footer_text']); ?>" />
    <?php
}

/**
 * @return rendered options page
 */
function be_theme_options_render_page() {
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <?php $theme_name = wp_get_theme();?>
        <h2><?php printf(__('%s Options', 'betheme'), $theme_name); ?></h2>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('be_options');
            do_settings_sections('theme_options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * validate form input
 */
function be_theme_options_validate($input) {
    $output = $defaults = be_get_default_theme_options();

    if (isset($input['theme_layout']) && array_key_exists($input['theme_layout'], be_layouts()))
        $output['theme_layout'] = $input['theme_layout'];
    if (isset($input['footer_copyright']))
        $output['footer_copyright'] = $input['footer_copyright'];
    if (isset($input['footer_text']))
        $output['footer_text'] = $input['footer_text'];
    return apply_filters('be_theme_options_validate', $output, $input, $defaults);
}


/**
 * Implements be theme options into theme customizer
 *
 * @param $wp_customize Thme Customizer object
 * @return void
 */
function be_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';

    $options = be_get_theme_options();
    $defaults = be_get_default_theme_options();

    // Default Layout
    $wp_customize->add_section('be_layout', array(
        'title' => __('Layout', 'betheme'),
        'priority' => 50,
    ));

    $wp_customize->add_setting('be_theme_options[theme_layout]', array(
        'type' => 'option',
        'default' => $defaults['theme_layout'],
        'sanitize_callback' => 'sanitize_key',
    ));

    $layouts = be_layouts();
    $choices = array();
    foreach ($layouts as $layout) {
        $choices[$layout['value']] = $layout['label'];
    }

    $wp_customize->add_control('be_theme_options[theme_layout]', array(
        'section' => 'be_layout',
        'type' => 'radio',
        'choices' => $choices,
    ));

    // Footer Section
    $wp_customize->add_section('footer', array(
        'title' => __('Footer Settings', 'betheme'),
        'priority' => 50
    ));
    // copyright text
    $wp_customize->add_setting('be_theme_options[footer_copyright]', array(
        'type' => 'option',
        'default' => $defaults['footer_copyright'],
    ));
    $wp_customize->add_control('be_theme_options[footer_copyright]', array(
        'label' => __('Copyright Text', 'betheme'),
        'section' => 'footer',
        'settings' => 'be_theme_options[footer_copyright]',
    ));
    // footer text
    $wp_customize->add_setting('be_theme_options[footer_text]', array(
        'type' => 'option',
        'default' => $defaults['footer_text'],
    ));
    $wp_customize->add_control('be_theme_options[footer_text]', array(
        'label' => __('Footer Text', 'betheme'),
        'section' => 'footer',
        'settings' => 'be_theme_options[footer_text]',
    ));
}

add_action('customize_register', 'be_customize_register');

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 * Used with blogname and blogdescription.
 */
function be_customize_preview_js() {
    wp_enqueue_script('be_customizer', get_template_directory_uri() . '/includes/theme-customizer.js', array('customize-preview'), '1.0', true);
}

add_action('customize_preview_init', 'be_customize_preview_js');