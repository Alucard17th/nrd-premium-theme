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

// add_action( 'ocdi/after_import', function ( $selected ) {

//     error_log('Selected: ' . print_r($selected, true));
// 	// turn "01 · Hero Showcase" → "hero-showcase"
// 	$slug = sanitize_title( $selected['custom_slug'] );

// 	$kit_zip = get_theme_file_path( "demo-data/{$slug}/elementor-kit.zip" );

// 	if ( class_exists( '\Elementor\Plugin' ) && file_exists( $kit_zip ) ) {
// 		\Elementor\Plugin::$instance
// 			->app
// 			->get_component( 'import-export' )
// 			->import_kit( $kit_zip, [ 'referrer' => 'remote' ] );

//         error_log('Kit Imported: ' . print_r($kit_zip, true));
// 	}else{
//         error_log('Error In Kit or Elementor: ' . print_r($kit_zip, true));
//     }

//     /* ---- Regenerate Elementor CSS ---- */
// 	if ( class_exists( '\Elementor\Plugin' ) ) {
// 		\Elementor\Plugin::$instance->files_manager->clear_cache();
// 	}

// 	// If you use UAEL or other addons that bundle their own CSS cache,
// 	// also clear them:
// 	if ( function_exists( 'uael_clear_asset_cache' ) ) {
// 		uael_clear_asset_cache();
// 	}

// } );

// add_action('ocdi/after_import', function($selected) {
//     // Log selected data for debugging
//     error_log('Selected demo: ' . print_r($selected, true));
    
//     if (!isset($selected['custom_slug'])) {
//         error_log('Error: custom_slug not set in selected demo data');
//         return;
//     }

//     $slug = sanitize_title($selected['custom_slug']);
//     $kit_zip = get_theme_file_path("demo-data/{$slug}/elementor-kit.zip");

//     // Check if file exists and is readable
//     if (!file_exists($kit_zip) || !is_readable($kit_zip)) {
//         error_log('Error: Kit file not found or not readable: ' . $kit_zip);
//         return;
//     }

//     // Verify Elementor is active and has the required component
//     if (!class_exists('\Elementor\Plugin') || 
//         !isset(\Elementor\Plugin::$instance->app) ||
//         !method_exists(\Elementor\Plugin::$instance->app, 'get_component')) {
//         error_log('Error: Elementor not properly initialized');
//         return;
//     }

//     try {
//         $import_component = \Elementor\Plugin::$instance
//             ->app
//             ->get_component('import-export');
            
//         if (!$import_component) {
//             error_log('Error: Elementor Import-Export component not available');
//             return;
//         }

//         $result = $import_component->import_kit($kit_zip, ['referrer' => 'remote']);
        
//         if (is_wp_error($result)) {
//             error_log('Kit import failed: ' . $result->get_error_message());
//         } else {
//             error_log('Kit imported successfully: ' . $kit_zip);
            
//             // Clear Elementor cache
//             \Elementor\Plugin::$instance->files_manager->clear_cache();
            
//             // Clear UAEL cache if exists
//             if (function_exists('uael_clear_asset_cache')) {
//                 uael_clear_asset_cache();
//             }
            
//             // Add additional cache clearing for other addons if needed
//         }
//     } catch (Exception $e) {
//         error_log('Exception during kit import: ' . $e->getMessage());
//     }
// });

/**
 *  OCDI: run *after* the XML/widgets/customizer have finished importing.
 *
 *  – $selected_import is the array you returned in pt-ocdi/import_files.
 *    We use its import_file_name to build the path to the kit ZIP.
 */
/**
 * One-click Demo → import Elementor kit → activate it → regenerate CSS.
 * Runs automatically after OCDI finishes importing pages/widgets/customizer.
 */
add_action( 'pt-ocdi/after_import', 'nrd_after_demo_import', 10, 1 );

function nrd_after_demo_import( array $selected ) {

	/* ─────────────────────────────────────────────────────────────
	 * 1. Locate the kit ZIP that belongs to this demo
	 *    (we store one zip per demo folder: demo-data/<slug>/elementor-kit.zip)
	 * ──────────────────────────────────────────────────────────── */
	if ( empty( $selected['custom_slug'] ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}

	$slug    = sanitize_title( $selected['custom_slug'] );           // eg “hero-showcase”
	$kit_zip = get_theme_file_path( "demo-data/{$slug}/elementor-kit.zip" );

	if ( ! file_exists( $kit_zip ) ) {
		return;                                                      // nothing to import
	}

	/* ─────────────────────────────────────────────────────────────
	 * 2. Import the kit and capture its post-ID
	 * ──────────────────────────────────────────────────────────── */
	$ie   = \Elementor\Plugin::$instance->app->get_component( 'import-export' );
	$resp = $ie->import_kit( $kit_zip, [ 'referrer' => 'remote' ] );

	$kit_id = is_array( $resp ) && ! empty( $resp['kit_id'] )
		? (int) $resp['kit_id']
		: 0;

	/* fallback – newest kit by date (old Elementor builds) */
	if ( ! $kit_id ) {
		$kit = get_posts( [
			'post_type'   => 'elementor_library',
			'meta_key'    => '_elementor_template_type',
			'meta_value'  => 'kit',
			'orderby'     => 'date',
			'order'       => 'DESC',
			'numberposts' => 1,
			'fields'      => 'ids',
		] );
		$kit_id = $kit ? (int) $kit[0] : 0;
	}

	if ( ! $kit_id ) {
		return;
	}

	/* ─────────────────────────────────────────────────────────────
	 * 3. Activate the kit (works on every Elementor version)
	 * ──────────────────────────────────────────────────────────── */
	update_option( 'elementor_active_kit', $kit_id );

	$km = \Elementor\Plugin::$instance->kits_manager;
	if ( method_exists( $km, 'switch_active_kit' ) ) {               // ≥ 3.11
		$km->switch_active_kit( $kit_id );
	}

	/* ─────────────────────────────────────────────────────────────
	 * 4. Flush + rebuild CSS so selectors use the new kit-ID
	 * ──────────────────────────────────────────────────────────── */
	$fm = \Elementor\Plugin::$instance->files_manager;
	$fm->clear_cache();                                              // wipe /uploads/elementor/css/

	$post_ids = get_posts( [
		'post_type'      => [ 'page', 'post', 'elementor_library' ],
		'posts_per_page' => -1,
		'meta_key'       => '_elementor_edit_mode',
		'fields'         => 'ids',
	] );

	if ( method_exists( $fm, 'regenerate_css' ) ) {                  // ≥ 3.10
		$fm->regenerate_css( $post_ids );
	} elseif ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {  // 2.9 – 3.9
		foreach ( $post_ids as $id ) {
			\Elementor\Core\Files\CSS\Post::create( $id )->update();
		}
	} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {        // ≤ 2.8
		foreach ( $post_ids as $id ) {
			\Elementor\Post_CSS_File::create( $id )->update();
		}
	}

	/* ─────────────────────────────────────────────────────────────
	 * 5. Flush UAEL or other addon caches
	 * ──────────────────────────────────────────────────────────── */
	if ( function_exists( 'uael_clear_asset_cache' ) ) {
		uael_clear_asset_cache();
	}
}
