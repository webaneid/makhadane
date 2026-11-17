<?php
/**
 * Footer Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

get_template_part( 'tp/mobile/menu-footer' );

$group = get_field( 'about_ane', 'option' );
$nama  = '';
if ( $group && isset( $group['company_name'] ) ) {
	$nama = $group['company_name'];
}
?>
<footer id="footer" class="ane-footer">
	<div class="footer-curve-top">
		<svg class="footer-curve-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 76" preserveAspectRatio="none" aria-hidden="true" focusable="false">
			<path d="M 0 76 L 0 70 L 580 70 C 642 70 662 6 720 6 C 778 6 798 70 860 70 L 1440 70 L 1440 76 Z"></path>
		</svg>
		<button type="button" class="footer-scroll-top-btn" aria-label="<?php esc_attr_e( 'Kembali ke atas', 'makhadane' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
				<path d="M12 19V5"></path>
				<path d="M6.5 10.5 12 5l5.5 5.5"></path>
			</svg>
		</button>
	</div>

	<h2 class="screen-reader-text"><?php esc_html_e( 'Informasi Situs', 'makhadane' ); ?></h2>
	<div class="atas">
		<div class="ane-container footer-inner">
			<div class="footer-brand-bar">
				<div class="footer-brand-logo">
					<?php
					$logoimg = get_theme_mod( 'logo', get_template_directory_uri() . '/img/logo-makhadane.png' );
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<a class="logo" href="' . esc_url( get_home_url() ) . '" rel="home"><img src="' . esc_url( $logoimg ) . '" width="250" height="61" loading="lazy" decoding="async" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"></a>';
					}
					?>
				</div>
				<div class="footer-brand-social">
					<?php
					if ( function_exists( 'ane_sosial_media' ) ) {
						ane_sosial_media();
					}
					?>
				</div>
			</div>

			<div class="footer-main-grid">
				<div class="footer-about-card">
					<div class="footer-content">
						<?php
						if ( function_exists( 'ane_about_company' ) ) {
							ane_about_company();
						}
						?>
					</div>
				</div>

				<div class="footer-col">
					<h3><?php esc_html_e( 'Shortcut Links', 'makhadane' ); ?></h3>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menufooter',
							'container_id'   => 'footer-menu',
							'container'      => 'div',
							'fallback_cb'    => 'wp_page_menu',
							'depth'          => 4,
						)
					);
					?>
				</div>

				<div class="footer-col">
					<h3><?php esc_html_e( 'Contact Us', 'makhadane' ); ?></h3>
					<?php
					if ( function_exists( 'ane_contact_info' ) ) {
						ane_contact_info();
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="bawah">
		<div class="ane-container">
			<div class="footer-copy">
				<?php
				$copyright_year = function_exists( 'ane_get_copyright_year' ) ? ane_get_copyright_year() : '&copy; ' . gmdate( 'Y' );
				$designed_by    = function_exists( 'ane_get_designed_by' ) ? ane_get_designed_by() : '';

				echo esc_html__( 'Copyright', 'makhadane' ) . ' ' . wp_kses_post( $copyright_year ) . ' ' . esc_html( (string) $nama ) . ' ' . wp_kses_post( $designed_by );
				?>
			</div>
		</div>
	</div>

</footer>


<?php
if ( esc_attr( get_field( 'ane_cs_aktif', 'option' ) ) ) :
	$makhadane_wa_numbers = array();
	if ( have_rows( 'ane_cs', 'option' ) ) {
		while ( have_rows( 'ane_cs', 'option' ) ) {
			the_row();
			$makhadane_wa_item = get_sub_field( 'ane_whatsapp' );
			if ( is_string( $makhadane_wa_item ) && '' !== trim( $makhadane_wa_item ) ) {
				$makhadane_wa_numbers[] = trim( $makhadane_wa_item );
			}
		}
	}
	?>
<div class="floating-chat" data-wa-numbers="<?php echo esc_attr( (string) wp_json_encode( $makhadane_wa_numbers ) ); ?>" data-wa-empty-msg="<?php esc_attr_e( 'WhatsApp number not available.', 'makhadane' ); ?>">
	<div class="chat">
		<div class="header">
			<?php
			$makhadane_cs_title = get_field( 'ane_cs_label', 'option' );
			if ( ! empty( $makhadane_cs_title ) ) :
				?>
				<span class="tombol"><?php echo esc_html( (string) $makhadane_cs_title ); ?></span>
			<?php else : ?>
				<span class="tombol"><?php esc_html_e( 'Layanan Informasi', 'makhadane' ); ?></span>
			<?php endif; ?>
			<button type="button" class="ane-close" aria-label="<?php esc_attr_e( 'Tutup chat', 'makhadane' ); ?>">
				<?php echo wp_kses_post( makhadane_icon( 'tutup' ) ); ?>
			</button>
		</div>
		<div class="mesej">
			<?php
			$makhadane_cs_msg = get_field( 'ane_cs_welcome', 'option' );
			if ( ! empty( $makhadane_cs_msg ) ) :
				?>
				<div class="mesej-header">
					<p><?php echo esc_html( (string) $makhadane_cs_msg ); ?></p>
				</div>
			<?php endif; ?>

			<div class="mesej-box">
				<input type="text" id="waMessage" class="wa-input" placeholder="<?php esc_attr_e( 'Ketik pesan...', 'makhadane' ); ?>">
				<button type="button" class="wa-button" id="sendWaButton" aria-label="<?php esc_attr_e( 'Kirim WhatsApp', 'makhadane' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M10 14l11 -11"></path>
						<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
					</svg>
				</button>
			</div>

			<?php if ( have_rows( 'ane_cs', 'option' ) ) : ?>
				<div class="body-pilihan">
					<h4><?php esc_html_e( 'Atau chat langsung dengan admin', 'makhadane' ); ?></h4>
				</div>
				<ul class="text-box">
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
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</div>
</body>
</html>
