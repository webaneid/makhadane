<?php
/**
 * Column News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped

if ( have_rows( 'column_item' ) ) : ?>
<section class="ane-news-column ane-blog-section">
	<div class="ane-container">
		<div class="ane-col-2">
			<?php
			while ( have_rows( 'column_item' ) ) :
				the_row();
				?>
				<?php
				$column_cat   = get_sub_field( 'pilih_kategori' );
				$column_title = get_sub_field( 'column_title' );

				$category_id   = $column_cat->term_id ?? 0;
				$category_name = esc_html( $column_cat->name ?? '' );
				$category_url  = $category_id ? esc_url( get_category_link( $category_id ) ) : '#';

				// Query semua post (5 post)
				$query = new WP_Query(
					array(
						'post_type'      => 'post',
						'cat'            => $category_id,
						'posts_per_page' => 5,
						'no_found_rows'  => true, // Optimasi query
					)
				);
				?>

				<div class="ane-item">
					<div class="blog-section-head">
						<div class="blog-section-title-wrap">
							<span class="blog-section-marker" aria-hidden="true"></span>
							<h2>
								<a href="<?php echo $category_url; ?>">
									<?php echo ! empty( $column_title ) ? wp_kses_post( $column_title ) : $category_name; ?>
								</a>
							</h2>
						</div>
						<span class="blog-section-line" aria-hidden="true"></span>
						<a class="blog-section-more" href="<?php echo $category_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
							<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
							<span class="blog-section-more-icon" aria-hidden="true"><?php echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) ); ?></span>
						</a>
					</div>

					<?php if ( $query->have_posts() ) : ?>
						<?php $counter = 0; ?>
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<?php
							if ( $counter === 0 ) {
								get_template_part( 'tp/content', 'overlay' ); // Post pertama sebagai overlay
							} else {
								get_template_part( 'tp/content', 'list' ); // Post berikutnya sebagai list
							}
							++$counter;
							?>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					<?php endif; ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php endif; ?>
