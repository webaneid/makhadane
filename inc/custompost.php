<?php
/**
 * Custom Post Types and Taxonomies
 *
 * @package Makhadane
 * @since 4.1.1
 */

/**
 * Register custom post types
 *
 * Registers ustadz, ekstrakurikuler, testimoni, and FAQ post types.
 *
 * @since 4.1.1
 * @return void
 */
function ane_custom_post_types() {
    $post_types = array(
        'ustadz'          => array(
            'singular' => __( 'Ustadz', 'makhadane' ),
            'plural'   => __( 'Asatidz', 'makhadane' ),
            'icon'     => 'dashicons-businessman',
            'supports' => array( 'title', 'editor', 'thumbnail' ),
        ),
        'ekstrakurikuler' => array(
            'singular' => __( 'Ekstrakurikuler', 'makhadane' ),
            'plural'   => __( 'Ekstrakurikuler', 'makhadane' ),
            'icon'     => 'dashicons-groups',
            'supports' => array( 'title', 'editor', 'thumbnail' ),
        ),
        'testimoni'       => array(
            'singular' => __( 'Testimoni', 'makhadane' ),
            'plural'   => __( 'Testimoni', 'makhadane' ),
            'icon'     => 'dashicons-admin-comments',
            'supports' => array( 'title', 'thumbnail' ),
        ),
        'faq'             => array(
            'singular' => __( 'FAQ', 'makhadane' ),
            'plural'   => __( 'FAQs', 'makhadane' ),
            'icon'     => 'dashicons-editor-help',
            'supports' => array( 'title', 'editor' ),
        ),
    );

    foreach ( $post_types as $post_type => $data ) {
        $labels = array(
            'name'                  => $data['plural'],
            'singular_name'         => $data['singular'],
            'menu_name'             => $data['plural'],
            /* translators: %s: Post type plural name */
            'all_items'             => sprintf( __( 'Semua %s', 'makhadane' ), $data['plural'] ),
            /* translators: %s: Post type singular name */
            'add_new'               => sprintf( __( 'Tambah %s Baru', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'add_new_item'          => sprintf( __( 'Tambah %s Baru', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'new_item'              => sprintf( __( '%s Baru', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'edit_item'             => sprintf( __( 'Edit %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'update_item'           => sprintf( __( 'Update %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'search_items'          => sprintf( __( 'Cari %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'featured_image'        => sprintf( __( 'Foto %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'set_featured_image'    => sprintf( __( 'Upload Foto %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'remove_featured_image' => sprintf( __( 'Hapus Foto %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Post type singular name */
            'use_featured_image'    => sprintf( __( 'Jadikan Foto %s', 'makhadane' ), $data['singular'] ),
            'name_admin_bar'        => $data['singular'],
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'capability_type'     => 'post',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'has_archive'         => true,
            'hierarchical'        => false,
            'show_in_rest'        => true,
            'menu_position'       => 10,
            'menu_icon'           => $data['icon'],
            'supports'            => $data['supports'],
        );

        register_post_type( $post_type, $args );
    }
}
add_action( 'init', 'ane_custom_post_types' );

/**
 * Register custom taxonomies
 *
 * Registers pelajaran and kelas taxonomies for ustadz post type.
 *
 * @since 4.1.1
 * @return void
 */
function ane_register_custom_taxonomies() {
    $taxonomies = array(
        'pelajaran' => array(
            'singular'  => __( 'Pelajaran', 'makhadane' ),
            'plural'    => __( 'Pelajaran', 'makhadane' ),
            'post_type' => array( 'ustadz' ),
            'slug'      => 'pelajaran',
        ),
        'kelas'     => array(
            'singular'  => __( 'Kelas', 'makhadane' ),
            'plural'    => __( 'Kelas', 'makhadane' ),
            'post_type' => array( 'ustadz' ),
            'slug'      => 'kelas',
        ),
    );

    foreach ( $taxonomies as $taxonomy => $data ) {
        $labels = array(
            'name'              => $data['plural'],
            'singular_name'     => $data['singular'],
            /* translators: %s: Taxonomy plural name */
            'search_items'      => sprintf( __( 'Cari %s', 'makhadane' ), $data['plural'] ),
            /* translators: %s: Taxonomy plural name */
            'all_items'         => sprintf( __( 'Semua %s', 'makhadane' ), $data['plural'] ),
            /* translators: %s: Taxonomy singular name */
            'parent_item'       => sprintf( __( 'Parent %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Taxonomy singular name */
            'parent_item_colon' => sprintf( __( 'Parent %s:', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Taxonomy singular name */
            'edit_item'         => sprintf( __( 'Edit %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Taxonomy singular name */
            'update_item'       => sprintf( __( 'Update %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Taxonomy singular name */
            'add_new_item'      => sprintf( __( 'Tambah %s', 'makhadane' ), $data['singular'] ),
            /* translators: %s: Taxonomy singular name */
            'new_item_name'     => sprintf( __( '%s Baru', 'makhadane' ), $data['singular'] ),
            'menu_name'         => $data['plural'],
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => $data['slug'] ),
        );

        register_taxonomy( $taxonomy, $data['post_type'], $args );
    }
}
add_action( 'init', 'ane_register_custom_taxonomies' );


/**
 * Include custom post types in RSS feed
 *
 * Adds ustadz and ekstrakurikuler to main RSS feed.
 *
 * @since 4.1.1
 * @param WP_Query $query The WP_Query instance
 * @return void
 */
function ane_custom_post_feed( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_feed() ) {
        $query->set( 'post_type', array( 'post', 'ustadz', 'ekstrakurikuler' ) );
    }
}
add_filter( 'pre_get_posts', 'ane_custom_post_feed' );


/**
 * Customize admin column titles for custom post types
 *
 * Changes 'Title' column label for ustadz, testimoni, and ekstrakurikuler.
 *
 * @since 4.1.1
 * @param array $columns Existing admin columns
 * @return array Modified columns
 */
function ane_custom_post_titles_columns( $columns ) {
    global $post_type;

    $custom_titles = array(
        'ustadz'          => __( 'Nama Ustadz', 'makhadane' ),
        'testimoni'       => __( 'Pemberi Testimoni', 'makhadane' ),
        'ekstrakurikuler' => __( 'Ekstrakurikuler', 'makhadane' ),
    );

    if ( isset( $custom_titles[ $post_type ] ) ) {
        $new_columns = array();
        foreach ( $columns as $key => $value ) {
            if ( 'title' === $key ) {
                $new_columns['custom_title'] = $custom_titles[ $post_type ];
            } else {
                $new_columns[ $key ] = $value;
            }
        }
        return $new_columns;
    }

    return $columns;
}

/**
 * Display custom column content for custom post types
 *
 * Outputs clickable title link in custom column.
 *
 * @since 4.1.1
 * @param string $column  Column name
 * @param int    $post_id Post ID
 * @return void
 */
function ane_custom_post_column_content( $column, $post_id ) {
    if ( 'custom_title' === $column ) {
        $edit_url = get_edit_post_link( $post_id );
        printf(
            '<a href="%s">%s</a>',
            esc_url( $edit_url ),
            esc_html( get_the_title( $post_id ) )
        );
    }
}

add_filter( 'manage_edit-ustadz_columns', 'ane_custom_post_titles_columns' );
add_filter( 'manage_edit-testimoni_columns', 'ane_custom_post_titles_columns' );
add_filter( 'manage_edit-ekstrakurikuler_columns', 'ane_custom_post_titles_columns' );

add_action( 'manage_ustadz_posts_custom_column', 'ane_custom_post_column_content', 10, 2 );
add_action( 'manage_testimoni_posts_custom_column', 'ane_custom_post_column_content', 10, 2 );
add_action( 'manage_ekstrakurikuler_posts_custom_column', 'ane_custom_post_column_content', 10, 2 );

/**
 * Customize FAQ admin column title
 *
 * Changes 'Title' column to 'Pertanyaan' for FAQ post type.
 *
 * @since 4.1.1
 * @param array $columns Existing admin columns
 * @return array Modified columns
 */
function ane_faq_title_column( $columns ) {
    if ( isset( $columns['title'] ) ) {
        unset( $columns['title'] );
    }

    $new_columns = array(
        'cb'         => $columns['cb'],
        'pertanyaan' => __( 'Pertanyaan', 'makhadane' ),
    );

    unset( $columns['cb'] );

    return array_merge( $new_columns, $columns );
}
add_filter( 'manage_faq_posts_columns', 'ane_faq_title_column' );

/**
 * Display FAQ column content
 *
 * Outputs clickable question link in pertanyaan column.
 *
 * @since 4.1.1
 * @param string $column  Column name
 * @param int    $post_id Post ID
 * @return void
 */
function ane_faq_column_content( $column, $post_id ) {
    if ( 'pertanyaan' === $column ) {
        $edit_url = get_edit_post_link( $post_id );
        printf(
            '<a href="%s">%s</a>',
            esc_url( $edit_url ),
            esc_html( get_the_title( $post_id ) )
        );
    }
}
add_action( 'manage_faq_posts_custom_column', 'ane_faq_column_content', 10, 2 );


/**
 * Display related ustadz by taxonomy
 *
 * Shows related ustadz posts based on shared taxonomy terms.
 *
 * @since 4.1.1
 * @param string $taxonomy Taxonomy slug (pelajaran or kelas)
 * @return void
 */
function ane_tampilkan_ustadz_terkait( $taxonomy ) {
    $terms = get_the_terms( get_the_ID(), $taxonomy );

    if ( ! $terms || is_wp_error( $terms ) ) {
        return;
    }

    $namaustadz = '';
    foreach ( $terms as $term ) {
        $namaustadz = $term->name;
    }

    $term_ids = wp_list_pluck( $terms, 'term_id' );

    $related_query = new WP_Query(
        array(
            'post_type'           => 'ustadz',
            'tax_query'           => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                    'operator' => 'IN',
                ),
            ),
            'posts_per_page'      => 4,
            'ignore_sticky_posts' => true,
            'orderby'             => 'rand',
            'post__not_in'        => array( get_the_ID() ),
            'no_found_rows'       => true,
        )
    );

    if ( $related_query->have_posts() ) :
        ?>
        <div class="ane-container">
            <div class="konten-terkait-v2">
                <h2>
                    <?php
                    printf(
                        /* translators: %s: Taxonomy term name */
                        esc_html__( 'Asatidz %s', 'makhadane' ),
                        esc_html( $namaustadz )
                    );
                    ?>
                </h2>
                <div id="post-block-slider" class="owl-carousel">
                    <?php
                    while ( $related_query->have_posts() ) :
                        $related_query->the_post();
                        get_template_part( 'tp/content', 'ustadz' );
                    endwhile;
                    ?>
                </div>
            </div>
        </div>
        <?php
    endif;

    wp_reset_postdata();
}