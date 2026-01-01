<?php
/**
 * Single Ustadz Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thumbImg    = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
$photo       = has_post_thumbnail() ? esc_url( $thumbImg[0] ) : esc_url( ane_dummy_thumbnail() );

$thumkotak   = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'kotak' );
$photokotak  = has_post_thumbnail() ? esc_url( $thumkotak[0] ) : esc_url( ane_dummy_kotak() );

$pelajaran = get_the_terms( get_the_ID(), 'pelajaran' );
$kelas     = get_the_terms( get_the_ID(), 'Kelas' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-post-single ane-single-ustadz' ); ?>>
	<header class="entry-header">
		<div class="single-post-header" style="background: url('<?php echo esc_url( $photo ); ?>')"></div>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="entry-content">
		<div class="ane-image">
			<img src="<?php echo esc_url( $photokotak ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
		</div>

		<div class="ane-content">
			<div class="ane-single-title">
				<h1 class="post-title"><?php the_title(); ?></h1>
			</div>

			<div class="share-this">
				<?php echo ane_social_share(); ?>
			</div>

			<figure class="wp-block-table">
				<table>
					<tbody>
						<tr>
							<td><?php esc_html_e( 'Subject', 'makhadane' ); ?></td>
							<td>
								<div class="content-category">
									<?php
									if ( $pelajaran && ! is_wp_error( $pelajaran ) ) {
										foreach ( $pelajaran as $pel ) {
											echo '<a class="post-cat" href="' . esc_url( get_term_link( $pel ) ) . '">' . esc_html( $pel->name ) . '</a>';
										}
									}
									?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Class', 'makhadane' ); ?></td>
							<td>
								<div class="content-category">
									<?php
									if ( $kelas && ! is_wp_error( $kelas ) ) {
										foreach ( $kelas as $kel ) {
											echo '<a class="post-cat" href="' . esc_url( get_term_link( $kel ) ) . '">' . esc_html( $kel->name ) . '</a>';
										}
									}
									?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</figure>

			<?php the_content(); ?>

		</div>

		<div class="konten-terkait-v2">
			<?php
			$terms = get_the_terms( $post->ID, 'Kelas' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				$related_query = new WP_Query(
					array(
						'post_type'           => 'ustadz',
						'tax_query'           => array(
							array(
								'taxonomy' => 'Kelas',
								'field'    => 'id',
								'terms'    => $term_ids,
								'operator' => 'IN',
							),
						),
						'posts_per_page'      => 4,
						'ignore_sticky_posts' => 1,
						'orderby'             => 'rand',
						'post__not_in'        => array( $post->ID ),
					)
				);

				if ( $related_query->have_posts() ) :
					$first_term = reset( $terms );
					?>
					<h2><?php echo esc_html( $first_term->name ) . ' ' . esc_html__( 'Teachers', 'makhadane' ); ?></h2>
					<div class="testimoni-slider owl-carousel">
						<?php
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							get_template_part( 'tp/content', 'ustadz' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php endif;
			}
			?>
		</div>

		<div class="konten-terkait-v2">
			<?php
			$terms = get_the_terms( $post->ID, 'pelajaran' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				$related_query = new WP_Query(
					array(
						'post_type'           => 'ustadz',
						'tax_query'           => array(
							array(
								'taxonomy' => 'pelajaran',
								'field'    => 'id',
								'terms'    => $term_ids,
								'operator' => 'IN',
							),
						),
						'posts_per_page'      => 4,
						'ignore_sticky_posts' => 1,
						'orderby'             => 'rand',
						'post__not_in'        => array( $post->ID ),
					)
				);

				if ( $related_query->have_posts() ) :
					$first_term = reset( $terms );
					?>
					<h2><?php echo esc_html( $first_term->name ) . ' ' . esc_html__( 'Teachers', 'makhadane' ); ?></h2>
					<div class="testimoni-slider owl-carousel">
						<?php
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							get_template_part( 'tp/content', 'ustadz' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php endif;
			}
			?>
		</div>

	</div>
</article>
