/**
 * Big Employee Responsive Blank theme customizer javascript
 *
 * @package BigEmployee_Responsive_Blank_Theme
 * @subpackage  Blank
 * @since 1.0
 */
( function( $ ){
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '#site-title' ).html( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '#site-description' ).html( to );
		} );
	} );
} )( jQuery );