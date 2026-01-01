<?php
/**
 * Mobile Institutional Menu Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_rows( 'ane_list_lembaga', 'option' ) ) :
	?>
	<nav class="menu-lembaga">
		<h3><?php esc_html_e( 'Jelajahi Informasi Lain:', 'makhadane' ); ?></h3>
		<ul>
			<?php
			while ( have_rows( 'ane_list_lembaga', 'option' ) ) :
				the_row();
				$link = get_sub_field( 'ane_link' );
				$icon = get_sub_field( 'ane_icon' );

				if ( $link && ! empty( $link['url'] ) ) :
					$link_url   = esc_url( $link['url'] );
					$link_title = esc_html( $link['title'] );
					$icon_html  = $icon ? '<i class="fa ' . esc_attr( $icon ) . '"></i> ' : '';
					?>
					<li>
						<a href="<?php echo $link_url; ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link_title ); ?>">
							<?php echo $icon_html; ?>
							<span><?php echo $link_title; ?></span>
						</a>
					</li>
					<?php
				endif;
			endwhile;
			?>
		</ul>
	</nav>
	<?php
endif;
