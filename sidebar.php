<?php
/**
 * The main sidebar template file.
 *
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage Blank
 */
?>
<div id="sidebar" role="complementary">
    <?php if (is_active_sidebar('sidebar')) : ?>
        <?php dynamic_sidebar('sidebar'); ?>
    <?php endif; ?>
</div>