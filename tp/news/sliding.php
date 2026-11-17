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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped

	$homekategori  = get_sub_field( 'pilih_kategori' );
	$judul         = get_sub_field( 'custom_title' );
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
			<div class="blog-section-head">
				<div class="blog-section-title-wrap">
					<span class="blog-section-marker" aria-hidden="true"></span>
					<h2>
						<a href="<?php echo esc_url( $category_link ); ?>">
							<?php echo ! empty( $judul ) ? esc_html( $judul ) : $category_name; ?>
						</a>
					</h2>
				</div>
				<span class="blog-section-line" aria-hidden="true"></span>
				<a class="blog-section-more" href="<?php echo esc_url( $category_link ); ?>">
					<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
					<span class="blog-section-more-icon" aria-hidden="true"><?php echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) ); ?></span>
				</a>
			</div>
		<?php endif; ?>

		<div class="owl-carousel dot-style2" id="home-sliding">
			<?php
			if ( $homekategori && ! empty( $homekategori->term_id ) ) :
				$the_query = new WP_Query(
					array(
						'post_type'      => 'post',
						'cat'            => $homekategori->term_id,
						'posts_per_page' => 8,
					)
				);

				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					get_template_part( 'tp/content', 'overlay' );
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</div>
</section>
