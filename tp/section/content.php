<?php
/**
 * Content Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ane-profil-konten">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<?php if ( has_post_thumbnail() ) :
					$thumb_img_url = esc_url( get_the_post_thumbnail_url( $post->ID, 'large' ) );
					$title = esc_attr( get_the_title() );
				?>
					<div class="ane-image">
						<img src="<?php echo $thumb_img_url; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>">
					</div>
				<?php endif; ?>
			</div>
			<div class="col-md-7">
				<div class="ane-content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</div>
</section>
