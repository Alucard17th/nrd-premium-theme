<?php
/* --------------------------------------------------------------------
 * 1. Admin menu (Dashboard ▸ MyTheme)
 * ------------------------------------------------------------------*/
add_action( 'admin_menu', 'mytheme_register_admin_pages', 15 ); // <- priority 15

function mytheme_register_admin_pages() {

	$cap         = 'manage_options';
	$parent_slug = 'mytheme-dashboard';

	/* Top-level item -------------------------------------------------- */
	add_menu_page(
		__( 'MyTheme', 'mytheme' ),
		'MyTheme',
		$cap,
		$parent_slug,
		'mytheme_dashboard_screen',
		'dashicons-admin-customizer',
		3
	);

	/* Child 1 – Getting Started -------------------------------------- */
	add_submenu_page(
		$parent_slug,
		__( 'Getting Started', 'mytheme' ),
		__( 'Getting Started', 'mytheme' ),
		$cap,
		$parent_slug,               // same slug → default child
		'mytheme_dashboard_screen'
	);

	/* Child 3 – Support ---------------------------------------------- */
	add_submenu_page(
		$parent_slug,
		__( 'Support', 'mytheme' ),
		__( 'Support', 'mytheme' ),
		$cap,
		'mytheme-support',
		'mytheme_support_screen'
	);
}

/* --------------------------------------------------------------------
 * 2. Callback stubs
 * ------------------------------------------------------------------*/
function mytheme_dashboard_screen() {
	echo '<div class="wrap"><h1>'. esc_html__( 'Welcome to MyTheme', 'mytheme' ) .'</h1>
	<p>'. esc_html__( 'Quick links, docs, and changelog go here.', 'mytheme' ) .'</p></div>';

    if ( ! mytheme_has_ocdi() ) {
        echo 'Plugin Installed';
    }else{
        echo 'Plugin Not Installed';
    }
}

function mytheme_support_screen() {
	echo '<div class="wrap"><h1>'. esc_html__( 'Need help?', 'mytheme' ) .'</h1>
	<p><a href="https://example.com/support" target="_blank">Open a ticket</a></p></div>';
}

function mytheme_has_ocdi() {
	return defined( 'PT_OCDI_VERSION' )                      // new constant
	    || function_exists( 'pt_one_click_demo_import' )    // helper function
	    || class_exists( '\OCDI\OneClickDemoImport' );      // namespaced class
}

// /* --------------------------------------------------------------------
//  * 3. Tell OCDI to live under the same menu when it IS active
//  * ------------------------------------------------------------------*/
// add_filter( 'ocdi/parent_slug', fn() => 'mytheme-dashboard' );
// add_filter( 'ocdi/menu_slug',  fn() => 'mytheme-demo-import' );
// add_filter( 'ocdi/page_title', fn() => __( 'Import a Starter Site', 'mytheme' ) );
// add_filter( 'ocdi/menu_title', fn() => __( 'Demo Import', 'mytheme' ) );

// /* Optional – hide the default Tools ▸ Import item */
// add_filter( 'ocdi/disable_pt_branding', '__return_true' );

// add_filter( 'pt-ocdi/plugin_page_setup', 'mytheme_move_ocdi_menu' );
// function mytheme_move_ocdi_menu( $args ) {
//     return array(
//         'parent_slug' => 'mytheme-dashboard',
//         'page_title' => esc_html__( 'Import Demo Data', 'mytheme' ),
//         'menu_title' => esc_html__( 'Demo Import', 'mytheme' ),
//         'capability' => 'manage_options', // Must match your menu capability
//         'menu_slug'  => 'mytheme-demo-import',
//     );
// }

// /* Optional - Hide OCDI branding */
// add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );