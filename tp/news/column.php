<?php
/**
 * Column News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (have_rows('column_item')): ?>
<section class="ane-news-column ane-blog-section">
    <div class="ane-container">
        <div class="ane-col-2">
            <?php while (have_rows('column_item')): the_row(); ?>
                <?php
                $ColumnCat   = get_sub_field('pilih_kategori');
                $ColumnTitle = get_sub_field('column_title');

                $category_id   = $ColumnCat->term_id ?? 0;
                $category_name = esc_html($ColumnCat->name ?? '');
                $category_url  = $category_id ? esc_url(get_category_link($category_id)) : '#';

                // Query semua post (5 post)
                $query = new WP_Query([
                    'post_type'      => 'post',
                    'cat'            => $category_id,
                    'posts_per_page' => 5,
                    'no_found_rows'  => true, // Optimasi query
                ]);
                ?>

                <div class="ane-item">
                    <!-- Judul Kategori -->
                    <div class="section-title">
                        <div class="section-title-item">
                            <a href="<?php echo $category_url; ?>">
                                <h2><?php echo !empty($ColumnTitle) ? wp_kses_post($ColumnTitle) : $category_name; ?></h2>
                            </a>
                        </div>
                        <a class="lainnya" href="<?php echo $category_url; ?>">
                            <?php esc_html_e('View All', 'makhadane'); ?> <i class="ane-chevron-right-alt-2"></i>
                        </a>
                    </div>

                    <?php if ($query->have_posts()) : ?>
                        <?php $counter = 0; ?>
                        <?php while ($query->have_posts()) : $query->the_post(); ?>
                            <?php
                            if ($counter === 0) {
                                get_template_part('tp/content', 'overlay'); // Post pertama sebagai overlay
                            } else {
                                get_template_part('tp/content', 'list'); // Post berikutnya sebagai list
                            }
                            $counter++;
                            ?>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>
