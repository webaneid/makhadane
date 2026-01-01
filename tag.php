<?php
/**
 * Tag Archive Template
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

$tag      = get_queried_object();
$tag_name = ! empty( $tag->name ) ? esc_html( $tag->name ) : '';
$tag_desc = ! empty( $tag->description ) ? wp_strip_all_tags( $tag->description ) : '';
?>

<main id="site-content" class="ane-arsip mb-40">
	<header class="archive-header" style="background-image: url('<?php echo esc_url( $thumb ); ?>')">
		<h1><?php echo esc_html( $tag_name ); ?></h1>
		<?php if ( ! empty( $tag_desc ) ) : ?>
			<p><?php echo esc_html( $tag_desc ); ?></p>
		<?php endif; ?>
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
