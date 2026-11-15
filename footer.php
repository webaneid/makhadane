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
	<h2 class="screen-reader-text"><?php esc_html_e( 'Informasi Situs', 'makhadane' ); ?></h2>
	<div class="atas">
		<div class="ane-container mplr">
			<div class="ane-atas">
				<div class="kiri">
					<?php
					$logoimg = get_theme_mod( 'logo', get_template_directory_uri() . '/img/logo-makhadane.png' );
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<a class="logo" href="' . esc_url( get_home_url() ) . '" rel="home"><img src="' . esc_url( $logoimg ) . '" width="250" height="61" loading="lazy" decoding="async" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"></a>';
					}
					?>
				</div>
				<div class="kanan">
					<?php
					if ( function_exists( 'ane_sosial_media' ) ) {
						ane_sosial_media();
					}
					?>
				</div>
			</div>
			<div class="ane-col-row">
				<div class="ane-item">
					<div class="isi">
						<div class="footer-content">
							<?php
							if ( function_exists( 'ane_about_company' ) ) {
								ane_about_company();
							}
							?>
						</div>
					</div>
				</div>

				<div class="ane-item">
					<div class="isi-v2">
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
				</div>
				<div class="ane-item">
					<div class="isi-v2">
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
	</div>

	<div class="bawah">
		<div class="ane-container">
			<div class="isi mplr">
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
			$cs_title = get_field( 'ane_cs_label', 'option' );
			if ( ! empty( $cs_title ) ) :
				?>
				<span class="tombol"><?php echo esc_html( $cs_title ); ?></span>
			<?php endif; ?>
			<div class="ane-close">
				<?php echo wp_kses_post( makhadane_icon( 'tutup' ) ); ?>
			</div>
		</div>
		<div class="mesej">
			<div class="mesej-header">
				<?php
				$msg = get_field( 'ane_cs_welcome', 'option' );
				if ( ! empty( $msg ) ) :
					?>
					<p><?php echo esc_html( $msg ); ?></p>
				<?php endif; ?>
			</div>

			<div class="mesej-box">
				<input type="text" id="waMessage" class="wa-input" placeholder="Ketik pesan...">
				<button type="button" class="wa-button" id="sendWaButton" aria-label="<?php esc_attr_e( 'Kirim WhatsApp', 'makhadane' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f1f1f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tabler-icon tabler-icon-send">
						<path d="M10 14l11 -11"></path>
						<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
					</svg>
				</button>
			</div>

			<?php if ( have_rows( 'ane_cs', 'option' ) ) : ?>
			<ul class="text-box">
				<div class="body-pilihan">
					<h4><?php esc_html_e( 'Atau Chat langsung dengan Admin kami', 'makhadane' ); ?></h4>
				</div>

				<?php
				while ( have_rows( 'ane_cs', 'option' ) ) :
					the_row();
					$wa = get_sub_field( 'ane_whatsapp' );
					?>
					<li>
						<?php
						$image = get_sub_field( 'ane_image' );
						$nama  = get_sub_field( 'ane_nama' );
						$area  = get_sub_field( 'ane_area' );

						if ( $image ) :
							$size  = 'kotak';
							$thumb = $image['sizes'][ $size ];
							if ( ! empty( $thumb ) ) :
								?>
								<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $nama ); ?>">
								<?php
							endif;
						endif;
						?>
						<a href="https://wa.me/<?php echo esc_attr( $wa ); ?>" target="_blank" rel="noopener noreferrer">
							<span><?php echo esc_html( $nama ); ?></span><br/>
							<?php echo esc_html( $area ); ?>
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
