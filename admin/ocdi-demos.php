<?php
/**
 * Tell OCDI about our five starter sites.
 */
add_filter( 'pt-ocdi/import_files', 'mytheme_ocdi_demos' );

function mytheme_ocdi_demos() {

	$base = get_template_directory_uri() . '/demo-data/';

	return [
		[
			'import_file_name'           => '01 · Saas Starter',
			'import_file_url'            => $base . 'hero-showcase/content.xml',
			'import_widget_file_url'     => $base . 'hero-showcase/widgets.wie',     // omit line if none
			'import_customizer_file_url' => $base . 'hero-showcase/customizer.dat',  // omit line if none
			'import_preview_image_url'   => $base . 'hero-showcase/preview.png',
			'import_notice'              => __( 'Hang tight—importing the Hero Showcase demo.', 'mytheme' ),
			'preview_url'                => 'https://demo.yoursite.com/hero-showcase', // optional live link
			'custom_slug'                => 'hero-showcase', // optional. Use this for a custom slug instead of the imported filename
		],
        [
			'import_file_name'           => '02 · Hero Def',
			'import_file_url'            => $base . 'hero-showcase/content.xml',
			'import_widget_file_url'     => $base . 'hero-showcase/widgets.wie',     // omit line if none
			'import_customizer_file_url' => $base . 'hero-showcase/customizer.dat',  // omit line if none
			'import_preview_image_url'   => $base . 'hero-showcase/preview.png',
			'import_notice'              => __( 'Hang tight—importing the Hero Showcase demo.', 'mytheme' ),
			'preview_url'                => 'https://demo.yoursite.com/hero-showcase', // optional live link
			'custom_slug'                => 'hero-def', // optional. Use this for a custom slug instead of the imported filename
		]

		/* duplicate & adjust four more blocks … */
	];
}
