<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage Blank
 * @since 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
        <meta name="HandheldFriendly" content="true" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <link type="text/plain" rel="author" href="<?php echo home_url(); ?>/humans.txt" />
        <link rel="shortcut icon" href="/favicon.png" />
        <?php wp_head(); ?>
        <!--[if lte IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    </head>
    <body <?php body_class(); ?>>
        <header id="header">
            <!--  if you would like to use logo instead of site title, you could reuse this commented code -->
            <!--
                <a id="logo" href="<?php echo home_url(); ?>" rel="home">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="<?php bloginfo('name'); ?> logo" width="200" height="230"/>
                </a>
            -->
            <a id="site-title" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><?php bloginfo('name'); ?></a>
            <span id="site-description"><?php bloginfo('description'); ?></span>
            <a class="fa fa-reorder" id="menu-toggle" href="#menu"></a>
            <?php be_main_menu(); ?>
        </header>
