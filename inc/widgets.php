<?php
/**
 * Posts Widget (Ane_Posts_Widget)
 *
 * Mendukung 3 pilihan desain tampilan:
 * - `list` (default): Classic Compact List (`tp/content-list.php`).
 * - `numbered`: Classic Lead + Numbered List (1 `tp/content-klasik.php` + `<ol>` `tp/content-list-title.php`).
 * - `featured_list`: Classic Lead + Compact List (1 `tp/content-klasik.php` + `tp/content-list.php`).
 *
 * @package Makhadane
 * @since 4.1.1
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.DB.SlowDBQuery, Universal.Files.SeparateFunctionsFromOO.Mixed

/**
 * Register posts widget.
 *
 * @since 4.1.1
 * @return void
 */
function ane_posts_widget_init() {
	register_widget( 'Ane_Posts_Widget' );
}
add_action( 'widgets_init', 'ane_posts_widget_init' );

/**
 * Custom posts widget class.
 *
 * Display posts by various criteria with 3 customizable design options.
 *
 * @since 4.1.1
 */
class Ane_Posts_Widget extends WP_Widget {

	/**
	 * Whitelist of allowed query types.
	 *
	 * @var array<int, string>
	 */
	private const ALLOWED_TYPES = array(
		'Recent Post',
		'Popular Post',
		'Most Comments',
		'Post Format: Gallery',
		'Post Format: Video',
	);

	/**
	 * Whitelist of allowed orderby columns.
	 *
	 * @var array<int, string>
	 */
	private const ALLOWED_ORDERBYS = array(
		'ID',
		'title',
		'date',
		'rand',
	);

	/**
	 * Constructor.
	 *
	 * @since 4.1.1
	 */
	public function __construct() {
		parent::__construct(
			'ane-post-widget',
			__( 'Makhadane: Posts', 'makhadane' ),
			array(
				'description' => __( 'Menampilkan daftar post berdasarkan post terbaru, kategori, tag, populer, atau komentar terbanyak dengan 3 pilihan desain.', 'makhadane' ),
			)
		);
	}

	/**
	 * Get available widget design choices.
	 *
	 * @return array<string, string>
	 */
	private function get_design_choices() {
		return array(
			'list'          => __( 'Desain 1: Compact List (Default)', 'makhadane' ),
			'numbered'      => __( 'Desain 2: Classic Lead + Numbered List', 'makhadane' ),
			'featured_list' => __( 'Desain 3: Classic Lead + Compact List', 'makhadane' ),
		);
	}

	/**
	 * Default values for widget instance.
	 *
	 * @return array<string, mixed>
	 */
	private function get_default_instance() {
		return array(
			'title'    => '',
			'design'   => 'list',
			'type'     => 'Recent Post',
			'category' => '',
			'post_tag' => '',
			'number'   => '5',
			'offset'   => '0',
			'orderby'  => 'date',
			'order'    => 'DESC',
		);
	}

	/**
	 * Display widget output.
	 *
	 * @since 4.1.1
	 * @param array<string, mixed> $args     Display arguments.
	 * @param array<string, mixed> $instance Widget instance settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$settings      = wp_parse_args( (array) $instance, $this->get_default_instance() );
		$before_widget = isset( $args['before_widget'] ) ? (string) $args['before_widget'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? (string) $args['after_widget'] : '';
		$before_title  = isset( $args['before_title'] ) ? (string) $args['before_title'] : '<h2 class="widget-title ane-title">';
		$after_title   = isset( $args['after_title'] ) ? (string) $args['after_title'] : '</h2>';

		$title          = apply_filters( 'widget_title', empty( $settings['title'] ) ? '' : (string) $settings['title'], $instance, $this->id_base );
		$design_raw     = (string) $settings['design'];
		$design_choices = $this->get_design_choices();
		$design         = isset( $design_choices[ $design_raw ] ) ? $design_raw : 'list';

		$type_raw = (string) $settings['type'];
		$type     = in_array( $type_raw, self::ALLOWED_TYPES, true ) ? $type_raw : 'Recent Post';

		$category = sanitize_text_field( (string) $settings['category'] );
		$post_tag = sanitize_text_field( (string) $settings['post_tag'] );
		$number   = absint( $settings['number'] );
		$number   = $number > 0 ? min( 20, $number ) : 5;
		$offset   = absint( $settings['offset'] );

		$orderby_raw = (string) $settings['orderby'];
		$orderby     = in_array( $orderby_raw, self::ALLOWED_ORDERBYS, true ) ? $orderby_raw : 'date';
		$order       = 'ASC' === strtoupper( (string) $settings['order'] ) ? 'ASC' : 'DESC';

		$query_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'orderby'             => $orderby,
			'order'               => $order,
			'posts_per_page'      => $number,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( $offset > 0 ) {
			$query_args['offset'] = $offset;
		}

		$tax_query = array();

		if ( 'Most Comments' === $type ) {
			$query_args['orderby'] = 'comment_count';
			$query_args['order']   = 'DESC';
		} elseif ( 'Popular Post' === $type ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'musi_views';
		} elseif ( 'Post Format: Gallery' === $type ) {
			$tax_query[] = array(
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => array( 'post-format-gallery' ),
			);
		} elseif ( 'Post Format: Video' === $type ) {
			$tax_query[] = array(
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => array( 'post-format-video' ),
			);
		}

		if ( '' !== $post_tag ) {
			$query_args['tag'] = $post_tag;
		} elseif ( '' !== $category ) {
			$tax_query[] = array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $category,
			);
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		$r = new WP_Query( $query_args );

		if ( 'Popular Post' === $type && ! $r->have_posts() ) {
			unset( $query_args['meta_key'] );
			$query_args['orderby'] = 'comment_count';
			$r                     = new WP_Query( $query_args );
		}

		if ( 'numbered' === $design ) {
			$widget_cls = 'post-widget post-widget-numbered';
		} elseif ( 'featured_list' === $design ) {
			$widget_cls = 'post-widget post-widget-featured';
		} else {
			$widget_cls = 'post-widget post-widget-list';
		}

		echo wp_kses_post( $before_widget );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $before_title ) . esc_html( $title ) . wp_kses_post( $after_title );
		}

		echo '<div class="' . esc_attr( $widget_cls ) . '">';

		if ( $r->have_posts() ) :
			if ( 'numbered' === $design ) :
				$posts_list  = $r->posts;
				$total_posts = count( $posts_list );
				global $post;

				if ( isset( $posts_list[0] ) && $posts_list[0] instanceof WP_Post ) :
					$post = $posts_list[0]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					?>
					<div class="widget-lead-post">
						<?php get_template_part( 'tp/content', 'klasik' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $total_posts > 1 ) : ?>
					<div class="widget-numbered-list">
						<ol>
							<?php
							for ( $i = 1; $i < $total_posts; $i++ ) :
								if ( ! ( $posts_list[ $i ] instanceof WP_Post ) ) {
									continue;
								}
								$post = $posts_list[ $i ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								setup_postdata( $post );
								?>
								<li>
									<?php get_template_part( 'tp/content', 'list-title' ); ?>
								</li>
							<?php endfor; ?>
						</ol>
					</div>
				<?php endif; ?>

			<?php elseif ( 'featured_list' === $design ) : ?>
				<?php
				$posts_list  = $r->posts;
				$total_posts = count( $posts_list );
				global $post;

				if ( isset( $posts_list[0] ) && $posts_list[0] instanceof WP_Post ) :
					$post = $posts_list[0]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					?>
					<div class="widget-lead-post">
						<?php get_template_part( 'tp/content', 'klasik' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $total_posts > 1 ) : ?>
					<div class="widget-sub-list">
						<?php
						for ( $i = 1; $i < $total_posts; $i++ ) :
							if ( ! ( $posts_list[ $i ] instanceof WP_Post ) ) {
								continue;
							}
							$post = $posts_list[ $i ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							setup_postdata( $post );
							get_template_part( 'tp/content', 'list' );
						endfor;
						?>
					</div>
				<?php endif; ?>

			<?php else : ?>
				<?php
				while ( $r->have_posts() ) :
					$r->the_post();
					get_template_part( 'tp/content', 'list' );
				endwhile;
				?>
			<?php endif; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No post found', 'makhadane' ); ?></p>
			<?php
		endif;

		echo '</div>';
		echo wp_kses_post( $after_widget );

		wp_reset_postdata();
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 4.1.1
	 * @param array<string, mixed> $new_instance Values just sent to be saved.
	 * @param array<string, mixed> $old_instance Previously saved values from database.
	 * @return array<string, mixed> Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance       = is_array( $old_instance ) ? $old_instance : array();
		$design_choices = $this->get_design_choices();

		$raw_design = isset( $new_instance['design'] ) ? sanitize_key( (string) $new_instance['design'] ) : 'list';
		$raw_type   = isset( $new_instance['type'] ) ? sanitize_text_field( (string) $new_instance['type'] ) : 'Recent Post';
		$raw_order  = isset( $new_instance['orderby'] ) ? sanitize_text_field( (string) $new_instance['orderby'] ) : 'date';
		$raw_num    = isset( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;

		$instance['title']    = isset( $new_instance['title'] ) ? sanitize_text_field( (string) $new_instance['title'] ) : '';
		$instance['design']   = isset( $design_choices[ $raw_design ] ) ? $raw_design : 'list';
		$instance['type']     = in_array( $raw_type, self::ALLOWED_TYPES, true ) ? $raw_type : 'Recent Post';
		$instance['category'] = isset( $new_instance['category'] ) ? sanitize_text_field( (string) $new_instance['category'] ) : '';
		$instance['post_tag'] = isset( $new_instance['post_tag'] ) ? sanitize_text_field( (string) $new_instance['post_tag'] ) : '';
		$instance['number']   = (string) ( $raw_num > 0 ? min( 20, $raw_num ) : 5 );
		$instance['offset']   = isset( $new_instance['offset'] ) ? (string) absint( $new_instance['offset'] ) : '0';
		$instance['orderby']  = in_array( $raw_order, self::ALLOWED_ORDERBYS, true ) ? $raw_order : 'date';
		$instance['order']    = ( isset( $new_instance['order'] ) && 'ASC' === strtoupper( (string) $new_instance['order'] ) ) ? 'ASC' : 'DESC';

		return $instance;
	}

	/**
	 * Display widget settings form in wp-admin.
	 *
	 * @since 4.1.1
	 * @param array<string, mixed> $instance Current widget settings.
	 * @return string
	 */
	public function form( $instance ) {
		$design_choices = $this->get_design_choices();
		$orders         = array( 'ASC', 'DESC' );

		$settings = wp_parse_args( (array) $instance, $this->get_default_instance() );
		$title    = (string) $settings['title'];
		$design   = isset( $design_choices[ (string) $settings['design'] ] ) ? (string) $settings['design'] : 'list';
		$type     = (string) $settings['type'];
		$category = (string) $settings['category'];
		$post_tag = (string) $settings['post_tag'];
		$number   = (string) $settings['number'];
		$orderby  = (string) $settings['orderby'];
		$order    = (string) $settings['order'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget Title', 'makhadane' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'design' ) ); ?>"><?php esc_html_e( 'Pilihan Desain:', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'design' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'design' ) ); ?>">
				<?php foreach ( $design_choices as $design_key => $design_label ) : ?>
					<option value="<?php echo esc_attr( $design_key ); ?>"<?php selected( $design, $design_key ); ?>><?php echo esc_html( $design_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php esc_html_e( 'Select a type', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
				<?php foreach ( self::ALLOWED_TYPES as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>"<?php selected( $type, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<hr />
		<p id="category<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Select a category:', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<option value=""><?php esc_html_e( 'All Category', 'makhadane' ); ?></option>
				<?php
				$category_get = get_terms(
					array(
						'taxonomy'   => 'category',
						'hide_empty' => false,
					)
				);

				if ( is_array( $category_get ) && ! empty( $category_get ) ) :
					foreach ( $category_get as $option ) :
						if ( $option instanceof WP_Term ) :
							?>
							<option value="<?php echo esc_attr( $option->slug ); ?>"<?php selected( $category, $option->slug ); ?>><?php echo esc_html( $option->name ); ?></option>
							<?php
						endif;
					endforeach;
				endif;
				?>
			</select>
		</p>
		<hr />
		<p id="tag<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>"><?php esc_html_e( 'Type tag', 'makhadane' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_tag' ) ); ?>" type="text" value="<?php echo esc_attr( $post_tag ); ?>" />
			<small><?php esc_html_e( 'separate by comma', 'makhadane' ); ?></small>
		</p>
		<hr />
		<h4><?php esc_html_e( 'Display options', 'makhadane' ); ?></h4>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'makhadane' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $number ); ?>" style="width:60px; margin-left:.2em; text-align:right;" />
		</p>
		<?php
		$orderby_style = ( 'Most Comments' === $type || 'Popular Post' === $type ) ? 'display:none' : 'display:block';
		?>
		<p id="orderby<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" style="<?php echo esc_attr( $orderby_style ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order post by:', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php foreach ( self::ALLOWED_ORDERBYS as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" id="<?php echo esc_attr( $option ); ?>"<?php selected( $orderby, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p id="order<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php foreach ( $orders as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>"<?php selected( $order, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
		return '';
	}
}
