<?php
/**
 * Classic News Layout Template Part
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
$kategori_id   = $homekategori->term_id ?? 0;
$kategori_nama = esc_html( $homekategori->name ?? '' );
$kategori_url  = $kategori_id ? esc_url( get_category_link( $kategori_id ) ) : '#';

// Ambil 5 post sekaligus (1 utama + 4 berikutnya)
$query = new WP_Query(
	array(
		'post_type'      => 'post',
		'cat'            => $kategori_id,
		'posts_per_page' => 5,
		'no_found_rows'  => true, // Optimasi query
	)
);
?>
<section class="ane-news-classic ane-blog-section">
	<div class="ane-col-column">
		<div class="blog-section-head">
			<div class="blog-section-title-wrap">
				<span class="blog-section-marker" aria-hidden="true"></span>
				<h2>
					<a href="<?php echo $kategori_url; ?>" <?php echo ( $kategori_url !== '#' ) ? 'rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( ! empty( $judul ) ? $judul : $kategori_nama ); ?>
					</a>
				</h2>
			</div>
			<span class="blog-section-line" aria-hidden="true"></span>
			<a class="blog-section-more" href="<?php echo $kategori_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" aria-label="<?php echo esc_attr( __( 'View All Posts in ', 'makhadane' ) . $kategori_nama ); ?>">
				<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
				<span class="blog-section-more-icon" aria-hidden="true"><?php echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) ); ?></span>
			</a>
		</div>

		<?php if ( $query->have_posts() ) : ?>
		<div class="ane-col-column">
			<?php $counter = 0; ?>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<?php if ( $counter === 0 ) : ?>
					<div class="ane-col-atas">
						<?php get_template_part( 'tp/content', 'klasik' ); ?>
					</div>
				<?php else : ?>
					<?php
					if ( $counter === 1 ) {
						echo '<div class="ane-col-row">';}
					?>
						<?php get_template_part( 'tp/content', 'klasik' ); ?>
					<?php
					if ( $counter === 4 ) {
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
