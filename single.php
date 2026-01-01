<?php
/**
 * Single Post Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
ane_set_views( get_the_ID() );
?>
<main id="site-content">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post();

		$post_id = get_the_ID();
		$thumbImg = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
		$photo = $thumbImg[0] ?? ane_dummy_thumbnail();
		$tanggal_iso = get_the_date('c', $post_id);
		$tanggal_format = get_the_date('l, j F Y', $post_id);
		$tanggal_modified = get_the_modified_date('c', $post_id);
		$author_name = get_the_author();
		$author_url = get_author_posts_url(get_the_author_meta('ID'));
		$categories = get_the_category();
		$category_names = array_map(fn($cat) => $cat->cat_name, $categories);
		$category_links = array_map(fn($cat) => '<a class="post-cat" href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->cat_name) . '</a>', $categories);
		$category_markup = implode(', ', $category_links);
		$tag_list = get_the_tag_list('<ul><li>', '</li><li>', '</li></ul>');

		// Schema.org JSON-LD for SEO
		$schema_data = [
			"@context" => "https://schema.org",
			"@type" => "Article",
			"headline" => get_the_title(),
			"image" => [
				"@type" => "ImageObject",
				"url" => esc_url($photo),
				"width" => $thumbImg[1] ?? 1200,
				"height" => $thumbImg[2] ?? 630
			],
			"datePublished" => $tanggal_iso,
			"dateModified" => $tanggal_modified,
			"author" => [
				"@type" => "Person",
				"name" => $author_name,
				"url" => $author_url
			],
			"publisher" => [
				"@type" => "Organization",
				"name" => get_bloginfo('name'),
				"logo" => [
					"@type" => "ImageObject",
					"url" => esc_url(get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : get_template_directory_uri() . '/images/logo.png')
				]
			],
			"description" => get_the_excerpt() ? wp_strip_all_tags(get_the_excerpt()) : wp_trim_words(get_the_content(), 30, '...'),
			"mainEntityOfPage" => [
				"@type" => "WebPage",
				"@id" => get_permalink()
			]
		];

		if (!empty($category_names)) {
			$schema_data["articleSection"] = $category_names;
		}
		?>

		<script type="application/ld+json">
			<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
		</script>

		<article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('ane-post-single'); ?> itemscope itemtype="https://schema.org/Article">
			<meta itemprop="mainEntityOfPage" content="<?php echo esc_url(get_permalink()); ?>">
			<meta itemprop="headline" content="<?php echo esc_attr(get_the_title()); ?>">
			<meta itemprop="author" content="<?php echo esc_attr($author_name); ?>">
			<meta itemprop="datePublished" content="<?php echo esc_attr($tanggal_iso); ?>">
			<meta itemprop="dateModified" content="<?php echo esc_attr($tanggal_modified); ?>">
			<meta itemprop="image" content="<?php echo esc_url($photo); ?>">

			<header class="entry-header">
				<div class="single-post-header" style="background-image: url('<?php echo esc_url($photo); ?>');"></div>
			</header>

			<?php ane_display_breadcrumbs(); ?>

			<div class="entry-content">
				<div class="ane-single-title">
					<div class="ane-kategori hidden-mobile"><?php echo $category_markup; ?></div>
					<?php
					ane_get_title();
					echo ane_single_meta();
					?>
				</div>

				<?php ane_get_featured_image(); ?>

				<div class="ane-content" itemprop="articleBody">

					<div class="share-this">
						<?php echo ane_social_share(); ?>
					</div>

					<?php the_content(); ?>

					<?php if ($tag_list) : ?>
						<div class="post-tags">
							<div class="post-tags-wrapper">
								<h2><?php esc_html_e('Tags', 'makhadane'); ?></h2>
								<?php echo $tag_list; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="post-nav"><?php echo ane_prev_next_post(); ?></div>

					<div class="terkait-newest">
						<div class="ane-col-55">
							<div class="ane-kiri">
								<div class="related-post"><?php echo ane_related_post(); ?></div>
							</div>
							<div class="ane-kanan">
								<div class="newest-post"><?php echo ane_newest_posts(); ?></div>
							</div>
						</div>
					</div>

					<?php
					// Facebook Comments integration
					if ( function_exists( 'ane_load_facebook_comment' ) ) {
						echo ane_load_facebook_comment();
					}
					?>
				</div>
			</div>
		</article>

	<?php endwhile; endif; ?>
</main><!-- .site-main -->
<?php get_footer();
