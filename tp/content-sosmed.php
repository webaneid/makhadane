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
?>
<?php if( have_rows('sosmed_ane', 'option') ): ?>
<div class="ane-sosmed">
	<ul>
		<?php
			while( have_rows('sosmed_ane', 'option') ): the_row();
				$facebook = esc_url(get_sub_field('sosmed_fb'));
				$whatsapp = get_sub_field('sosmed_wa');
				$instagram = esc_url(get_sub_field('sosmed_ig'));
				$twitter = esc_url(get_sub_field('sosmed_tw'));
				$youtube = esc_url(get_sub_field('sosmed_youtube'));
		?>

		<?php if( !empty($whatsapp) ): ?>
			<li><a class="whatsapp" href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" target="_blank"><i class="ane-whatsapp"></i></a></li>
		<?php endif; ?>

		<?php if( !empty($facebook) ): ?>
			<li><a class="facebook" href="<?php echo esc_url($facebook); ?>" target="_blank"><i class="ane-facebook"></i></a></li>
		<?php endif; ?>

		<?php if( !empty($twitter) ): ?>
			<li><a class="twitter" href="<?php echo esc_url($twitter); ?>" target="_blank"><i class="ane-twitter"></i></a></li>
		<?php endif; ?>

		<?php if( !empty($youtube) ): ?>
			<li><a class="youtube" href="<?php echo esc_url($youtube); ?>" target="_blank"><i class="ane-youtube"></i></a></li>
		<?php endif; ?>

		<?php if( !empty($instagram) ): ?>
			<li><a class="instagram" href="<?php echo esc_url($instagram); ?>" target="_blank"><i class="ane-instagram"></i></a></li>
		<?php endif; ?>

		<?php endwhile; ?>
	</ul>
</div>
<?php endif; ?>
