<?php
/**
 * Contact Page Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$makhadane_photo    = has_post_thumbnail() ? (string) get_the_post_thumbnail_url( get_the_ID(), 'large' ) : ane_dummy_thumbnail();
$makhadane_location = get_field( 'gmap', 'option' );
$makhadane_contact  = get_field( 'contact_ane', 'option' );
$makhadane_contact  = is_array( $makhadane_contact ) ? $makhadane_contact : array();

$makhadane_telephone = trim( (string) ( $makhadane_contact['kontak_telepon'] ?? '' ) );
$makhadane_email     = trim( (string) ( $makhadane_contact['kontak_email'] ?? '' ) );
$makhadane_alamat    = function_exists( 'ane_get_alamat' ) ? (string) ane_get_alamat() : '';

$makhadane_address = array(
	'@type'           => 'PostalAddress',
	'streetAddress'   => wp_strip_all_tags( (string) ( $makhadane_contact['kontak_alamat'] ?? '' ) ),
	'addressLocality' => wp_strip_all_tags( (string) ( $makhadane_contact['kontak_kabupaten'] ?? '' ) ),
	'addressRegion'   => wp_strip_all_tags( (string) ( $makhadane_contact['kontak_provinsi'] ?? '' ) ),
	'postalCode'      => wp_strip_all_tags( (string) ( $makhadane_contact['kontak_kodepos'] ?? '' ) ),
	'addressCountry'  => 'ID',
);

$makhadane_schema_data = array(
	'@context'        => 'https://schema.org',
	'@type'           => 'ContactPage',
	'name'            => get_the_title(),
	'url'             => esc_url( (string) get_permalink() ),
	'description'     => get_the_excerpt() ? wp_strip_all_tags( (string) get_the_excerpt() ) : get_bloginfo( 'description' ),
	'contactPoint'    => array(
		'@type'             => 'ContactPoint',
		'telephone'         => $makhadane_telephone,
		'email'             => $makhadane_email,
		'contactType'       => 'customer service',
		'areaServed'        => 'ID',
		'availableLanguage' => array( 'Indonesian', 'Arabic', 'English' ),
	),
	'address'         => $makhadane_address,
	'potentialAction' => array(
		'@type'  => 'CommunicateAction',
		'target' => esc_url( (string) get_permalink() ),
		'name'   => 'Contact Us',
	),
);

if ( is_array( $makhadane_location ) && isset( $makhadane_location['lat'], $makhadane_location['lng'] ) ) {
	$makhadane_schema_data['geo'] = array(
		'@type'     => 'GeoCoordinates',
		'latitude'  => (string) $makhadane_location['lat'],
		'longitude' => (string) $makhadane_location['lng'],
	);
}
?>

<script type="application/ld+json">
	<?php echo wp_json_encode( $makhadane_schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
</script>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-page-default ane-page-kontak' ); ?>>
	<header class="entry-header" style="background-image: url('<?php echo esc_url( $makhadane_photo ); ?>');">
		<div class="ane-container kontak-header-inner">
			<h1><?php echo esc_html( get_the_title() ); ?></h1>
			<p class="kontak-header-sub"><?php esc_html_e( 'Hubungi kami untuk informasi pendaftaran, program pendidikan, maupun kerja sama.', 'makhadane' ); ?></p>
		</div>
	</header>

	<?php
	if ( function_exists( 'ane_display_breadcrumbs' ) ) {
		ane_display_breadcrumbs();
	}
	?>

	<div class="ane-container kontak-wrap">
		<?php if ( ! empty( $makhadane_contact['kontak_alamat'] ) || '' !== $makhadane_telephone || '' !== $makhadane_email ) : ?>
			<div class="kontak-info-grid">
				<?php if ( ! empty( $makhadane_contact['kontak_alamat'] ) ) : ?>
					<div class="kontak-info-card">
						<span class="kontak-info-icon" aria-hidden="true">
							<?php echo wp_kses_post( makhadane_icon( 'lokasi' ) ); ?>
						</span>
						<div class="kontak-info-body">
							<h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
							<p><?php echo wp_kses_post( $makhadane_alamat ); ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_telephone ) : ?>
					<div class="kontak-info-card">
						<span class="kontak-info-icon" aria-hidden="true">
							<?php echo wp_kses_post( makhadane_icon( 'telepon' ) ); ?>
						</span>
						<div class="kontak-info-body">
							<h2><?php esc_html_e( 'Telepon', 'makhadane' ); ?></h2>
							<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $makhadane_telephone ) ); ?>"><?php echo esc_html( $makhadane_telephone ); ?></a></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_email ) : ?>
					<div class="kontak-info-card">
						<span class="kontak-info-icon" aria-hidden="true">
							<?php echo wp_kses_post( makhadane_icon( 'email' ) ); ?>
						</span>
						<div class="kontak-info-body">
							<h2><?php esc_html_e( 'Email', 'makhadane' ); ?></h2>
							<p><a href="mailto:<?php echo esc_attr( $makhadane_email ); ?>"><?php echo esc_html( $makhadane_email ); ?></a></p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="kontak-main-grid">
			<div class="kontak-form-card">
				<h2 class="widget-title ane-title"><?php esc_html_e( 'Send Us a Message', 'makhadane' ); ?></h2>
				<div class="kontak-form-body">
					<?php the_content(); ?>
				</div>
			</div>

			<div class="kontak-side-col">
				<?php if ( is_array( $makhadane_location ) && isset( $makhadane_location['lat'], $makhadane_location['lng'] ) ) : ?>
					<div class="kontak-map">
						<div class="acf-map">
							<div class="marker" data-lat="<?php echo esc_attr( (string) $makhadane_location['lat'] ); ?>" data-lng="<?php echo esc_attr( (string) $makhadane_location['lng'] ); ?>">
								<a class="directions" href="https://www.google.com/maps?saddr=My+Location&daddr=<?php echo esc_attr( (string) $makhadane_location['lat'] . ',' . (string) $makhadane_location['lng'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Get Directions to', 'makhadane' ); ?> <?php echo esc_html( (string) ( $makhadane_location['address'] ?? '' ) ); ?>
								</a>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( have_rows( 'ane_cs', 'option' ) ) : ?>
					<div class="kontak-cs-card">
						<h2 class="widget-title ane-title"><?php esc_html_e( 'Fast Response', 'makhadane' ); ?></h2>
						<ul class="kontak-cs-list">
							<?php
							while ( have_rows( 'ane_cs', 'option' ) ) :
								the_row();
								$makhadane_wa    = (string) get_sub_field( 'ane_whatsapp' );
								$makhadane_image = get_sub_field( 'ane_image' );
								$makhadane_nama  = (string) get_sub_field( 'ane_nama' );
								$makhadane_area  = (string) get_sub_field( 'ane_area' );
								$makhadane_thumb = ( is_array( $makhadane_image ) && ! empty( $makhadane_image['sizes']['kotak'] ) ) ? (string) $makhadane_image['sizes']['kotak'] : '';
								?>
								<li>
									<a class="floating-chat-agent" href="https://wa.me/<?php echo esc_attr( $makhadane_wa ); ?>" target="_blank" rel="noopener noreferrer">
										<?php if ( '' !== $makhadane_thumb ) : ?>
											<img src="<?php echo esc_url( $makhadane_thumb ); ?>" width="42" height="42" alt="<?php echo esc_attr( $makhadane_nama ); ?>" loading="lazy" decoding="async">
										<?php endif; ?>
										<span class="floating-chat-agent-info">
											<strong><?php echo esc_html( $makhadane_nama ); ?></strong>
											<?php if ( '' !== $makhadane_area ) : ?>
												<span><?php echo esc_html( $makhadane_area ); ?></span>
											<?php endif; ?>
										</span>
										<span class="floating-chat-agent-arrow" aria-hidden="true">
											<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
										</span>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
