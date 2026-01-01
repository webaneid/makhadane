<?php
/**
 * Archive Template: Category
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Ambil gambar latar belakang dari opsi
$bgimg_data = get_field( 'ane_image_arsip', 'option' );
$bgimg_url  = '';

if ( $bgimg_data && isset( $bgimg_data['sizes']['large'] ) ) {
	$bgimg_url = esc_url( $bgimg_data['sizes']['large'] );
}

// Ambil data kategori
$category      = get_queried_object();
$category_name = esc_html( $category->name );
?>

<main id="site-content" class="ane-arsip mb-40">
	<?php if ( $bgimg_url ) : ?>
		<header class="archive-header" style="background-image: url('<?php echo $bgimg_url; ?>')">
			<h1><?php echo $category_name; ?></h1>
		</header>
	<?php else : ?>
		<header class="archive-header">
			<h1><?php echo $category_name; ?></h1>
		</header>
	<?php endif; ?>

	<?php
	if ( function_exists( 'ane_display_breadcrumbs' ) ) {
		ane_display_breadcrumbs();
	}
	?>

	<div class="ane-container">
		<div class="ane-col-46">
			<?php
			get_sidebar();
			get_template_part( 'tp/content', 'archive' );
			?>
		</div>
	</div>
</main>

<?php
get_footer();
