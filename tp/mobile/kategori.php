<?php
/**
 * Mobile Category Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
			$paged = get_query_var( 'paged', 1 );

			if ( 1 === $paged ) {
				get_template_part( 'tp/news', 'featured-mobile' );
			}

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

				foreach ( $mobile_query->posts as $index => $post ) {
					setup_postdata( $post );
					get_template_part( 'tp/content', 'list' );

					if ( 6 === $index ) {
						get_template_part( 'tp/mobile', 'news-populer' );
					}
				}

				wp_reset_postdata();
				echo '</section>';

				if ( $mobile_query->max_num_pages > 1 ) {
					echo ane_post_pagination();
				}
			endif;
			?>

		</div>
	</div>
</article>
