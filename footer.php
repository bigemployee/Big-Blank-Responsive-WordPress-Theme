<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
?>
<?php
$options = be_get_theme_options();
$footer_copyright = $options['footer_copyright'];
$footer_text = $options['footer_text'];
?>
<footer id="footer" class="footer">
    <?php be_footer_menu(); ?>
    <div id="copyright">
        <p class="copyright"><?php echo $footer_copyright; ?></p>
        <p><?php echo $footer_text;?>. Theme by <a href="http://bigemployee.com/projects/big-blank-responsive-wordpress-theme/">Big Employee</a></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>