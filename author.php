<?php
/**
 * Archive Template: Author
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Ambil gambar latar belakang dari opsi
$bgimg_data = get_field( 'ane_image_author', 'option' );
$bgimg_url  = '';

if ( $bgimg_data && isset( $bgimg_data['sizes']['large'] ) ) {
	$bgimg_url = esc_url( $bgimg_data['sizes']['large'] );
}

$author_id    = get_queried_object_id();
$display_name = esc_html( get_the_author_meta( 'display_name', $author_id ) );
$author_desc  = get_the_author_meta( 'description', $author_id );
$author_avatar = esc_url( get_avatar_url( $author_id, array( 'size' => 90 ) ) );
?>

<main id="site-content" class="ane-arsip mb-40">
	<?php if ( $bgimg_url ) : ?>
		<header class="archive-header" style="background-image: url('<?php echo $bgimg_url; ?>')">
			<div class="ane-author">
				<?php if ( ! empty( $display_name ) ) : ?>
					<div class="author-image">
						<div class="ane-image">
							<img src="<?php echo $author_avatar; ?>" alt="<?php echo esc_attr( $display_name ); ?>" width="90" height="90" loading="lazy">
						</div>
					</div>
					<div class="author-desk">
						<h1><?php echo $display_name; ?></h1>
						<?php if ( ! empty( $author_desc ) ) : ?>
							<p><?php echo nl2br( esc_html( $author_desc ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</header>
	<?php else : ?>
		<header class="archive-header">
			<div class="ane-author">
				<?php if ( ! empty( $display_name ) ) : ?>
					<div class="author-image">
						<div class="ane-image">
							<img src="<?php echo $author_avatar; ?>" alt="<?php echo esc_attr( $display_name ); ?>" width="90" height="90" loading="lazy">
						</div>
					</div>
					<div class="author-desk">
						<h1><?php echo $display_name; ?></h1>
						<?php if ( ! empty( $author_desc ) ) : ?>
							<p><?php echo nl2br( esc_html( $author_desc ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</header>
	<?php endif; ?>

	<?php
	if ( function_exists( 'ane_display_breadcrumbs' ) ) {
		ane_display_breadcrumbs();
	}
	?>

	<div class="ane-container">
		<div class="entry-content">
			<div class="ane-col-46">
				<?php
				get_sidebar();
				get_template_part( 'tp/content', 'archive' );
				?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
