<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Big Blank
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 */
get_header();
get_header('layout');
?>
<?php if (have_posts()) : ?>
    <header class="page-header">
        <h1 class="page-title" <?php schema('name'); ?>>
            <?php
            if (is_day()) {
                printf(__('Daily Archives: %s', 'bigblank'), get_the_date());
            } elseif (is_month()) {
                printf(__('Monthly Archives: %s', 'bigblank'), get_the_date(_x('F Y', 'monthly archives date format', 'bigblank')));
            } elseif (is_year()) {
                printf(__('Yearly Archives: %s', 'bigblank'), get_the_date(_x('Y', 'yearly archives date format', 'bigblank')));
            } elseif (is_tax()) {
                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                echo $term->name;
            } else {
                _e('Archives', 'bigblank');
            }
            ?>
        </h1>
    </header><!-- .page-header -->
            <?php
            // Start the Loop.
            while (have_posts()) : the_post();
                /*
                 * Include the post format-specific template for the content. If you want to
                 * use this in a child theme, then include a file called called content-___.php
                 * (where ___ is the post format) and that will be used instead.
                 */
                get_template_part('content', get_post_format());
            endwhile;
            // Previous/next page navigation.
            bigblank_paging_nav();
        else :
            // If no content, include the "No posts found" template.
            get_template_part('content', 'none');
        endif;
        ?>
<?php
get_footer('layout');
get_footer();
