<?php
/**
 * Admin pages & styling for Elemen Ane options.
 *
 * @package makhadane
 */

/**
 * Return all Ane admin sections.
 */
function ane_get_admin_sections() : array {
	$sections = array(
		'ane-setup'          => array(
			'title'      => __( 'Makhad Ane', 'makhadane' ),
			'menu_title' => __( 'Makhad Ane', 'makhadane' ),
			'badge'      => __( 'Control Center', 'makhadane' ),
			'tagline'    => __( 'Main panel to configure identity, colors, and Makhad Ane theme utilities.', 'makhadane' ),
			'location'   => __( 'Use this page as a summary and shortcut to ACF sub-menus.', 'makhadane' ),
			'cards'      => array(
				array(
					'label'       => __( 'Workflow', 'makhadane' ),
					'title'       => __( 'Customer Care', 'makhadane' ),
					'description' => __( 'Manage editorial contact data, CS, WhatsApp, and help CTA.', 'makhadane' ),
					'link'        => admin_url( 'admin.php?page=ane-customer-care' ),
					'link_label'  => __( 'Open Customer Care', 'makhadane' ),
				),
				array(
					'label'       => __( 'SEO & News', 'makhadane' ),
					'title'       => __( 'SEO & News Setup', 'makhadane' ),
					'description' => __( 'Google News sitemap, AI crawler optimization, and news website SEO guide.', 'makhadane' ),
					'link'        => admin_url( 'admin.php?page=ane-seo-news' ),
					'link_label'  => __( 'Open SEO & News', 'makhadane' ),
				),
				array(
					'label'       => __( 'Theme Updates', 'makhadane' ),
					'title'       => __( 'Check for Updates', 'makhadane' ),
					'description' => __( 'Check GitHub for latest Makhad Ane theme version and update automatically.', 'makhadane' ),
					'link'        => add_query_arg( 'ane_force_check', '1', admin_url( 'themes.php' ) ),
					'link_label'  => __( 'Check Updates Now', 'makhadane' ),
				),
				array(
					'label'        => __( 'Color and Scripts', 'makhadane' ),
					'title'        => __( 'Select colors and scripts', 'makhadane' ),
					'description'  => __( 'Select and manage theme colors and custom scripts for Google Analytics and other tools.', 'makhadane' ),
					'link'       => admin_url( 'admin.php?page=ane-general-setting' ),
					'link_label' => __( 'General Setting', 'makhadane' ),
				),
			),
		),
		'ane-seo-news'       => array(
			'title'      => __( 'SEO & News Setup', 'makhadane' ),
			'menu_title' => __( 'SEO & News', 'makhadane' ),
			'badge'      => __( 'Google News Ready', 'makhadane' ),
			'tagline'    => __( 'Complete guide for Google News submission, AI crawler optimization, and news website SEO.', 'makhadane' ),
			'location'   => __( 'Enhance Yoast SEO Free with NewsArticle schema, Google News sitemap, and AI-friendly metadata.', 'makhadane' ),
		),
		'ane-general-setting' => array(
			'title'      => __( 'General Setting', 'makhadane' ),
			'menu_title' => __( 'General Setting', 'makhadane' ),
			'badge'      => __( 'Brand Identity', 'makhadane' ),
			'tagline'    => __( 'Configure brand identity, logo, tagline, and fallback content for hero blocks.', 'makhadane' ),
		),
		'ane-customer-care'    => array(
			'title'      => __( 'Customer Care', 'makhadane' ),
			'menu_title' => __( 'Customer Care', 'makhadane' ),
			'badge'      => __( 'Support Channel', 'makhadane' ),
			'tagline'    => __( 'All communication channels: editorial email, hotline, WhatsApp, and operating hours.', 'makhadane' ),
		),
	);

	return apply_filters( 'ane/admin/sections', $sections );
}

/**
 * Register options pages via ACF.
 */
function ane_register_acf_options_pages() : void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	$sections = ane_get_admin_sections();

	acf_add_options_page(
		array(
			'page_title' => $sections['ane-setup']['title'],
			'menu_title' => $sections['ane-setup']['menu_title'],
			'menu_slug'  => 'ane-setup',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-customizer',
			'position'   => 59,
			'redirect'   => false,
		)
	);

	$subpages = array(
		'ane-general-setting',
		'ane-customer-care',
	);

	foreach ( $subpages as $slug ) {
		if ( empty( $sections[ $slug ] ) ) {
			continue;
		}

		acf_add_options_sub_page(
			array(
				'page_title'  => $sections[ $slug ]['title'],
				'menu_title'  => $sections[ $slug ]['menu_title'],
				'menu_slug'   => $slug,
				'parent_slug' => 'ane-setup',
				'capability'  => 'manage_options',
			)
		);
	}
}
add_action( 'acf/init', 'ane_register_acf_options_pages' );

/**
 * Register SEO & News submenu separately (uses custom render, not ACF).
 * Uses admin_menu with late priority to ensure parent exists.
 */
function ane_register_seo_news_page() {
	$sections = ane_get_admin_sections();

	if ( ! empty( $sections['ane-seo-news'] ) ) {
		add_submenu_page(
			'ane-setup',
			$sections['ane-seo-news']['title'],
			$sections['ane-seo-news']['menu_title'],
			'manage_options',
			'ane-seo-news',
			'ane_render_seo_news_page'
		);
	}
}
add_action( 'admin_menu', 'ane_register_seo_news_page', 999 );

/**
 * Register fallback menu when ACF Options is not available.
 */
function ane_register_admin_menu_fallback() : void {
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	add_menu_page(
		__( 'Makhad Ane', 'makhadane' ),
		__( 'Makhad Ane', 'makhadane' ),
		'manage_options',
		'ane-setup',
		'ane_render_acf_missing_notice',
		'dashicons-admin-customizer',
		59
	);

	$sections = ane_get_admin_sections();
	// Note: ane-seo-news is registered separately via ane_register_seo_news_page().
	$slugs    = array( 'ane-general-setting', 'ane-customer-care' );

	foreach ( $slugs as $slug ) {
		if ( empty( $sections[ $slug ] ) ) {
			continue;
		}

		add_submenu_page(
			'ane-setup',
			$sections[ $slug ]['title'],
			$sections[ $slug ]['menu_title'],
			'manage_options',
			$slug,
			'ane_render_acf_missing_notice'
		);
	}
}
add_action( 'admin_menu', 'ane_register_admin_menu_fallback' );

/**
 * Render fallback notice.
 */
function ane_render_acf_missing_notice() : void {
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Makhad Ane', 'makhadane' ) . '</h1>';
	echo '<p>' . esc_html__( 'Activate Advanced Custom Fields Pro to start using this options page.', 'makhadane' ) . '</p>';
	echo '</div>';
}

/**
 * Determine current Ane admin slug.
 */
function ane_get_current_admin_page_slug() : ?string {
	if ( empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return null;
	}

	$slug     = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sections = ane_get_admin_sections();

	return isset( $sections[ $slug ] ) ? $slug : null;
}

/**
 * Enqueue admin styles globally for all admin pages.
 *
 * This ensures consistent styling across dashboard, settings, and custom admin pages.
 */
function ane_enqueue_global_admin_styles() : void {
	wp_enqueue_style(
		'ane-admin-global',
		get_template_directory_uri() . '/css/admin.min.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'admin_enqueue_scripts', 'ane_enqueue_global_admin_styles' );

/**
 * Register custom meta boxes for Ane admin pages.
 */
function ane_register_admin_meta_boxes() : void {
	$sections = ane_get_admin_sections();

	foreach ( array_keys( $sections ) as $slug ) {
		$hook = ( 'ane-setup' === $slug ) ? 'toplevel_page_ane-setup' : 'ane_page_' . $slug;
		add_action( 'load-' . $hook, 'ane_prepare_admin_metaboxes' );
	}
}
add_action( 'admin_menu', 'ane_register_admin_meta_boxes', 20 );

/**
 * Prepare metaboxes for the current screen.
 */
function ane_prepare_admin_metaboxes() : void {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$slug = ane_get_current_admin_page_slug();

	/**
	 * Allow developers to register additional metaboxes.
	 *
	 * @param WP_Screen $screen Screen object.
	 * @param string    $slug   Current Ane admin slug.
	 */
	do_action( 'ane/options_page/register_metaboxes', $screen, $slug );
}

/**
 * Render hero + cards before ACF form.
 */
function ane_render_admin_intro() : void {
	$slug = ane_get_current_admin_page_slug();

	if ( ! $slug ) {
		return;
	}

	$sections = ane_get_admin_sections();
	$data     = $sections[ $slug ] ?? null;

	if ( ! $data ) {
		return;
	}

	echo '<div class="ane-admin wrap" id="ane-admin-' . esc_attr( $slug ) . '">';
	echo '<div class="ane-admin__hero">';
	echo '<div class="ane-admin__hero-content">';
	if ( ! empty( $data['badge'] ) ) {
		echo '<span class="ane-admin__badge">' . esc_html( $data['badge'] ) . '</span>';
	}
	echo '<h1>' . esc_html( $data['title'] ) . '</h1>';
	if ( ! empty( $data['tagline'] ) ) {
		echo '<p>' . esc_html( $data['tagline'] ) . '</p>';
	}
	echo '</div>';
	echo '</div>';

	if ( ! empty( $data['cards'] ) && is_array( $data['cards'] ) ) {
		echo '<div class="ane-admin__cards">';
		foreach ( $data['cards'] as $card ) {
			echo '<div class="ane-admin__card">';
			if ( ! empty( $card['label'] ) ) {
				echo '<span class="ane-admin__card-label">' . esc_html( $card['label'] ) . '</span>';
			}
			if ( ! empty( $card['title'] ) ) {
				echo '<h3>' . esc_html( $card['title'] ) . '</h3>';
			}
			if ( ! empty( $card['description'] ) ) {
				echo '<p>' . esc_html( $card['description'] ) . '</p>';
			}
			if ( ! empty( $card['items'] ) && is_array( $card['items'] ) ) {
				echo '<ul>';
				foreach ( $card['items'] as $item ) {
					echo '<li>' . esc_html( $item ) . '</li>';
				}
				echo '</ul>';
			}
			if ( ! empty( $card['link'] ) || ! empty( $card['link_2'] ) ) {
				echo '<div class="ane-admin__card-buttons">';

				if ( ! empty( $card['link'] ) ) {
					echo '<a class="ane-admin__cta" href="' . esc_url( $card['link'] ) . '">';
					echo '<span class="ane-admin__cta-text">' . esc_html( $card['link_label'] ?? __( 'Open page', 'makhadane' ) ) . '</span>';
					echo '<span class="ane-admin__cta-mobile">Open</span>';
					echo '<span class="ane-admin__cta-arrow" aria-hidden="true">→</span>';
					echo '</a>';
				}

				if ( ! empty( $card['link_2'] ) ) {
					echo '<a class="ane-admin__cta ane-admin__cta--secondary" href="' . esc_url( $card['link_2'] ) . '">';
					echo '<span class="ane-admin__cta-text">' . esc_html( $card['link_2_label'] ?? __( 'Open page', 'makhadane' ) ) . '</span>';
					echo '<span class="ane-admin__cta-mobile">Open</span>';
					echo '<span class="ane-admin__cta-arrow" aria-hidden="true">→</span>';
					echo '</a>';
				}

				echo '</div>';
			}
			echo '</div>';
		}
		echo '</div>';
	}

	// Render meta boxes registered via 'ane/options_page/register_metaboxes' filter.
	$screen = get_current_screen();
	if ( $screen ) {
		ob_start();
		echo '<div class="ane-admin__metaboxes">';
		do_meta_boxes( $screen, 'ane-admin', null );
		echo '</div>';
		$metabox_markup = trim( ob_get_clean() );
		if ( $metabox_markup ) {
			echo $metabox_markup;
		}
	}

	do_action( 'ane/options_page/after_intro', $slug );
	echo '</div>';
}
add_action( 'admin_notices', 'ane_render_admin_intro' );

/**
 * Get Google News Sitemap URL.
 *
 * @return string News sitemap URL.
 */
function ane_get_news_sitemap_url() : string {
	// Check if Yoast SEO is active and has news sitemap
	if ( defined( 'WPSEO_VERSION' ) && class_exists( 'WPSEO_News_Sitemap' ) ) {
		return home_url( '/news-sitemap.xml' );
	}

	// Fallback to standard WordPress sitemap for posts
	return home_url( '/wp-sitemap-posts-post-1.xml' );
}

/**
 * Render SEO & News Setup page content.
 */
function ane_render_seo_news_page() {

	$news_sitemap_url = ane_get_news_sitemap_url();
	$home_url         = home_url();
	$rss_feed_url     = get_feed_link();

	?>
	<style>
		.ane-seo-panel {
			background: white;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 20px;
			margin: 20px 0;
			box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		}
		.ane-seo-panel h3 {
			margin-top: 0;
			border-bottom: 2px solid #2271b1;
			padding-bottom: 10px;
			color: #1d2327;
		}
		.ane-seo-checklist {
			list-style: none;
			padding-left: 0;
		}
		.ane-seo-checklist li {
			padding: 8px 0;
			padding-left: 30px;
			position: relative;
		}
		.ane-seo-checklist li:before {
			content: '✓';
			position: absolute;
			left: 0;
			color: #46b450;
			font-weight: bold;
			font-size: 18px;
		}
		.ane-seo-url-box {
			background: #f0f0f1;
			border: 1px solid #c3c4c7;
			border-radius: 4px;
			padding: 12px;
			font-family: monospace;
			font-size: 14px;
			word-break: break-all;
			margin: 10px 0;
		}
		.ane-seo-url-box code {
			color: #2271b1;
			font-weight: 600;
		}
		.ane-seo-warning {
			background: #fcf9e8;
			border-left: 4px solid #dba617;
			padding: 12px;
			margin: 15px 0;
		}
		.ane-seo-success {
			background: #e7f7e7;
			border-left: 4px solid #46b450;
			padding: 12px;
			margin: 15px 0;
		}
		.ane-seo-steps {
			counter-reset: step-counter;
			list-style: none;
			padding-left: 0;
		}
		.ane-seo-steps li {
			counter-increment: step-counter;
			padding: 15px 0;
			padding-left: 45px;
			position: relative;
			border-bottom: 1px solid #f0f0f1;
		}
		.ane-seo-steps li:before {
			content: counter(step-counter);
			position: absolute;
			left: 0;
			top: 15px;
			width: 30px;
			height: 30px;
			background: #2271b1;
			color: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
		}
		.ane-seo-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 20px;
			margin: 20px 0;
		}
	</style>

	<div class="wrap">
		<!-- Google News Sitemap -->
		<div class="ane-seo-panel">
			<h3>📰 Google News Sitemap</h3>
			<p><?php esc_html_e( 'Your Google News sitemap is general and includes posts from the last 2 days.', 'makhadane' ); ?></p>

			<div class="ane-seo-url-box">
				<strong><?php esc_html_e( 'News Sitemap URL:', 'makhadane' ); ?></strong><br>
				<code><?php echo esc_html( $news_sitemap_url ); ?></code>
			</div>

			<p>
				<a href="<?php echo esc_url( $news_sitemap_url ); ?>" class="button button-primary" target="_blank">
					<?php esc_html_e( 'View News Sitemap', 'makhadane' ); ?>
				</a>
			</p>

			<div class="ane-seo-warning">
				<strong><?php esc_html_e( '⚠️ Important:', 'makhadane' ); ?></strong>
				<?php esc_html_e( 'Add this URL to Google Search Console under Sitemaps section.', 'makhadane' ); ?>
			</div>
		</div>

		<!-- Google News Publisher Center -->
		<div class="ane-seo-panel">
			<h3>🚀 Google News Publisher Center Submission</h3>
			<p><?php esc_html_e( 'Follow these steps to submit your news website to Google News:', 'makhadane' ); ?></p>

			<ol class="ane-seo-steps">
				<li>
					<strong><?php esc_html_e( 'Go to Google News Publisher Center', 'makhadane' ); ?></strong><br>
					<a href="https://publishercenter.google.com/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Publisher Center', 'makhadane' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Add Your Publication', 'makhadane' ); ?></strong><br>
					<?php esc_html_e( 'Click "Add publication" and enter your website URL.', 'makhadane' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Verify Ownership', 'makhadane' ); ?></strong><br>
					<?php esc_html_e( 'Verify via Google Search Console (recommended) or HTML tag.', 'makhadane' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Add News Sitemap', 'makhadane' ); ?></strong><br>
					<?php esc_html_e( 'In Google Search Console, go to Sitemaps and add:', 'makhadane' ); ?><br>
					<code><?php echo esc_html( $news_sitemap_url ); ?></code>
				</li>
				<li>
					<strong><?php esc_html_e( 'Complete Publication Details', 'makhadane' ); ?></strong><br>
					<?php esc_html_e( 'Fill in publication name, logo, contact info, and editorial team.', 'makhadane' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Submit for Review', 'makhadane' ); ?></strong><br>
					<?php esc_html_e( 'Google will review your application (typically 1-2 weeks).', 'makhadane' ); ?>
				</li>
			</ol>

			<div class="ane-seo-success">
				<strong><?php esc_html_e( '✅ Requirements Met:', 'makhadane' ); ?></strong><br>
				<?php esc_html_e( 'Your theme includes NewsArticle schema, proper meta tags, and author attribution.', 'makhadane' ); ?>
			</div>
		</div>

		<!-- SEO Features Enabled -->
		<div class="ane-seo-panel">
			<h3>🎯 SEO Features Enabled</h3>
			<p><?php esc_html_e( 'Makhad Ane theme automatically includes these SEO enhancements:', 'makhadane' ); ?></p>

			<div class="ane-seo-grid">
				<div>
					<h4><?php esc_html_e( 'Schema.org Markup', 'makhadane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'NewsArticle schema', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Breadcrumb schema', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Publisher & Author schema', 'makhadane' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'AI-Friendly Metadata', 'makhadane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'Dublin Core metadata', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Citation metadata', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Enhanced RSS feed', 'makhadane' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'Open Graph & Twitter', 'makhadane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'Facebook Open Graph', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Twitter Card tags', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Social sharing optimized', 'makhadane' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'News Optimization', 'makhadane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'Google News sitemap', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Freshness signals', 'makhadane' ); ?></li>
						<li><?php esc_html_e( 'Robots meta enhanced', 'makhadane' ); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<!-- AI Crawler Optimization -->
		<div class="ane-seo-panel">
			<h3>🤖 AI Crawler Optimization</h3>
			<p><?php esc_html_e( 'Your content is optimized for AI models like ChatGPT, Claude, and Perplexity:', 'makhadane' ); ?></p>

			<ul class="ane-seo-checklist">
				<li><?php esc_html_e( 'Dublin Core metadata for academic/news citations', 'makhadane' ); ?></li>
				<li><?php esc_html_e( 'Citation metadata for proper attribution', 'makhadane' ); ?></li>
				<li><?php esc_html_e( 'Structured NewsArticle schema', 'makhadane' ); ?></li>
				<li><?php esc_html_e( 'Enhanced RSS feed with full content', 'makhadane' ); ?></li>
				<li><?php esc_html_e( 'Clear author attribution and bylines', 'makhadane' ); ?></li>
				<li><?php esc_html_e( 'Semantic HTML5 markup', 'makhadane' ); ?></li>
			</ul>

			<div class="ane-seo-url-box">
				<strong><?php esc_html_e( 'RSS Feed URL:', 'makhadane' ); ?></strong><br>
				<code><?php echo esc_html( $rss_feed_url ); ?></code>
			</div>
		</div>

		<!-- Testing & Validation -->
		<div class="ane-seo-panel">
			<h3>🧪 Testing & Validation Tools</h3>
			<p><?php esc_html_e( 'Use these tools to validate your SEO implementation:', 'makhadane' ); ?></p>

			<div class="ane-seo-grid">
				<div>
					<h4><?php esc_html_e( 'Facebook Debugger', 'makhadane' ); ?></h4>
					<p><?php esc_html_e( 'Test Open Graph tags', 'makhadane' ); ?></p>
					<a href="https://developers.facebook.com/tools/debug/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'makhadane' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Twitter Card Validator', 'makhadane' ); ?></h4>
					<p><?php esc_html_e( 'Test Twitter Card meta', 'makhadane' ); ?></p>
					<a href="https://cards-dev.twitter.com/validator" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'makhadane' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Schema Markup Validator', 'makhadane' ); ?></h4>
					<p><?php esc_html_e( 'Test structured data', 'makhadane' ); ?></p>
					<a href="https://validator.schema.org/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'makhadane' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Google Rich Results Test', 'makhadane' ); ?></h4>
					<p><?php esc_html_e( 'Test rich snippets', 'makhadane' ); ?></p>
					<a href="https://search.google.com/test/rich-results" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'makhadane' ); ?>
					</a>
				</div>
			</div>
		</div>

		<!-- Additional Resources -->
		<div class="ane-seo-panel">
			<h3>📚 Additional Resources</h3>
			<ul>
				<li>
					<strong><?php esc_html_e( 'Google News Guidelines:', 'makhadane' ); ?></strong>
					<a href="https://support.google.com/news/publisher-center/answer/9606710" target="_blank">
						<?php esc_html_e( 'View Guidelines', 'makhadane' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Google Search Console:', 'makhadane' ); ?></strong>
					<a href="https://search.google.com/search-console" target="_blank">
						<?php esc_html_e( 'Open Console', 'makhadane' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Schema.org Documentation:', 'makhadane' ); ?></strong>
					<a href="https://schema.org/NewsArticle" target="_blank">
						<?php esc_html_e( 'NewsArticle Docs', 'makhadane' ); ?>
					</a>
				</li>
			</ul>
		</div>

		<!-- SEO Action Plan for Google Enhanced Results -->
		<div class="ane-seo-panel">
			<h3>🎯 SEO Action Plan - Optimasi untuk Hasil Pencarian Google yang Lebih Baik</h3>

			<div class="ane-seo-success">
				<strong>✅ Fitur SEO Premium Sudah Aktif!</strong>
				<p>Theme Anda sudah dilengkapi dengan structured data lengkap untuk meningkatkan visibilitas di Google.</p>
			</div>

			<h4>📊 Apa yang Sudah Otomatis Berjalan:</h4>
			<ul class="ane-seo-checklist">
				<li><strong>NewsArticle Schema</strong> - Setiap artikel memiliki rich snippets untuk Google News</li>
				<li><strong>Breadcrumb Schema</strong> - Google memahami struktur hierarki website Anda</li>
				<li><strong>WebSite Schema + SearchAction</strong> - Search box bisa muncul di hasil Google</li>
				<li><strong>Navigation Schema</strong> - Menu utama ditandai untuk Google Sitelinks</li>
				<li><strong>Dublin Core & Citation</strong> - Optimasi untuk AI crawlers (ChatGPT, Claude, Perplexity)</li>
				<li><strong>Open Graph & Twitter Cards</strong> - Preview cantik saat share di sosial media</li>
			</ul>

			<h4 style="margin-top: 30px;">📝 Action Plan Anda (Checklist 30 Hari):</h4>

			<ol class="ane-seo-steps">
				<li>
					<strong>Hari 1-3: Setup Google Search Console</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Daftar website di <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
						<li>Verifikasi kepemilikan website (gunakan meta tag atau DNS)</li>
						<li>Submit sitemap.xml: <code><?php echo esc_url( $news_sitemap_url ); ?></code></li>
						<li>Request indexing untuk 5-10 halaman penting</li>
					</ul>
				</li>

				<li>
					<strong>Hari 4-7: Validasi Structured Data</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Buka <a href="https://search.google.com/test/rich-results" target="_blank">Rich Results Test</a></li>
						<li>Test homepage Anda - pastikan WebSite schema terdeteksi</li>
						<li>Test 2-3 artikel - pastikan NewsArticle schema terdeteksi</li>
						<li>Test halaman category - pastikan CollectionPage schema terdeteksi</li>
						<li>Screenshot hasil test untuk dokumentasi</li>
					</ul>
				</li>

				<li>
					<strong>Hari 8-14: Optimasi Struktur Website</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Buat 5-8 kategori utama yang fokus (jangan terlalu banyak)</li>
						<li>Setiap kategori minimal punya 10-15 artikel berkualitas</li>
						<li>Setup menu utama (<strong>Appearance → Menus</strong>) dengan kategori penting</li>
						<li>Pastikan menu di-assign ke location <strong>"menuutama"</strong></li>
						<li>Buat halaman penting: About, Contact, Privacy Policy</li>
					</ul>
				</li>

				<li>
					<strong>Hari 15-21: Internal Linking Strategy</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Di setiap artikel baru, link ke 2-3 artikel terkait yang sudah ada</li>
						<li>Update artikel lama: tambahkan link ke artikel baru yang relevan</li>
						<li>Link dari homepage ke kategori/halaman penting</li>
						<li>Gunakan anchor text yang natural dan deskriptif</li>
						<li>Hindari link berlebihan (3-5 internal links per artikel sudah cukup)</li>
					</ul>
				</li>

				<li>
					<strong>Hari 22-28: Content Quality & Consistency</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Publish artikel minimal 2-3x per minggu (consistency is key!)</li>
						<li>Setiap artikel minimal 300-500 kata (lebih panjang lebih baik)</li>
						<li>Gunakan heading (H2, H3) untuk struktur artikel</li>
						<li>Selalu upload featured image berkualitas (min 1200x675px)</li>
						<li>Tulis excerpt/ringkasan untuk setiap artikel</li>
						<li>Pilih 1 kategori utama per artikel (jangan multi-kategori)</li>
					</ul>
				</li>

				<li>
					<strong>Hari 29-30: Monitor & Optimize</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Cek Google Search Console → Performance → lihat impressions & clicks</li>
						<li>Identifikasi artikel dengan impressions tinggi tapi clicks rendah</li>
						<li>Improve title & meta description artikel tersebut (make it clickable!)</li>
						<li>Cek Coverage → fix halaman yang error atau excluded</li>
						<li>Monitor Core Web Vitals → pastikan website loading cepat</li>
					</ul>
				</li>
			</ol>

			<h4 style="margin-top: 30px;">🎁 Bonus Tips - Mempercepat Google Sitelinks:</h4>
			<div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 15px 0;">
				<p><strong>Google Sitelinks</strong> adalah sub-link yang muncul di bawah hasil pencarian website Anda (seperti menu mini). Sitelinks muncul otomatis ketika:</p>
				<ul style="margin-left: 20px;">
					<li>Website punya traffic organik yang stabil</li>
					<li>Struktur navigasi jelas dan konsisten</li>
					<li>User sering search brand name Anda di Google</li>
					<li>Website punya authority (backlinks, umur domain, trust)</li>
				</ul>
				<p><strong>Timeline realistis:</strong> 2-6 bulan setelah optimasi di atas (tergantung kompetisi niche Anda)</p>
				<p><strong>Cara mempercepat:</strong></p>
				<ul style="margin-left: 20px;">
					<li>Brand building (social media, PR, backlinks berkualitas)</li>
					<li>Konsisten publish konten berkualitas</li>
					<li>Encourage user search brand name Anda (bukan generic keywords)</li>
					<li>Pastikan bounce rate rendah (konten engaging, loading cepat)</li>
				</ul>
			</div>

			<h4 style="margin-top: 30px;">🔍 Tools untuk Monitor SEO:</h4>
			<div class="ane-seo-grid">
				<div style="background: white; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
					<strong>📊 Google Search Console</strong>
					<p>Monitor traffic, indexing, dan performance</p>
					<a href="https://search.google.com/search-console" target="_blank" class="button button-secondary">Open Console →</a>
				</div>
				<div style="background: white; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
					<strong>✅ Rich Results Test</strong>
					<p>Validasi structured data Anda</p>
					<a href="https://search.google.com/test/rich-results" target="_blank" class="button button-secondary">Test Now →</a>
				</div>
				<div style="background: white; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
					<strong>⚡ PageSpeed Insights</strong>
					<p>Cek kecepatan loading website</p>
					<a href="https://pagespeed.web.dev/" target="_blank" class="button button-secondary">Check Speed →</a>
				</div>
			</div>

			<div class="ane-seo-warning" style="margin-top: 20px;">
				<strong>⚠️ Penting untuk Diingat:</strong>
				<ul style="margin: 10px 0 0 20px;">
					<li>SEO adalah marathon, bukan sprint (butuh waktu 2-6 bulan untuk hasil signifikan)</li>
					<li>Google Sitelinks <strong>TIDAK bisa dipaksa</strong> - Google yang memutuskan berdasarkan site authority</li>
					<li>Focus on quality content & user experience - ranking akan follow naturally</li>
					<li>Jangan gunakan black-hat SEO (keyword stuffing, paid links, cloaking) - bisa kena penalty!</li>
					<li>Monitor terus Google Search Console untuk early detection masalah indexing</li>
				</ul>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Inject admin bar logo dynamically for mobile.
 *
 * Uses WordPress custom logo if set, otherwise fallback to theme logo.
 * Injects inline CSS to replace hardcoded "makhadane" text with logo image.
 *
 * @since 1.0.0
 */
function ane_admin_bar_logo() : void {
	// Get custom logo ID from theme customizer.
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		// Use WordPress custom logo if set.
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	} else {
		// Fallback to theme logo.
		$logo_url = get_template_directory_uri() . '/img/logo-makhadane.svg';
	}

	?>
	<style>
		@media screen and (max-width: 782px) {
			#wpadminbar #wp-admin-bar-root-default::after {
				background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
			}
		}
	</style>
	<?php
}
add_action( 'admin_head', 'ane_admin_bar_logo' );
add_action( 'wp_head', 'ane_admin_bar_logo' );
