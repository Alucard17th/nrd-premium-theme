<?php

// Load styles and scripts
function mpt_enqueue_scripts() {
    wp_enqueue_style('mpt-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'mpt_enqueue_scripts');

// Theme supports
add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('menus');
    
    // Register menu
    register_nav_menus([
        'main_menu' => __('Main Menu', 'nrd-premium-theme')
    ]);

    // Elementor support
    add_theme_support('elementor');

});

// Activate plugins
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
add_action('tgmpa_register', 'mpt_register_required_plugins');

// Admin Menu 
require_once get_template_directory() . '/admin/admin.php';
// OCDI DEMOS 
require_once get_template_directory() . '/admin/ocdi-demos.php';

function mpt_register_required_plugins() {
    $plugins = [
        [
            'name'     => 'Elementor',
            'slug'     => 'elementor',
            'required' => true,
        ],
        [
            'name'     => 'One Click Demo Import',
            'slug'     => 'one-click-demo-import',
            'required' => true,
        ],
        [
            'name'     => 'Contact Form 7',
            'slug'     => 'contact-form-7',
            'required' => true, // Recommended but not required
        ],
        [
            'name'     => 'Ultimate Addons for Elementor Lite',
            'slug'     => 'header-footer-elementor',
            'required' => true, // Recommended but not required
        ],
        [
            'name'               => 'NRD Theme Companion',                 // what users see
            'slug'               => 'nrd-theme-companion',                 // folder + main file prefix
            'source'             => get_template_directory() . '/plugins/nrd-theme-companion.zip',
            'required'           => true,       // “This theme won’t work without it”
            'version'            => '1.0.0',    // minimum version (optional but recommended)
            'force_activation'   => true,       // auto-activate when theme activates
            'force_deactivation' => true,       // auto-deactivate if theme is switched
        ],
    ];

    $config = [
        'id'           => 'nrd-premium-theme',
        'menu'         => 'install-required-plugins',
        'has_notices'  => true,
        'dismissable'  => false,
        'is_automatic' => false,
    ];

    tgmpa($plugins, $config);
}

// add_filter( 'elementor/utils/register_elementor_post_type_args', function ( $args ) {
// 	$args['can_export'] = true;
// 	return $args;
// }, 10, 1 );

add_action( 'ocdi/after_import', function ( $selected ) {

    error_log('Selected: ' . print_r($selected, true));
	// turn "01 · Hero Showcase" → "hero-showcase"
	$slug = sanitize_title( $selected['custom_slug'] );

	$kit_zip = get_theme_file_path( "demo-data/{$slug}/elementor-kit.zip" );

	if ( class_exists( '\Elementor\Plugin' ) && file_exists( $kit_zip ) ) {
		\Elementor\Plugin::$instance
			->app
			->get_component( 'import-export' )
			->import_kit( $kit_zip, [ 'referrer' => 'remote' ] );

        error_log('Kit Imported: ' . print_r($kit_zip, true));

        // 2.  Tell Elementor to inherit fonts & colours from the theme ------
        //    (same effect as ticking the two check-boxes in Elementor → Settings)
        update_option( 'elementor_disable_color_schemes',      'yes' ); // Disable Default Colors
        update_option( 'elementor_disable_typography_schemes', 'yes' ); // Disable Default Fonts
	}else{
        error_log('Error In Kit or Elementor: ' . print_r($kit_zip, true));
    }

    // /* ---------- set Home 1 as the static front page ---------- */
    // // try slug first (faster), fall back to page title
    // $front_page = get_page_by_path( 'home-1' );           // slug after import
    // if ( ! $front_page ) {
    //     $front_page = get_page_by_title( 'Home 1' );      // title as seen in the XML
    // }

    // if ( $front_page ) {
    //     update_option( 'show_on_front', 'page' );         // switch to static page
    //     update_option( 'page_on_front', $front_page->ID );
    // }

    // /* ---------- optional: set a Posts / Blog page ---------- */
    // $blog_page = get_page_by_path( 'blog' ) ?: get_page_by_title( 'Blog' );
    // if ( $blog_page ) {
    //     update_option( 'page_for_posts', $blog_page->ID );
    // }

    /* ---- Regenerate Elementor CSS ---- */
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	// If you use UAEL or other addons that bundle their own CSS cache,
	// also clear them:
	if ( function_exists( 'uael_clear_asset_cache' ) ) {
		uael_clear_asset_cache();
	}

}, 20 );