<?php
/**
 * Archive Template: Testimoni (Testimonials)
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Ambil gambar latar belakang dari opsi.
$makhadane_bgimg_data = get_field( 'ane_image_arsip_testimoni', 'option' );
$makhadane_bgimg_url  = '';

if ( is_array( $makhadane_bgimg_data ) && isset( $makhadane_bgimg_data['sizes']['large'] ) ) {
	$makhadane_bgimg_url = (string) $makhadane_bgimg_data['sizes']['large'];
}
?>

<main id="site-content" class="ane-arsip mb-40 ane-arsip-testimoni">
	<?php if ( '' !== $makhadane_bgimg_url ) : ?>
		<header class="archive-header" style="background-image: url('<?php echo esc_url( $makhadane_bgimg_url ); ?>')">
			<?php post_type_archive_title( '<h1 class="general-title">', '</h1>' ); ?>
		</header>
	<?php else : ?>
		<header class="archive-header">
			<?php post_type_archive_title( '<h1 class="general-title">', '</h1>' ); ?>
		</header>
	<?php endif; ?>

	<?php
	if ( function_exists( 'ane_display_breadcrumbs' ) ) {
		ane_display_breadcrumbs();
	}
	?>

	<div class="ane-container">
		<?php if ( have_posts() ) : ?>
			<div class="entry-content">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'tp/content', 'testimoni' );
				endwhile;
				?>
			</div>
			<?php ane_post_pagination(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="entry-content">
				<?php get_template_part( 'tp/content', 'none' ); ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
