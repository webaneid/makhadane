<?php
/**
 * Pelajaran Taxonomy Archive Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bgimg = get_field( 'ane_image_arsip_ustadz', 'option' );
$bgimg = $bgimg ? ( $bgimg['sizes']['large'] ?: ane_dummy_thumbnail() ) : ane_dummy_thumbnail();
?>

<main id="site-content" class="ane-arsip mb-40 ane-arsip-ustadz">
	<header class="archive-header" style="background: url('<?php echo esc_url( $bgimg ); ?>')">
		<?php single_term_title( '<h1 class="general-title">', '</h1>' ); ?>
	</header>

	<?php ane_display_breadcrumbs(); ?>

	<div class="ane-container">
		<div class="entry-content">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'tp/content', 'ustadz' );
				endwhile;
			else :
				get_template_part( 'tp/content', 'none' );
			endif;

			echo ane_post_pagination();
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
