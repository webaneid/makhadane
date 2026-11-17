<?php
/**
 * Column News Layout Template Part (2-Column Category Digest)
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! have_rows( 'column_item' ) ) {
	return;
}
?>
<section class="ane-news-column ane-blog-section">
	<div class="news-column-grid">
		<?php
		while ( have_rows( 'column_item' ) ) :
			the_row();
			$makhadane_column_cat   = get_sub_field( 'pilih_kategori' );
			$makhadane_column_title = trim( (string) get_sub_field( 'column_title' ) );
			$makhadane_category_id  = ( is_object( $makhadane_column_cat ) && isset( $makhadane_column_cat->term_id ) ) ? (int) $makhadane_column_cat->term_id : 0;
			$makhadane_cat_name     = ( is_object( $makhadane_column_cat ) && isset( $makhadane_column_cat->name ) ) ? (string) $makhadane_column_cat->name : '';
			$makhadane_category_url = $makhadane_category_id ? (string) get_category_link( $makhadane_category_id ) : '#';
			$makhadane_heading      = '' !== $makhadane_column_title ? $makhadane_column_title : $makhadane_cat_name;

			$makhadane_query = new WP_Query(
				array(
					'post_type'           => 'post',
					'cat'                 => $makhadane_category_id,
					'posts_per_page'      => 5,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			?>
			<div class="news-column-card">
				<?php if ( '' !== $makhadane_heading ) : ?>
					<div class="blog-section-head">
						<div class="blog-section-title-wrap">
							<span class="blog-section-marker" aria-hidden="true"></span>
							<h2>
								<a href="<?php echo esc_url( $makhadane_category_url ); ?>">
									<?php echo esc_html( $makhadane_heading ); ?>
								</a>
							</h2>
						</div>
						<span class="blog-section-line" aria-hidden="true"></span>
						<a class="blog-section-more" href="<?php echo esc_url( $makhadane_category_url ); ?>">
							<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
							<span class="blog-section-more-icon" aria-hidden="true"><?php echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) ); ?></span>
						</a>
					</div>
				<?php endif; ?>

				<?php if ( $makhadane_query->have_posts() ) : ?>
					<?php
					$makhadane_counter = 0;
					while ( $makhadane_query->have_posts() ) :
						$makhadane_query->the_post();
						++$makhadane_counter;

						if ( 1 === $makhadane_counter ) :
							get_template_part( 'tp/content', 'overlay' );
							if ( $makhadane_query->post_count > 1 ) :
								?>
								<div class="news-column-list">
							<?php endif; ?>
						<?php else : ?>
							<?php get_template_part( 'tp/content', 'list' ); ?>
							<?php
						endif;
					endwhile;
					if ( $makhadane_counter > 1 ) :
						?>
						</div>
						<?php
					endif;
					wp_reset_postdata();
					?>
				<?php endif; ?>
			</div>
		<?php endwhile; ?>
	</div>
</section>
