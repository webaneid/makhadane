<?php
/**
 * Home News Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ane-home-news gp">
    <div class="ane-container">
        <div class="ane-text">
            <div class="ane-isi">
                <?php $judul = get_sub_field('ane_title'); ?>
                <h2 class="judul-utama">
                    <?php echo !empty($judul) ? wp_kses_post($judul) : esc_html__('Our <span>Blogs</span>', 'makhadane'); ?>
                </h2>

                <?php
                $deskripsi = get_sub_field('ane_deskripsi');
                if (!empty($deskripsi)) {
                    echo '<p>' . esc_html($deskripsi) . '</p>';
                }
                ?>
            </div>
        </div>

        <div class="ane-col-46">
            <?php
            // Query semua post (5 post)
            $query = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 5,
                'no_found_rows'  => true, // Optimasi query
            ]);

            if ($query->have_posts()) :
                $counter = 0;
            ?>
                <div class="ane-kiri">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php if ($counter === 0) : ?>
                            <?php get_template_part('tp/content', 'overlay'); ?>
                        <?php endif; ?>
                        <?php $counter++; ?>
                    <?php endwhile; ?>
                </div>

                <div class="ane-kanan">
                    <?php $counter = 0; ?>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php if ($counter > 0) : ?>
                            <?php get_template_part('tp/content', 'overlay'); ?>
                        <?php endif; ?>
                        <?php $counter++; ?>
                    <?php endwhile; ?>
                </div>

                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
