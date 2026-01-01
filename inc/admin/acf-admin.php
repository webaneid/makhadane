<?php
/**
 * Register ACF Field Group for Page Schema Type
 * Allows selecting schema.org type for each page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'acf/init', 'ane_register_page_schema_field' );

function ane_register_page_schema_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_page_schema',
			'title'    => 'SEO Schema Settings',
			'fields'   => array(
				array(
					'key'           => 'field_ane_page_schema_type',
					'label'         => 'Schema Type',
					'name'          => 'ane_page_schema_type',
					'type'          => 'select',
					'instructions'  => 'Pilih jenis schema yang sesuai dengan konten halaman. Schema ini akan muncul di hasil pencarian Google dan membantu SEO.',
					'required'      => 0,
					'default_value' => 'WebPage',
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 1,
					'ajax'          => 0,
					'return_format' => 'value',
					'placeholder'   => 'Pilih Schema Type',
					'choices'       => array(
						'WebPage'           => 'WebPage (Default) - Halaman standar',
						'AboutPage'         => 'AboutPage - Halaman tentang kami / profil',
						'ContactPage'       => 'ContactPage - Halaman kontak',
						'FAQPage'           => 'FAQPage - Halaman FAQ / tanya jawab',
						'ProfilePage'       => 'ProfilePage - Halaman profil organisasi/perusahaan',
						'CollectionPage'    => 'CollectionPage - Halaman koleksi/kumpulan konten',
						'ItemPage'          => 'ItemPage - Halaman item individual',
						'SearchResultsPage' => 'SearchResultsPage - Halaman hasil pencarian',
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
			),
			'menu_order'            => 20,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => 'Schema.org structured data untuk halaman',
		)
	);
}