<?php
/**
 * Mobile Footer Menu Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped

$urlhome = home_url( '/' );

if ( have_rows( 'ane_mobile_menu', 'option' ) ) :
	?>
	<div id="footer-bar" class="footer-bar-1">
		<a href="<?php echo esc_url( $urlhome ); ?>" rel="noopener noreferrer" aria-label="Home">
			<?php echo wp_kses_post( makhadane_icon( 'home' ) ); ?><span>Home</span>
		</a>
		<?php
		while ( have_rows( 'ane_mobile_menu', 'option' ) ) :
			the_row();
			$link = get_sub_field( 'ane_link' );
			$icon = get_sub_field( 'ane_icon' );

			if ( $link && ! empty( $link['url'] ) ) :
				$link_url   = esc_url( $link['url'] );
				$link_title = esc_html( $link['title'] );
				$icon_html  = $icon ? wp_kses_post( makhadane_icon( (string) $icon ) ) . ' ' : '';
				?>
				<a href="<?php echo $link_url; ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link_title ); ?>">
					<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo $link_title; ?></span>
				</a>
				<?php
			endif;
		endwhile;
		?>
	</div>
	<?php
endif;
