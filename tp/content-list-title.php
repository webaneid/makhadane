<?php
/**
 * List Title Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-lis-title'); ?>>
	<?php ane_get_title()?>
</article>
