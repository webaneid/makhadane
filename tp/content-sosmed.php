<?php
/**
 * Social Media Links Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<?php if ( have_rows( 'sosmed_ane', 'option' ) ) : ?>
<div class="ane-sosmed">
	<ul>
		<?php
		while ( have_rows( 'sosmed_ane', 'option' ) ) :
			the_row();
			$facebook  = esc_url( get_sub_field( 'sosmed_fb' ) );
			$whatsapp  = get_sub_field( 'sosmed_wa' );
			$instagram = esc_url( get_sub_field( 'sosmed_ig' ) );
			$twitter   = esc_url( get_sub_field( 'sosmed_tw' ) );
			$youtube   = esc_url( get_sub_field( 'sosmed_youtube' ) );
			?>

			<?php if ( ! empty( $whatsapp ) ) : ?>
			<li><a class="whatsapp" href="https://wa.me/<?php echo esc_attr( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><?php echo wp_kses_post( makhadane_icon( 'whatsapp' ) ); ?></a></li>
		<?php endif; ?>

			<?php if ( ! empty( $facebook ) ) : ?>
			<li><a class="facebook" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><?php echo wp_kses_post( makhadane_icon( 'facebook' ) ); ?></a></li>
		<?php endif; ?>

			<?php if ( ! empty( $twitter ) ) : ?>
			<li><a class="twitter" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><?php echo wp_kses_post( makhadane_icon( 'twitter' ) ); ?></a></li>
		<?php endif; ?>

			<?php if ( ! empty( $youtube ) ) : ?>
			<li><a class="youtube" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><?php echo wp_kses_post( makhadane_icon( 'youtube' ) ); ?></a></li>
		<?php endif; ?>

			<?php if ( ! empty( $instagram ) ) : ?>
			<li><a class="instagram" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><?php echo wp_kses_post( makhadane_icon( 'instagram' ) ); ?></a></li>
		<?php endif; ?>

		<?php endwhile; ?>
	</ul>
</div>
<?php endif; ?>
