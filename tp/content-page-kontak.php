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

// Cek apakah ada featured image, jika tidak gunakan thumbnail default
$photo = has_post_thumbnail() ? wp_get_attachment_image_url(get_post_thumbnail_id(), 'large') : ane_dummy_thumbnail();

// Ambil data kontak dari ACF
$location = get_field('gmap', 'option');
$contact  = get_field('contact_ane', 'option');

$telephone = esc_html($contact['kontak_telepon'] ?? '');
$email     = esc_html($contact['kontak_email'] ?? '');

// Data alamat
$address = [
	"@type"            => "PostalAddress",
	"streetAddress"    => esc_html($contact['kontak_alamat'] ?? ''),
	"addressLocality"  => esc_html($contact['kontak_kabupaten'] ?? ''),
	"addressRegion"    => esc_html($contact['kontak_provinsi'] ?? ''),
	"postalCode"       => esc_html($contact['kontak_kodepos'] ?? ''),
	"addressCountry"   => "ID"
];

// JSON-LD Schema untuk Halaman Kontak
$schema_data = [
	"@context" => "https://schema.org",
	"@type"    => "ContactPage",
	"name"     => get_the_title(),
	"url"      => esc_url(get_permalink()),
	"description" => get_the_excerpt() ? wp_kses_post(get_the_excerpt()) : esc_html(get_bloginfo('description')),
	"contactPoint" => [
		"@type"        => "ContactPoint",
		"telephone"    => $telephone,
		"email"        => $email,
		"contactType"  => "customer service",
		"areaServed"   => "ID",
		"availableLanguage" => ["Indonesian", "Arabic", "English"]
	],
	"address" => $address,
	"potentialAction" => [
		"@type"   => "CommunicateAction",
		"target"  => esc_url(get_permalink()),
		"name"    => "Contact Us"
	]
];

// Jika ada lokasi di Google Maps, tambahkan koordinatnya
if ($location) {
	$schema_data["geo"] = [
		"@type"     => "GeoCoordinates",
		"latitude"  => esc_attr($location['lat']),
		"longitude" => esc_attr($location['lng'])
	];
}
?>

<script type="application/ld+json">
	<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<?php get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('ane-page-default ane-page-kontak'); ?>>

	<header class="entry-header" style="background-image: url('<?php echo esc_url($photo); ?>');">
		<?php the_title('<h1>', '</h1>'); ?>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="ane-container">
		<div class="ane-col-column mplr">
			<div class="kontak-konten">
				<div class="ane-col-2">
					<div class="ane-item">
						<?php if (!empty($contact['kontak_alamat'])) : ?>
							<div class="alamat">
								<h3><?php bloginfo('name'); ?></h3>
								<p><?php echo ane_get_alamat() ?></p>
							</div>
						<?php endif; ?>
					</div>

					<div class="ane-item">
						<?php if ($telephone || $email) : ?>
							<div class="kontak text-right">
								<?php if ($telephone) : ?>
									<p><?php esc_html_e('Phone:', 'makhadane'); ?> <?php echo esc_html($telephone); ?></p>
								<?php endif; ?>

								<?php if ($email) : ?>
									<p><?php esc_html_e('e-Mail:', 'makhadane'); ?> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="ane-col-column hidden-desktop">
			<?php if (have_rows('ane_cs','option')): ?>
				<ul class="ane-cs-mobile mplr">
					<div class="body-pilihan">
						<h4><?php esc_html_e( 'Fast Response', 'makhadane' ); ?></h4>
					</div>

					<?php while (have_rows('ane_cs','option')) : the_row();
						$wa = get_sub_field('ane_whatsapp'); ?>
						<li>
							<?php
							$image = get_sub_field('ane_image');
							$nama = get_sub_field('ane_nama');
							$area = get_sub_field('ane_area');

							if ($image):
								$size = 'kotak';
								$thumb = $image['sizes'][$size];
								if (!empty($thumb)): ?>
									<img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($nama); ?>">
								<?php endif;
							endif; ?>
							<a href="https://wa.me/<?php echo esc_attr($wa); ?>" target="_blank">
								<span><?php echo esc_html($nama); ?></span><br/>
								<?php echo esc_html($area); ?>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="ane-col-2">
			<div class="ane-item mplr">
				<div class="entry-content">
					<div class="ane-title">
						<h1><?php esc_html_e('Send Us a Message', 'makhadane'); ?></h1>
					</div>
					<?php the_content(); ?>
				</div>
			</div>

			<?php if ($location) : ?>
				<div class="ane-item">
					<div class="kontak-map">
						<div class="acf-map">
							<div class="marker" data-lat="<?php echo esc_attr($location['lat']); ?>" data-lng="<?php echo esc_attr($location['lng']); ?>">
								<a class="directions" href="https://www.google.com/maps?saddr=My+Location&daddr=<?php echo esc_attr($location['lat'] . ',' . $location['lng']); ?>">
									<?php esc_html_e('Get Directions to', 'makhadane'); ?> <?php echo esc_html($location['address']); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</article>

<?php get_footer(); ?>
