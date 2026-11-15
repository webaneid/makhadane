<?php
/**
 * Mobile Category Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-mobile-cat' ); ?>>
	<div class="ane-container">
		<div class="isi">

			<div class="pilih-kategori">
				<?php if ( get_field( 'ane_aktif' ) ) : ?>
					<h1><?php esc_html_e( 'Select Category', 'makhadane' ); ?></h1>
					<?php get_template_part( 'tp/mobile/kategori-menu' ); ?>
				<?php endif; ?>
			</div>

			<?php
			$paged = max( 1, absint( get_query_var( 'paged', 1 ) ) );

			$mobile_query = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 15,
					'paged'          => $paged,
					'post_status'    => 'publish',
				)
			);

			if ( $mobile_query->have_posts() ) :
				echo '<section class="ane-newest">';

				while ( $mobile_query->have_posts() ) :
					$mobile_query->the_post();
					get_template_part( 'tp/content', 'list' );
				endwhile;

				wp_reset_postdata();
				echo '</section>';

				if ( $mobile_query->max_num_pages > 1 ) {
					ane_post_pagination( $mobile_query );
				}
			endif;
			?>

		</div>
	</div>
</article>
