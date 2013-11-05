<?php
/**
 * The template for displaying search forms in Twenty Eleven
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
?>
<form method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="text" class="field search-field" name="s" placeholder="<?php esc_attr_e('Search', 'betheme'); ?>" />
    <i class="fa fa-search"></i>
    <input type="submit" class="search-submit" name="submit" value="<?php esc_attr_e('Search', 'betheme'); ?>" />
</form>
