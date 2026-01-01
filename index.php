<?php
/**
 * The main template file
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bgimg     = get_field( 'ane_image_arsip', 'option' );
$bgimg_url = ! empty( $bgimg['sizes']['large'] ) ? esc_url( $bgimg['sizes']['large'] ) : ane_dummy_thumbnail();
?>

<main id="site-content" class="ane-arsip mb-40">
	<header class="archive-header" style="background-image: url('<?php echo esc_url( $bgimg_url ); ?>')">
		<h1><?php the_archive_title(); ?></h1>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="ane-container">
		<div class="ane-col-46">
			<?php
			get_sidebar();
			get_template_part( 'tp/content', 'archive' );
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
