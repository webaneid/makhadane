<?php
/**
 * Archive Template: FAQ
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bgimg_data = get_field( 'ane_image_arsip_faq', 'option' );
$bgimg_url  = '';

if ( $bgimg_data && isset( $bgimg_data['sizes']['large'] ) ) {
	$bgimg_url = esc_url( $bgimg_data['sizes']['large'] );
}
?>

<main id="site-content" class="ane-arsip mb-40 ane-arsip-faq">
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
		<div class="accordion">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<div class="accordion-item">
						<button id="accordion-button-<?php the_ID(); ?>" aria-expanded="false">
							<span class="accordion-title"><?php the_title(); ?></span>
							<span class="icon" aria-hidden="true"></span>
						</button>
						<div class="accordion-content"><?php the_content(); ?></div>
					</div>
				<?php endwhile; ?>

				<?php ane_post_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'tp/content', 'none' ); ?>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php
get_footer();
