<?php
/**
 * Institutional Page Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( has_post_thumbnail() ) {
	$thumbImg = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );
	$photo    = ! empty( $thumbImg ) ? esc_url( $thumbImg[0] ) : ane_dummy_thumbnail();
} else {
	$photo = ane_dummy_thumbnail();
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-page-default ane-page-lembaga' ); ?>>
	<header class="entry-header" style="background: url('<?php echo esc_url( $photo ); ?>')">
		<h1><?php the_title(); ?></h1>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="ane-container">
		<div class="entry-content">
			<?php get_template_part( 'tp/mobile/menu-lembaga' ); ?>

			<div class="ane-content">
				<div class="share-this">
					<?php echo ane_social_share(); ?>
				</div>

				<?php the_content(); ?>

				<div class="comments-form">
					<?php //echo fungsi_komen_facebook_webane(); ?>
				</div>
			</div>
		</div>
	</div>
</article>
