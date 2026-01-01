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
	<link rel="preload" as="image" href="<?php echo esc_url( $logoimg ); ?>">

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
		<div class="bawah" <?php if ( get_field( 'ane_display_topheader', 'option' ) ) { echo 'style="padding-top:45px"'; } ?>>
			<div class="desktop-menu">
				<div class="ane-container">
					<div class="desktop-menu-row">
						<div class="ane-logo">
							<?php
							if ( has_custom_logo() ) {
								the_custom_logo();
							} else {
								echo '<a class="logo" href="' . esc_url( get_home_url() ) . '" rel="home">
									<img src="' . esc_url( $logoimg ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">
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
						$link = get_field( 'ane_link_top', 'option' );
						if ( ! empty( $link ) ) :
							?>
							<div class="ane-link-top">
								<a id="klik-header" class="btn-top" href="<?php echo esc_url( $link['url'] ); ?>">
									<?php echo esc_html( $link['title'] ); ?><i class="ane-chevron-right"></i>
								</a>
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
							<i class="ane-kalender"></i> <?php echo esc_html( date_i18n( 'l, j F Y' ) ); ?>
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
					<img src="' . esc_url( $logoimg ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">
				</a>';
			}
			?>
			<div class="mobile-menu-area">
				<div class="mobile-menu">

				</div>
			</div>
		</div>
	</header>
