<?php
/**
 * Search Results Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bgimg = get_field( 'ane_image_arsip', 'option' );
$thumb = ! empty( $bgimg['sizes']['large'] ) ? esc_url( $bgimg['sizes']['large'] ) : ane_dummy_thumbnail();
?>

<main id="site-content" class="ane-arsip mb-40">
	<header class="archive-header" style="background: url('<?php echo esc_url( $thumb ); ?>')">
		<h1>
			<?php
			if ( get_search_query() ) {
				printf( esc_html__( 'Search Result for: %s', 'makhadane' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
			} else {
				esc_html_e( 'Please enter a search term.', 'makhadane' );
			}
			?>
		</h1>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="ane-container">
		<?php get_search_form(); ?>

		<div class="ane-col-46">
			<?php
			get_sidebar();
			get_template_part( 'tp/content', 'archive' );
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
