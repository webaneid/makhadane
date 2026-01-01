<?php
/**
 * Home Gallery Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$homekategori = get_sub_field('pilih_kategori');
$judul = get_sub_field('ane_title');

// Cek apakah kategori ada sebelum mengakses propertinya
if (!$homekategori || !isset($homekategori->term_id, $homekategori->name)) {
    return; // Menghindari error jika kategori tidak ada
}

$category_id = $homekategori->term_id;
$category_name = esc_html($homekategori->name);
$category_link = esc_url(get_category_link($category_id));
?>
<section class="ane-home-gallery gp">
    <div class="ane-container">
        <div class="ane-text">
            <a href="<?php echo $category_link; ?>" rel="noopener noreferrer">
                <h2 class="judul-utama"><?php echo !empty($judul) ? wp_kses_post($judul) : $category_name; ?></h2>
            </a>

            <?php 
            $deskripsi = get_sub_field('ane_deskripsi');
            if (!empty($deskripsi)) {
                echo '<p>' . esc_html($deskripsi) . '</p>';
            }
            ?>

            <div class="lainnya">
                <a href="<?php echo $category_link; ?>" aria-label="<?php esc_attr_e('View All Posts in ', 'makhadane'); echo esc_attr($category_name); ?>">
                    <?php _e('View All', 'makhadane'); ?> <i class="ane-chevron-right-alt-2"></i>
                </a>
            </div>
        </div>
        
        <div class="owl-carousel dot-style2" id="home-sliding">
            <?php
            $query_args = [
                'post_type'      => 'post',
                'cat'            => $category_id, // Gunakan term_id langsung
                'posts_per_page' => 8,
                'no_found_rows'  => true, // Optimasi query
            ];
            $the_query = new WP_Query($query_args);

            if ($the_query->have_posts()) :
                while ($the_query->have_posts()) : $the_query->the_post();
                    get_template_part('tp/content', 'overlay');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div><!-- container end-->
</section>
