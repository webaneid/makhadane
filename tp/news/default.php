<?php
/**
 * Default News Layout Template Part
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
$category_id   = $homekategori->term_id ?? 0;
$category_name = esc_html( $homekategori->name ?? '' );
$category_link = $category_id ? esc_url( get_category_link( $category_id ) ) : '#';

// Ambil 6 post sekaligus untuk menghindari banyak query
$query = new WP_Query(
	array(
		'post_type'      => 'post',
		'cat'            => $category_id,
		'posts_per_page' => 6, // 1 post utama + 5 post lainnya
		'no_found_rows'  => true, // Optimasi query
	)
);
?>
<section class="ane-news-default ane-blog-section">
	<div class="ane-col-column">
		<div class="blog-section-head">
			<div class="blog-section-title-wrap">
				<span class="blog-section-marker" aria-hidden="true"></span>
				<h2>
					<a href="<?php echo $category_link; ?>">
						<?php echo esc_html( ! empty( $judul ) ? $judul : $category_name ); ?>
					</a>
				</h2>
			</div>
			<span class="blog-section-line" aria-hidden="true"></span>
			<a class="blog-section-more" href="<?php echo $category_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
				<span class="blog-section-more-icon" aria-hidden="true"><?php echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) ); ?></span>
			</a>
		</div>

		<?php if ( $query->have_posts() ) : ?>
		<div class="ane-col-46">
			<?php $counter = 0; ?>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<?php if ( $counter === 0 ) : ?>
					<!-- Kolom Kiri (Post Utama) -->
					<div class="ane-kiri">
						<?php get_template_part( 'tp/content', 'overlay' ); ?>
					</div>
				<?php else : ?>
					<!-- Kolom Kanan (Post Lainnya) -->
					<?php
					if ( $counter === 1 ) {
						echo '<div class="ane-kanan">';}
					?>
						<?php get_template_part( 'tp/content', 'list' ); ?>
					<?php
					if ( $counter === 5 ) {
						echo '</div>';}
					?>
				<?php endif; ?>
				<?php ++$counter; ?>
			<?php endwhile; ?>
			<?php
			if ( $counter > 1 ) {
				echo '</div>';}
			?>
		</div>
		<?php endif; ?>
		
		<?php wp_reset_postdata(); ?>
</section>
