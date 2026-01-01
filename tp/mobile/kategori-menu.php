<?php
/**
 * Mobile Category Menu Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ane-mobile-list">
	<?php if ( have_rows( 'ane_menu_kategori', 'option' ) ) : ?>
		<nav class="ane-mobile-nav">
			<ul>
				<?php
				while ( have_rows( 'ane_menu_kategori', 'option' ) ) :
					the_row();
					$kategori = get_sub_field( 'pilih_kategori' );
					$icon     = get_sub_field( 'ane_icon' );

					if ( $kategori && ! empty( $kategori->term_id ) ) :
						$category_link = esc_url( get_category_link( $kategori->term_id ) );
						$category_name = esc_html( $kategori->name );
						$icon_html     = $icon ? '<i class="fa ' . esc_attr( $icon ) . '"></i> ' : '';
						?>
						<li>
							<a href="<?php echo $category_link; ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $category_name ); ?>">
								<?php echo $icon_html; ?>
								<span><?php echo $category_name; ?></span>
							</a>
						</li>
						<?php
					endif;
				endwhile;
				?>
			</ul>
		</nav>
	<?php endif; ?>
</div>
