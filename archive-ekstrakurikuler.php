<?php
/**
 * Archive Template: Ekstrakurikuler
 *
 * @package makhadane
 * @since 4.1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Get background image from ACF options
$bgimg_data = get_field( 'ane_image_arsip_ekstra', 'option' );
$bgimg_url  = '';

if ( $bgimg_data && isset( $bgimg_data['sizes']['large'] ) ) {
	$bgimg_url = esc_url( $bgimg_data['sizes']['large'] );
} elseif ( has_post_thumbnail() ) {
	$bgimg_url = get_the_post_thumbnail_url( null, 'large' );
}
?>

<main id="site-content" class="ane-arsip mb-40 ane-arsip-ekstrakurikuler">
	<?php if ( $bgimg_url ) : ?>
		<header class="archive-header" style="background-image: url('<?php echo $bgimg_url; ?>')">
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
		<div class="entry-content">
			<?php if ( have_posts() ) : ?>
				<div class="ane-col-4">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<div class="ane-item">
							<?php get_template_part( 'tp/content', 'ekstrakurikuler' ); ?>
						</div>
					<?php endwhile; ?>
				</div>

				<?php
				ane_post_pagination();
				?>
			<?php else : ?>
				<?php get_template_part( 'tp/content', 'none' ); ?>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php
get_footer();
