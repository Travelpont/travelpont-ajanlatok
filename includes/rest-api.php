<?php
/**
 * Travelpont Ajánlatok – REST API (CSONTVÁZ)
 *
 * Prefix: /wp-json/tpa/v1/
 *
 * JELENLEG csak a publikus /status ping él. Ez a fájl a HELYE a későbbi
 * portál-kommunikációnak, az aktivbalaton.hu mintája szerint
 * (E:\aktivbalaton.hu\Saját pluginok\_AKTIV\balaton-szallasok\includes\rest-api.php):
 *
 *   GET  /tpa/v1/ajanlatok          – Lista (szűrés, lapozás)         [KÉSŐBB]
 *   GET  /tpa/v1/ajanlat/{id}       – Egy ajánlat részletei           [KÉSŐBB]
 *   POST /tpa/v1/ajanlat            – Ajánlat létrehozása             [KÉSŐBB]
 *   PUT  /tpa/v1/ajanlat/{id}       – Ajánlat frissítése              [KÉSŐBB]
 *   POST /tpa/v1/ajanlat/{id}/kep   – Kép sideload URL-ből            [KÉSŐBB]
 *   GET  /tpa/v1/meta               – Kategóriák listája              [KÉSŐBB]
 *
 * Auth a write endpointokhoz majd: WordPress Application Password
 * (Basic Auth) + current_user_can( 'publish_posts' ) – lásd tpa_api_auth().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', function() {

    // Publikus státusz / ping – ezzel ellenőrizhető, hogy a plugin él
    register_rest_route( 'tpa/v1', '/status', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'tpa_api_status',
        'permission_callback' => '__return_true',
    ) );

    // Bővítési pont: a későbbi portál-endpointok innen regisztrálhatók
    do_action( 'tpa_rest_api_init' );
} );

// ── Auth a későbbi write endpointokhoz (már előkészítve) ──────────────────────
function tpa_api_auth() {
    return current_user_can( 'publish_posts' );
}

// ── GET /tpa/v1/status ────────────────────────────────────────────────────────
function tpa_api_status() {
    return rest_ensure_response( array(
        'plugin'     => 'Travelpont Ajánlatok REST API',
        'version'    => TPA_VERSION,
        'cpt_exists' => post_type_exists( 'ajanlat' ),
    ) );
}
