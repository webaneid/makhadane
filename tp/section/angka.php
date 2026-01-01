<?php
/**
 * School Data Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_rows( 'ane_data_sekolah' ) ) : ?>
	<div class="ane-data-content">
		<?php while ( have_rows( 'ane_data_sekolah' ) ) : the_row();
			$icon  = get_sub_field( 'ane_icon' );
			$title = get_sub_field( 'ane_title' );
			$jml   = get_sub_field( 'ane_jumlah' );

			$icon  = ! empty( $icon ) ? esc_attr( $icon ) : '';
			$title = ! empty( $title ) ? esc_html( $title ) : '';
			$jml   = ! empty( $jml ) ? esc_html( $jml ) : '';

			if ( ! empty( $icon ) && ! empty( $title ) && ! empty( $jml ) ) :
		?>
			<div class="ane-item">
				<i class="fa <?php echo $icon; ?>"></i>
				<h2 class="mb-0 fw-bold"><?php echo $jml; ?></h2>
				<p class="mb-0"><?php echo $title; ?></p>
			</div>
		<?php endif; endwhile; ?>
	</div>
<?php endif; ?>
