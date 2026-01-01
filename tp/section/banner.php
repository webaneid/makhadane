<?php
/**
 * Banner Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$perusahaan = get_sub_field( 'home_banner_perusahaan' );
$url        = get_sub_field( 'home_banner_url' );
$nama       = get_sub_field( 'home_banner_name' );
$image      = get_sub_field( 'home_banner_image' );
$start      = get_sub_field( 'home_banner_start' );
$end        = get_sub_field( 'home_banner_end' );

$perusahaan = ! empty( $perusahaan ) ? esc_html( $perusahaan ) : '';
$url        = ! empty( $url ) ? esc_url( $url ) : '#';
$nama       = ! empty( $nama ) ? esc_html( $nama ) : 'Advertisement';
$image      = ! empty( $image ) ? esc_url( $image ) : '';

$mulai  = ! empty( $start ) ? ( strtotime( $start ) - strtotime( 'today' ) ) / ( 60 * 60 * 24 ) : null;
$akhir  = ! empty( $end ) ? ( strtotime( $end ) - strtotime( 'today' ) ) / ( 60 * 60 * 24 ) : null;
$online = ! is_null( $akhir ) ? $akhir : -1;

if ( ! is_null( $mulai ) && ( $mulai <= 0 || $online >= 0 ) ) :
?>
	<section class="section-banner">
		<div class="ane-container">
			<a href="<?php echo $url; ?>" target="_blank" title="<?php echo esc_attr( $nama . ' by ' . $perusahaan ); ?>">
				<div class="ane-image">
					<?php if ( ! empty( $image ) ) : ?>
						<img src="<?php echo $image; ?>" alt="<?php echo esc_attr( $nama . ' by ' . $perusahaan ); ?>" title="<?php echo esc_attr( $nama . ' by ' . $perusahaan ); ?>">
					<?php endif; ?>
					<div class="banner-info">
						Ad by <?php echo $perusahaan; ?>
					</div>
				</div>
			</a>
		</div>
	</section>
<?php endif; ?>
