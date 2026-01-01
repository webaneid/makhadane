<?php
/**
 * Sidebar Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'default-sidebar' ) ) {
	return;
}
?>

<aside class="ane-kiri" id="sticky-sidebar">
	<div class="right-sidebar">
		<?php dynamic_sidebar( 'default-sidebar' ); ?>
	</div>
</aside>
