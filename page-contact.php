<?php
/**
 * Contact Page Template
 * This template is used to specify the correct schema for contact page.
 * Dont forget to change the inc/schema.php to correct slug if you will use a 
 * different page name for your contact page.
 * 
 * @see http://codex.wordpress.org/Template_Hierarchy#Page_display
 */
get_header();
get_header('layout');
?>
<?php
// Start the Loop.
while (have_posts()) : the_post();
    ?>
    <article id="page-contact" <?php post_class(); ?> <?php schema(); ?>>
        <header class="entry-header">
            <?php
            the_title('<h1 class="entry-title" ' . schema('name', false, false) . '>', '</h1>');
            ?>
        </header><!-- .entry-header -->
        <div class="entry-content" <?php schema('mainContentOfPage'); ?>>
            <?php
            the_content();
            ?>
        </div><!-- .entry-content -->
        <footer class="entry-meta">
            <?php edit_post_link(__('Edit', 'bigblank')); ?>
        </footer><!-- .entry-meta -->
    </article><!-- #page-contact -->
    <?php
endwhile;
?>
<?php
get_footer('layout');
get_footer();
