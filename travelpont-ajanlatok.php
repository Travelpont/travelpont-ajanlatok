<?php
/**
 * Plugin Name: Travelpont Ajánlatok
 * Plugin URI:  https://travelpont.hu
 * Description: Repülő-, busz- vagy csak szállás-ajánlatok kezelése és kártyás megjelenítése – ACF-mentes, önálló plugin, az aktivbalaton.hu plugin-konvenciók mintájára.
 * Version:     1.9.0
 * Author:      travelpont.hu
 * Text Domain: travelpont-ajanlatok
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tpa_plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
define( 'TPA_VERSION', $tpa_plugin_data['Version'] );
define( 'TPA_PATH', plugin_dir_path( __FILE__ ) );
define( 'TPA_URL',  plugin_dir_url( __FILE__ ) );

// ── Egyedi mezők panel elrejtése ──────────────────────────────────────────────
add_action( 'add_meta_boxes', function() {
    remove_meta_box( 'postcustom', 'ajanlat', 'normal' );
}, 99 );

// ── Modulok betöltése ─────────────────────────────────────────────────────────
require_once TPA_PATH . 'includes/fields.php';
require_once TPA_PATH . 'includes/cpt.php';
require_once TPA_PATH . 'includes/meta-boxes.php';
require_once TPA_PATH . 'includes/shortcodes.php';
require_once TPA_PATH . 'includes/single-display.php';
require_once TPA_PATH . 'includes/rest-api.php';

// ── Aktiválás / deaktiválás: permalink szabályok frissítése ───────────────────
register_activation_hook( __FILE__, function() {
    tpa_register_cpt();
    tpa_register_kategoria_taxonomy();
    flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

// ── Frontend eszközök ─────────────────────────────────────────────────────────
// Csak regisztrálunk – a shortcode és a single nézet tölti be ténylegesen,
// így az oldal többi részét nem lassítjuk fölöslegesen.
add_action( 'wp_enqueue_scripts', function() {
    wp_register_style(
        'travelpont-ajanlatok',
        TPA_URL . 'assets/css/frontend.css',
        array(), TPA_VERSION
    );
    wp_register_script(
        'travelpont-ajanlatok-galeria',
        TPA_URL . 'assets/js/galeria-lightbox.js',
        array(), TPA_VERSION, true
    );
    if ( is_singular( 'ajanlat' ) ) {
        wp_enqueue_style( 'travelpont-ajanlatok' );
        wp_enqueue_script( 'travelpont-ajanlatok-galeria' );
    }
} );

// ── Admin eszközök ────────────────────────────────────────────────────────────
add_action( 'admin_enqueue_scripts', function() {
    global $post_type;
    if ( $post_type !== 'ajanlat' ) return;
    wp_enqueue_style(
        'travelpont-ajanlatok-admin',
        TPA_URL . 'assets/css/admin.css',
        array(), TPA_VERSION
    );
    wp_enqueue_script(
        'travelpont-ajanlatok-admin-tipus-toggle',
        TPA_URL . 'assets/js/admin-tipus-toggle.js',
        array(), TPA_VERSION, true
    );
} );
