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

$makhadane_homekategori  = get_sub_field( 'pilih_kategori' );
$makhadane_judul         = trim( (string) get_sub_field( 'custom_title' ) );
$makhadane_kategori_id   = ( is_object( $makhadane_homekategori ) && isset( $makhadane_homekategori->term_id ) ) ? (int) $makhadane_homekategori->term_id : 0;
$makhadane_kategori_nama = ( is_object( $makhadane_homekategori ) && isset( $makhadane_homekategori->name ) ) ? (string) $makhadane_homekategori->name : '';
$makhadane_kategori_url  = $makhadane_kategori_id ? (string) get_category_link( $makhadane_kategori_id ) : '#';
$makhadane_heading       = '' !== $makhadane_judul ? $makhadane_judul : $makhadane_kategori_nama;

$makhadane_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'cat'                 => $makhadane_kategori_id,
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="ane-news-classic ane-blog-section">
	<?php if ( '' !== $makhadane_heading ) : ?>
		<div class="blog-section-head">
			<div class="blog-section-title-wrap">
				<span class="blog-section-marker" aria-hidden="true"></span>
				<h2>
					<a href="<?php echo esc_url( $makhadane_kategori_url ); ?>"<?php echo ( '#' !== $makhadane_kategori_url ) ? ' rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( $makhadane_heading ); ?>
					</a>
				</h2>
			</div>
			<span class="blog-section-line" aria-hidden="true"></span>
			<a class="blog-section-more" href="<?php echo esc_url( $makhadane_kategori_url ); ?>" aria-label="<?php echo esc_attr( __( 'View All Posts in ', 'makhadane' ) . $makhadane_kategori_nama ); ?>">
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
				?>
				<div class="ane-col-atas">
					<?php get_template_part( 'tp/content', 'klasik' ); ?>
				</div>
				<?php if ( $makhadane_query->post_count > 1 ) : ?>
					<div class="ane-col-row">
				<?php endif; ?>
			<?php else : ?>
				<?php get_template_part( 'tp/content', 'klasik' ); ?>
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
</section>
