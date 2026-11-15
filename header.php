<?php
/**
 * Header Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />

	<!-- Favicon -->
	<?php if ( has_site_icon() ) : ?>
		<link rel="icon" href="<?php echo esc_url( get_site_icon_url() ); ?>" type="image/x-icon">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_site_icon_url( 180 ) ); ?>">
	<?php else : ?>
		<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/img/default-favicon.ico' ); ?>" type="image/x-icon">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_template_directory_uri() . '/img/default-apple-icon.png' ); ?>">
	<?php endif; ?>

	<?php
	$logoimg = get_theme_mod( 'logo', get_template_directory_uri() . '/img/logo-makhadane.png' );
	?>

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>
<div class="webane-wrapper">
	<header class="ane-header">
		<div class="bawah" 
		<?php
		if ( get_field( 'ane_display_topheader', 'option' ) ) {
			echo 'style="padding-top:45px"'; }
		?>
		>
			<div class="desktop-menu">
				<div class="ane-container">
					<div class="desktop-menu-row">
						<div class="ane-logo">
							<?php
							if ( has_custom_logo() ) {
								the_custom_logo();
							} else {
								echo '<a class="logo" href="' . esc_url( get_home_url() ) . '" rel="home">
									<img src="' . esc_url( $logoimg ) . '" width="250" height="61" decoding="async" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">
								</a>';
							}
							?>
						</div>
						<div class="ane-menu">
							<nav id="mobile-menu-active">
								<?php
								wp_nav_menu(
									array(
										'theme_location' => 'menuutama',
										'container_id'   => 'main-menu',
										'echo'           => true,
										'fallback_cb'    => 'wp_page_menu',
										'items_wrap'     => '<ul class="main-menu">%3$s</ul>',
										'depth'          => 4,
									)
								);
								?>
							</nav>
						</div>
						<?php
						$makhadane_contact    = get_field( 'contact_ane', 'option' );
						$makhadane_phone_raw  = ( is_array( $makhadane_contact ) && ! empty( $makhadane_contact['kontak_telepon'] ) ) ? trim( (string) $makhadane_contact['kontak_telepon'] ) : '';
						$makhadane_phone_href = '' !== $makhadane_phone_raw ? (string) preg_replace( '/[^0-9+]/', '', $makhadane_phone_raw ) : '';

						$makhadane_sosmed = get_field( 'sosmed_ane', 'option' );
						$makhadane_wa_raw = ( is_array( $makhadane_sosmed ) && ! empty( $makhadane_sosmed['sosmed_wa'] ) ) ? trim( (string) $makhadane_sosmed['sosmed_wa'] ) : '';
						if ( '' === $makhadane_wa_raw ) {
							$makhadane_cs = get_field( 'ane_cs', 'option' );
							if ( is_array( $makhadane_cs ) && ! empty( $makhadane_cs[0]['ane_whatsapp'] ) ) {
								$makhadane_wa_raw = trim( (string) $makhadane_cs[0]['ane_whatsapp'] );
							}
						}
						$makhadane_wa_digits = '' !== $makhadane_wa_raw ? (string) preg_replace( '/[^0-9]/', '', $makhadane_wa_raw ) : '';

						$makhadane_header_link = get_field( 'ane_link_top', 'option' );
						$makhadane_cta_url     = ( is_array( $makhadane_header_link ) && ! empty( $makhadane_header_link['url'] ) ) ? trim( (string) $makhadane_header_link['url'] ) : '';
						$makhadane_cta_title   = ( is_array( $makhadane_header_link ) && ! empty( $makhadane_header_link['title'] ) ) ? trim( (string) $makhadane_header_link['title'] ) : '';
						$makhadane_cta_target  = ( is_array( $makhadane_header_link ) && ! empty( $makhadane_header_link['target'] ) ) ? trim( (string) $makhadane_header_link['target'] ) : '';

						if ( '' !== $makhadane_phone_href || '' !== $makhadane_wa_digits || ( '' !== $makhadane_cta_url && '' !== $makhadane_cta_title ) ) :
							?>
							<div class="ane-header-actions">
								<?php if ( '' !== $makhadane_phone_href ) : ?>
									<a class="ane-header-icon-btn" href="tel:<?php echo esc_attr( $makhadane_phone_href ); ?>" aria-label="<?php esc_attr_e( 'Telepon', 'makhadane' ); ?>" title="<?php echo esc_attr( $makhadane_phone_raw ); ?>">
										<?php echo wp_kses_post( makhadane_icon( 'telepon' ) ); ?>
									</a>
								<?php endif; ?>

								<?php if ( '' !== $makhadane_wa_digits ) : ?>
									<a class="ane-header-icon-btn" href="https://wa.me/<?php echo esc_attr( $makhadane_wa_digits ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp', 'makhadane' ); ?>">
										<?php echo wp_kses_post( makhadane_icon( 'whatsapp' ) ); ?>
									</a>
								<?php endif; ?>

								<?php if ( '' !== $makhadane_cta_url && '' !== $makhadane_cta_title ) : ?>
									<a id="klik-header" class="ane-header-cta" href="<?php echo esc_url( $makhadane_cta_url ); ?>"<?php echo '_blank' === $makhadane_cta_target ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
										<?php echo esc_html( $makhadane_cta_title ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ( get_field( 'ane_display_topheader', 'option' ) ) : ?>
			<div class="bg-bottom">
				<div class="container">
					<div class="ane-top-isi">
						<div class="ane-kiri">
							<div class="marquee-news">
								<?php
								if ( function_exists( 'ane_get_marquee_news' ) ) {
									$news_items = ane_get_marquee_news();
									if ( ! empty( $news_items ) ) {
										echo '<marquee class="news-items" scrollamount="4" onMouseOver="this.stop()" onMouseOut="this.start()">';
										foreach ( $news_items as $item ) {
											echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a> &nbsp;|&nbsp; ';
										}
										echo '</marquee>';
									}
								}
								?>
							</div>
						</div>
						<div class="ane-kanan">
							<?php echo wp_kses_post( makhadane_icon( 'calendar' ) ); ?> <?php echo esc_html( date_i18n( 'l, j F Y' ) ); ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="primary-mobile-menu">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<a class="logo" href="' . esc_url( get_home_url() ) . '" rel="home">
					<img src="' . esc_url( $logoimg ) . '" width="250" height="61" decoding="async" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">
				</a>';
			}
			?>
			<div class="mobile-menu-area">
				<div class="mobile-menu">

				</div>
			</div>
		</div>
	</header>
