<?php
/**
 * Search Form Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ane-cari">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="text" class="form-control" placeholder="<?php echo esc_attr_x( 'Looking for some thing?  &hellip;', 'placeholder', 'makhadane' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
		<button><i class="ane-search"></i></button>
	</form>
</div>
