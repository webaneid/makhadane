<?php
/**
 * Featured Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_rows( 'ane_featured' ) ) : ?>
<section class="section-featured">
	<div class="ane-container">
		<?php $has_items = false; ?>
		<ul>
			<?php while ( have_rows( 'ane_featured' ) ) : the_row();
				$link      = get_sub_field( 'ane_link' );
				$image     = get_sub_field( 'ane_icon' );
				$judul     = get_sub_field( 'ane_title' );
				$deskripsi = get_sub_field( 'ane_deskripsi' );

				if ( ! empty( $judul ) || ! empty( $deskripsi ) || ( ! empty( $image ) && ! empty( $judul ) ) || ( ! empty( $link ) && isset( $link['url'], $link['title'] ) ) ) :
					$has_items = true;

					$has_link    = ! empty( $link ) && isset( $link['url'] );
					$link_url    = $has_link ? esc_url( $link['url'] ) : '';
					$link_target = ( $has_link && ! empty( $link['target'] ) ) ? ' target="' . esc_attr( $link['target'] ) . '"' : '';
					$link_rel    = ( $link_target === ' target="_blank"' ) ? ' rel="noopener noreferrer"' : '';
			?>
			<li>
				<div class="item">
					<?php if ( ! empty( $image ) && ! empty( $judul ) ) : ?>
					<div class="image">
						<?php if ( $has_link ) : ?>
							<a href="<?php echo $link_url; ?>"<?php echo $link_target . $link_rel; ?>>
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $judul ?: 'Featured Image' ); ?>" title="<?php echo esc_attr( $judul ); ?>">
							</a>
						<?php else : ?>
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $judul ?: 'Featured Image' ); ?>" title="<?php echo esc_attr( $judul ); ?>">
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<div class="content">
						<?php if ( ! empty( $judul ) ) : ?>
						<h4>
							<?php if ( $has_link ) : ?>
								<a href="<?php echo $link_url; ?>"<?php echo $link_target . $link_rel; ?>><?php echo wp_kses_post( $judul ); ?></a>
							<?php else : ?>
								<?php echo wp_kses_post( $judul ); ?>
							<?php endif; ?>
						</h4>
						<?php endif; ?>

						<?php if ( ! empty( $deskripsi ) ) : ?>
						<p><?php echo wp_kses_post( $deskripsi ); ?></p>
						<?php endif; ?>

						<?php if ( $has_link && isset( $link['title'] ) ) : ?>
						<a href="<?php echo $link_url; ?>" class="btn btn-primary"<?php echo $link_target . $link_rel; ?>>
							<?php echo esc_html( $link['title'] ); ?>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</li>
			<?php endif; endwhile; ?>
		</ul>
		<?php if ( ! $has_items ) : ?>
			<p><?php echo esc_html__( 'No posts found.', 'makhadane' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>
