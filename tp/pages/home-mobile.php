<?php
/**
 * Home Mobile Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'landing-page' ); ?>>
	<?php
	if ( 1 === $paged && have_rows( 'home_content' ) ) :
		while ( have_rows( 'home_content' ) ) :
			the_row();

			$layouts = array(
				'home_box_slider'      => 'big-slider',
				'home_featured'        => 'featured',
				'home_cta'             => 'calltoaction',
				'home_testimoni'       => 'testimoni',
				'home_ekstrakurikuler' => 'ekstra',
				'home_guru'            => 'guru',
				'home_banner'          => 'banner',
				'home_about'           => 'about-us',
				'image_side_text'      => 'image-side-text',
				'image_side_listing'   => 'image-side-listing',
				'image_below_text'     => 'image-below-text',
				'home_faq'             => 'faq',
			);

			$current_layout = get_row_layout();
			if ( isset( $layouts[ $current_layout ] ) ) {
				get_template_part( 'tp/section/' . $layouts[ $current_layout ] );
			}

		endwhile;
	endif;

	$mobile_query = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 10,
			'paged'          => $paged,
			'post_status'    => 'publish',
		)
	);
	?>

	<section class="ane-post-mobile">
		<?php if ( $mobile_query->have_posts() ) : ?>
			<div class="ane-text">
				<h2 class="judul-utama"><?php esc_html_e( 'Newest', 'makhadane' ); ?> <span><?php esc_html_e( 'Posts', 'makhadane' ); ?></span></h2>
			</div>
			<?php
			$counter = 0;
			while ( $mobile_query->have_posts() ) :
				$mobile_query->the_post();
				get_template_part( 'tp/content', ( 4 === $counter ) ? 'overlay' : 'list' );
				$counter++;
			endwhile;
			wp_reset_postdata();
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'makhadane' ); ?></p>
		<?php endif; ?>
	</section>

	<?php
	if ( $mobile_query->max_num_pages > 1 ) :
		global $wp_query;
		$orig_query = $wp_query;
		$wp_query   = $mobile_query;
		echo ane_post_pagination();
		$wp_query = $orig_query;
	endif;
	?>
</article>
