<?php
/**
 * Page Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = get_the_ID();
$photo     = has_post_thumbnail() ? esc_url(wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'large')) : esc_url(ane_dummy_thumbnail());
$title     = get_the_title();
$permalink = get_permalink();
?>

<article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('ane-page-default'); ?>>

	<header class="entry-header" style="background-image: url('<?php echo $photo; ?>');">
		<h1><?php echo wp_kses_post($title); ?></h1>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="entry-content ane-container">
		<div class="ane-content">

			<!-- Share this section -->
			<div class="share-this">
				<?php
				if ( function_exists( 'ane_social_share' ) ) {
					echo ane_social_share();
				}
				?>
			</div>

			<!-- Page content -->
			<?php the_content(); ?>

			<!-- Comments section -->
			<div class="comments-form">
				<?php
			// Facebook Comments integration
			if ( function_exists( 'ane_load_facebook_comment' ) ) {
				echo ane_load_facebook_comment();
			}
			?>
			</div>

		</div>
	</div>

</article>
