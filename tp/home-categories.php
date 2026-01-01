<?php
/**
 * Home Categories Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$homekategori = get_sub_field('pilih_kategori');
$judul = get_sub_field('pilih_judul');
?>
<section class="home-kabar section-paddings">
	<div class="container boxane">
		<div class="row pt-15">
			<div class="section-title-item">
				<?php if(!empty($judul)): ?>
					<a href="<?php echo esc_url(get_category_link($homekategori->term_id)); ?>"><h1><?php echo esc_html( $judul ); ?></h1></a>
				<?php else: ?>
					<a href="<?php echo esc_url(get_category_link($homekategori->term_id)); ?>"><h1><?php echo esc_html( $homekategori->name ); ?></h1></a>
				<?php endif; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 p0-m">
				<?php
				$the_query = new WP_Query(array(
					'post_type' => 'post',
					'cat' => get_cat_ID(''. $homekategori->name .''),
					'posts_per_page' => 1
				));
				while ( $the_query->have_posts() ) : $the_query->the_post();
					get_template_part( 'tp/content', 'overlay' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<div class="col-md-6">
				<?php
				$the_query = new WP_Query(array(
					'post_type' => 'post',
					'cat' => get_cat_ID(''. $homekategori->name .''),
					'posts_per_page' => 4,
					'offset' => 1
				));
				while ( $the_query->have_posts() ) : $the_query->the_post();
					get_template_part( 'tp/content', 'imagesmalltitle' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

		</div>
	</div>
</section>
