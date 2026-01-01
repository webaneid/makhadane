<?php
/**
 * Sliding News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$homekategori = get_sub_field('pilih_kategori');
	$judul = get_sub_field('custom_title');
	$category_link = '';
	$category_name = '';

	if ( $homekategori && ! empty( $homekategori->term_id ) ) {
		$category_link = get_category_link( $homekategori->term_id );
		$category_name = esc_html( $homekategori->name );
	}
?>
<section class="ane-news-sliding ane-blog-section">
	<div class="ane-col-column">
		<?php if ( ! empty( $category_link ) ) : ?>
			<div class="section-title">
				<div class="section-title-item">
					<?php if ( ! empty( $judul ) ) : ?>
						<a href="<?php echo esc_url( $category_link ); ?>"><h2><?php echo esc_html( $judul ); ?></h2></a>
					<?php else : ?>
						<a href="<?php echo esc_url( $category_link ); ?>"><h2><?php echo $category_name; ?></h2></a>
					<?php endif; ?>
				</div>
				<a class="lainnya" href="<?php echo esc_url( $category_link ); ?>">
					<?php esc_html_e( 'View All', 'makhadane' ); ?> <i class="ane-chevron-right-alt-2"></i>
				</a>
			</div>
		<?php endif; ?>

		<div class="owl-carousel dot-style2" id="home-sliding">
			<?php
			if ( $homekategori && ! empty( $homekategori->term_id ) ) :
				$the_query = new WP_Query( array(
					'post_type'      => 'post',
					'cat'            => $homekategori->term_id,
					'posts_per_page' => 8,
				) );

				while ( $the_query->have_posts() ) : $the_query->the_post();
					get_template_part( 'tp/content', 'overlay' );
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</div>
</section>
