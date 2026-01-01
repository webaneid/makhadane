<?php
/**
 * Classic News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$homekategori  = get_sub_field('pilih_kategori');
$judul         = get_sub_field('custom_title');
$kategori_id   = $homekategori->term_id ?? 0;
$kategori_nama = esc_html($homekategori->name ?? '');
$kategori_url  = $kategori_id ? esc_url(get_category_link($kategori_id)) : '#';

// Ambil 5 post sekaligus (1 utama + 4 berikutnya)
$query = new WP_Query([
    'post_type'      => 'post',
    'cat'            => $kategori_id,
    'posts_per_page' => 5,
    'no_found_rows'  => true, // Optimasi query
]);
?>
<section class="ane-news-classic ane-blog-section">
    <div class="ane-col-column">
        <!-- Judul Kategori -->
        <div class="section-title">
            <div class="section-title-item">
                <a href="<?php echo $kategori_url; ?>" <?php echo ($kategori_url !== '#') ? 'rel="noopener noreferrer"' : ''; ?>>
                    <h2><?php echo esc_html($judul ?: $kategori_nama); ?></h2>
                </a>
            </div>
            <a class="lainnya" href="<?php echo $kategori_url; ?>" aria-label="<?php esc_attr_e('View All Posts in ', 'makhadane'); echo esc_attr($kategori_nama); ?>">
                <?php esc_html_e('View All', 'makhadane'); ?> <i class="ane-chevron-right-alt-2"></i>
            </a>
        </div>

        <?php if ($query->have_posts()) : ?>
        <div class="ane-col-column">
            <?php $counter = 0; ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php if ($counter === 0) : ?>
                    <div class="ane-col-atas">
                        <?php get_template_part('tp/content', 'klasik'); ?>
                    </div>
                <?php else : ?>
                    <?php if ($counter === 1) echo '<div class="ane-col-row">'; ?>
                        <?php get_template_part('tp/content', 'klasik'); ?>
                    <?php if ($counter === 4) echo '</div>'; ?>
                <?php endif; ?>
                <?php $counter++; ?>
            <?php endwhile; ?>
            <?php if ($counter > 1) echo '</div>'; ?>
        </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
</section>
