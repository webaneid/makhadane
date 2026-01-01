<?php
/**
 * Posts Widget
 *
 * @package Makhadane
 * @since 4.1.1
 */

/**
 * Register posts widget
 *
 * @since 4.1.1
 * @return void
 */
function ane_posts_widget_init() {
	register_widget( 'Ane_Posts_Widget' );
}
add_action( 'widgets_init', 'ane_posts_widget_init' );

/**
 * Custom posts widget class
 *
 * Display posts by various criteria with customizable options.
 *
 * @since 4.1.1
 */
class Ane_Posts_Widget extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 4.1.1
	 */
	public function __construct() {
		parent::__construct(
			'ane-post-widget',
			__( 'Makhadane: Posts', 'makhadane' ),
			array(
				'description' => __( 'Display posts by recent post, category, tagged or most comment post as widget items, order by date, id. you can customize', 'makhadane' ),
			)
		);
	}

	/**
	 * Display widget output
	 *
	 * @since 4.1.1
	 * @param array $args     Display arguments
	 * @param array $instance Widget instance settings
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$type     = isset( $instance['type'] ) ? $instance['type'] : 'Recent Post';
		$category = isset( $instance['category'] ) ? $instance['category'] : '';
		$post_tag = isset( $instance['post_tag'] ) ? $instance['post_tag'] : '';
		$number   = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$orderby  = isset( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order    = isset( $instance['order'] ) ? $instance['order'] : 'DESC';

		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$query_args = array(
			'post_status'    => 'publish',
			'orderby'        => $orderby,
			'order'          => $order,
			'posts_per_page' => $number,
			'no_found_rows'  => true,
		);

		if ( 'Most Comments' === $type ) {
			$query_args['orderby'] = 'comment_count';
			$query_args['order']   = 'DESC';
		} elseif ( 'Popular Post' === $type ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'musi_views';
		} elseif ( 'Post Format: Gallery' === $type ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => 'post-format-gallery',
				),
			);
		} elseif ( 'Post Format: Video' === $type ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => 'post-format-video',
				),
			);
		}

		if ( ! empty( $post_tag ) ) {
			$query_args['tag'] = $post_tag;
		} elseif ( ! empty( $category ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		}

		$r = new WP_Query( $query_args );

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		echo '<div class="post-widget">';

		if ( $r->have_posts() ) {
			while ( $r->have_posts() ) {
				$r->the_post();
				get_template_part( 'tp/content', 'list' );
			}
		} else {
			echo '<p>' . esc_html__( 'No post found', 'makhadane' ) . '</p>';
		}

		echo '</div>';
		echo $args['after_widget'];

		wp_reset_postdata();
	}

	/**
	 * Sanitize widget form values as they are saved
	 *
	 * @since 4.1.1
	 * @param array $new_instance Values just sent to be saved
	 * @param array $old_instance Previously saved values from database
	 * @return array Updated safe values to be saved
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']    = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['type']     = ! empty( $new_instance['type'] ) ? sanitize_text_field( $new_instance['type'] ) : 'Recent Post';
		$instance['category'] = ! empty( $new_instance['category'] ) ? sanitize_text_field( $new_instance['category'] ) : '';
		$instance['post_tag'] = ! empty( $new_instance['post_tag'] ) ? sanitize_text_field( $new_instance['post_tag'] ) : '';
		$instance['number']   = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
		$instance['offset']   = ! empty( $new_instance['offset'] ) ? absint( $new_instance['offset'] ) : 0;
		$instance['orderby']  = ! empty( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : 'date';
		$instance['order']    = ! empty( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : 'DESC';

		return $instance;
	}

	/**
	 * Back-end widget form
	 *
	 * @since 4.1.1
	 * @param array $instance Previously saved values from database
	 * @return void
	 */
	public function form( $instance ) {
		$types    = array( 'Recent Post', 'Popular Post', 'Most Comments', 'Post Format: Gallery', 'Post Format: Video' );
		$orderbys = array( 'ID', 'title', 'date', 'rand' );
		$orders   = array( 'ASC', 'DESC' );

		$defaults = array(
			'title'    => '',
			'type'     => 'Recent Post',
			'category' => '',
			'number'   => '5',
			'offset'   => '0',
			'orderby'  => 'date',
			'order'    => 'DESC',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$type     = isset( $instance['type'] ) ? $instance['type'] : 'Recent Post';
		$category = isset( $instance['category'] ) ? $instance['category'] : '';
		$post_tag = isset( $instance['post_tag'] ) ? $instance['post_tag'] : '';
		$number   = isset( $instance['number'] ) ? $instance['number'] : '5';
		$offset   = isset( $instance['offset'] ) ? $instance['offset'] : '0';
		$orderby  = isset( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order    = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget Title', 'makhadane' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php esc_html_e( 'Select a type', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
				<?php foreach ( $types as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $type, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<hr />

		<p>
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

				if ( ! empty( $category_get ) && ! is_wp_error( $category_get ) ) {
					foreach ( $category_get as $cat ) {
						printf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $cat->slug ),
							selected( $category, $cat->slug, false ),
							esc_html( $cat->name )
						);
					}
				}
				?>
			</select>
		</p>

		<hr />

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>"><?php esc_html_e( 'Type tag', 'makhadane' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_tag' ) ); ?>" type="text" value="<?php echo esc_attr( $post_tag ); ?>" />
			<small><?php esc_html_e( 'Separate by comma', 'makhadane' ); ?></small>
		</p>

		<hr />

		<h4><?php esc_html_e( 'Display options', 'makhadane' ); ?></h4>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'makhadane' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $number ); ?>" style="width:60px;" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order post by:', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php foreach ( $orderbys as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $orderby, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'makhadane' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php foreach ( $orders as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $order, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}
}
