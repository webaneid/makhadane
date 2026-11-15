<?php
/**
 * Testimoni Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$thumb_kotak = has_post_thumbnail() ? wp_get_attachment_image_src( (int) get_post_thumbnail_id(), 'kotak' ) : false;
$photo       = is_array( $thumb_kotak ) && ! empty( $thumb_kotak[0] ) ? $thumb_kotak[0] : ane_dummy_kotak();

$testimoni = get_field( 'ane_testimoni' );
$jabatan   = get_field( 'ane_jabatan' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-konten-testimoni' ); ?>>
	<header class="entry-header">
		<?php if ( $testimoni ) : ?>
			<p><?php echo esc_html( (string) $testimoni ); ?></p>
		<?php endif; ?>
	</header>

	<div class="post-content">
		<div class="ane-image">
			<img src="<?php echo esc_url( (string) $photo ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
		</div>

		<div class="orangnya">
			<?php ane_get_title(); ?>
			<?php if ( $jabatan ) : ?>
				<p><?php echo esc_html( (string) $jabatan ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</article>
