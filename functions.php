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

add_action( 'pt-ocdi/after_import', 'mytheme_ocdi_after_import' );

function mytheme_ocdi_after_import( $import ) {

	// your existing front-page / menu logic here …

	/* ---- Regenerate Elementor CSS ---- */
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	// If you use UAEL or other addons that bundle their own CSS cache,
	// also clear them:
	if ( function_exists( 'uael_clear_asset_cache' ) ) {
		uael_clear_asset_cache();
	}
}

add_filter( 'elementor/utils/register_elementor_post_type_args', function ( $args ) {
	$args['can_export'] = true;
	return $args;
}, 10, 1 );

add_action( 'ocdi/after_import', function () {

    /* — 1  set static front page / menus (your existing code) — */

    /* — 2  pick the **latest** imported kit and activate it — */
    if ( class_exists( '\Elementor\Plugin' ) ) {

        $kits = get_posts( [
            'post_type'   => 'elementor_library',
            'meta_key'    => '_elementor_template_type',
            'meta_value'  => 'kit',
            'orderby'     => 'date',
            'order'       => 'DESC',   // newest first
            'numberposts' => 1,
            'fields'      => 'ids',
        ] );

        if ( $kits ) {
            $kit_id = (int) $kits[0];
            update_option( 'elementor_active_kit', $kit_id );
            \Elementor\Plugin::$instance->kits_manager->switch_active_kit( $kit_id );
        }

        // Clear & regenerate CSS so global tokens compile
        \Elementor\Plugin::$instance->files_manager->clear_cache();
        if ( function_exists( 'uael_clear_asset_cache' ) ) {
            uael_clear_asset_cache();
        }
    }
} );
