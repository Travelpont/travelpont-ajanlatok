<?php
/**
 * Travelpont Ajánlatok – REST API
 *
 * Prefix: /wp-json/tpa/v1/
 *
 *   GET  /tpa/v1/ajanlatok          – Lista (szűrés, lapozás)
 *   GET  /tpa/v1/ajanlat/{id}       – Egy ajánlat részletei
 *   POST /tpa/v1/ajanlat            – Ajánlat létrehozása
 *   PUT  /tpa/v1/ajanlat/{id}       – Ajánlat frissítése
 *   POST /tpa/v1/ajanlat/{id}/kep   – Kiemelt kép sideload URL-ből
 *   POST /tpa/v1/ajanlat/{id}/galeria             – Galéria-kép hozzáadása URL-ből
 *   DELETE /tpa/v1/ajanlat/{id}/galeria/{kep_id}   – Galéria-kép eltávolítása
 *   GET  /tpa/v1/meta               – Kategóriák + Úticélok listája (Portál form-mezőkhöz)
 *   GET  /tpa/v1/status             – Publikus ping
 *
 * Auth: WordPress Application Password (Basic Auth) + publish_posts capability
 * (a Travelpont Portal Firebase Cloud Function proxyja hívja, sosem a böngésző közvetlenül).
 *
 * Yoast SEO mezők (seo_title/seo_metadesc) a create/update végpontokon keresztül
 * íródnak/olvasódnak (_yoast_wpseo_title / _yoast_wpseo_metadesc postmeta) – NEM a
 * tpa_get_fields() rendszer része, mert ezek Yoast saját mezői, nem a plugin sajátjai.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', function() {

    register_rest_route( 'tpa/v1', '/ajanlatok', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'tpa_api_list',
        'permission_callback' => 'tpa_api_auth',
        'args'                => array(
            'per_page' => array( 'type' => 'integer', 'default' => 20, 'minimum' => 1, 'maximum' => 100 ),
            'page'     => array( 'type' => 'integer', 'default' => 1,  'minimum' => 1 ),
            'search'   => array( 'type' => 'string',  'default' => '' ),
            'status'   => array( 'type' => 'string',  'default' => 'publish', 'enum' => array( 'publish', 'draft', 'any' ) ),
            'kategoria'=> array( 'type' => 'string',  'default' => '' ),
            'uticel_id'=> array( 'type' => 'integer', 'default' => 0 ),
        ),
    ) );

    register_rest_route( 'tpa/v1', '/ajanlat/(?P<id>\d+)', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'tpa_api_get',
            'permission_callback' => 'tpa_api_auth',
        ),
        array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => 'tpa_api_update',
            'permission_callback' => 'tpa_api_auth',
        ),
    ) );

    register_rest_route( 'tpa/v1', '/ajanlat', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'tpa_api_create',
        'permission_callback' => 'tpa_api_auth',
        'args'                => tpa_api_args(),
    ) );

    register_rest_route( 'tpa/v1', '/ajanlat/(?P<id>\d+)/kep', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'tpa_api_sideload_image',
        'permission_callback' => 'tpa_api_auth',
        'args'                => array(
            'url' => array( 'type' => 'string', 'required' => true ),
        ),
    ) );

    register_rest_route( 'tpa/v1', '/ajanlat/(?P<id>\d+)/galeria', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'tpa_api_galeria_add',
        'permission_callback' => 'tpa_api_auth',
        'args'                => array(
            'url' => array( 'type' => 'string', 'required' => true ),
        ),
    ) );

    register_rest_route( 'tpa/v1', '/ajanlat/(?P<id>\d+)/galeria/(?P<kep_id>\d+)', array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => 'tpa_api_galeria_remove',
        'permission_callback' => 'tpa_api_auth',
    ) );

    // Felirat mentése egy galéria-képhez (POST ugyanarra az útvonalra).
    register_rest_route( 'tpa/v1', '/ajanlat/(?P<id>\d+)/galeria/(?P<kep_id>\d+)', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'tpa_api_galeria_caption',
        'permission_callback' => 'tpa_api_auth',
        'args'                => array(
            'caption' => array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
        ),
    ) );

    register_rest_route( 'tpa/v1', '/meta', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'tpa_api_meta',
        'permission_callback' => 'tpa_api_auth',
    ) );

    // Publikus státusz / ping – ezzel ellenőrizhető, hogy a plugin él
    register_rest_route( 'tpa/v1', '/status', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'tpa_api_status',
        'permission_callback' => '__return_true',
    ) );

    do_action( 'tpa_rest_api_init' );
} );

// ── Auth ───────────────────────────────────────────────────────────────────────
function tpa_api_auth() {
    return current_user_can( 'publish_posts' );
}

// ── Közös arg-definíciók a create/update endpointokhoz ────────────────────────
// A REST paraméterek neve MEGEGYEZIK a tényleges meta-kulccsal (tpa_get_fields()-ből)
// – nincs külön "portál-nevesítés", a form és a WP admin ugyanazt a mezőnevet látja.
function tpa_api_args() {
    $args = array(
        'title'   => array( 'type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
        'content' => array( 'type' => 'string', 'default'  => '' ),
        'status'  => array( 'type' => 'string', 'default'  => 'publish', 'enum' => array( 'publish', 'draft' ) ),
        'kategoriak' => array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'string' ) ),
        'seo_title'    => array( 'type' => 'string', 'default' => '' ),
        'seo_metadesc' => array( 'type' => 'string', 'default' => '' ),
    );

    foreach ( tpa_get_fields() as $key => $field ) {
        if ( ! empty( $field['readonly'] ) ) continue; // pl. találat dátuma – a plugin kezeli
        $args[ $key ] = array( 'type' => 'string', 'default' => '' );
    }

    return $args;
}

// ── Ajánlat → API válasz formátum ──────────────────────────────────────────────
function tpa_api_format( $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'ajanlat' ) return array();

    $uticel_id        = tpa_mezo( $post_id, 'tpa_uticel' );
    $uticel_title     = $uticel_id ? get_the_title( (int) $uticel_id ) : '';
    $uticel_breadcrumb = tpa_uticel_breadcrumb( $uticel_id ); // "Ország › Régió › Város" (sima szöveg)

    $kategoria_terms = wp_get_post_terms( $post_id, 'ajanlat_kategoria' );
    $kategoriak      = is_wp_error( $kategoria_terms ) ? array() : wp_list_pluck( $kategoria_terms, 'name' );
    $kategoria_ids   = is_wp_error( $kategoria_terms ) ? array() : wp_list_pluck( $kategoria_terms, 'term_id' );

    $thumb_id  = (int) get_post_thumbnail_id( $post_id );
    $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : '';

    $galeria_ids = tpa_api_galeria_ids( $post_id );

    $ar_szamitott = tpa_teljes_ar( $post_id );

    $data = array(
        'id'              => $post_id,
        'title'           => $post->post_title,
        'slug'            => $post->post_name,
        'status'          => $post->post_status,
        'content'         => $post->post_content,
        'uticel_title'      => $uticel_title,
        'uticel_breadcrumb' => $uticel_breadcrumb,
        'ar_szamitott'    => $ar_szamitott,
        'ar_format'       => $ar_szamitott !== '' ? tpa_osszeg_format( $post_id, $ar_szamitott ) : '',
        'idopont_megjelenites' => tpa_idopont_megjelenites( $post_id ), // dátumokból képzett kiírás (vagy kézi szöveg)
        'ejszakak_szam'        => tpa_ejszakak_szam( $post_id ),         // dátumokból számolt éjszakák (vagy kézi érték)
        'ar_megjegyzes_megjelenites' => tpa_ar_megjegyzes_megjelenites( $post_id ),
        'lejart'          => tpa_lejart( $post_id ),
        'deal_lejart'     => tpa_deal_lejart( $post_id ),  // kézi státusz VAGY dátum-lejárat
        'talalat_regi'    => tpa_talalat_regi( $post_id ), // frissesség-küszöbnél régebbi találat
        'hatralevo_napok' => tpa_hatralevo_napok( $post_id ),
        'szallas_platform_nev' => tpa_szallas_platform_nev( $post_id ),
        'kategoriak'      => $kategoriak,
        'kategoria_ids'   => $kategoria_ids,
        'seo_title'       => get_post_meta( $post_id, '_yoast_wpseo_title', true ),
        'seo_metadesc'    => get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ),
        'thumbnail_id'    => $thumb_id,
        'thumbnail_url'   => $thumb_url ?: '',
        'galeria_ids'     => $galeria_ids,
        'galeria_urls'    => tpa_api_galeria_urls( $galeria_ids ),
        'galeria'         => tpa_api_galeria( $galeria_ids ),
        'permalink'       => get_permalink( $post_id ) ?: '',
        'edit_url'        => admin_url( "post.php?post={$post_id}&action=edit" ),
        'created_at'      => get_post_field( 'post_date', $post_id ),
        'modified_at'     => get_post_field( 'post_modified', $post_id ),
    );

    // Minden egyedi mező (tpa_celallomas, tpa_ar, tpa_kiwi_link, stb.) nyers értéke
    foreach ( tpa_get_fields() as $key => $field ) {
        $data[ $key ] = tpa_mezo( $post_id, $key );
    }

    return $data;
}

// ── Egyedi mezők mentése (create/update közös, tpa_get_fields()-re épül) ──────
function tpa_api_save_fields( $post_id, WP_REST_Request $req ) {
    foreach ( tpa_get_fields() as $key => $field ) {
        if ( ! empty( $field['readonly'] ) ) continue; // pl. találat dátuma – a plugin kezeli
        if ( $req->get_param( $key ) === null ) continue;

        $raw   = wp_unslash( $req->get_param( $key ) );
        $type  = isset( $field['type'] ) ? $field['type'] : 'text';
        $value = tpa_sanitize_field_value( $type, $raw, $field );

        update_post_meta( $post_id, $key, $value );
    }

    if ( $req->get_param( 'kategoriak' ) !== null ) {
        $names = array_map( 'sanitize_text_field', (array) $req->get_param( 'kategoriak' ) );
        wp_set_post_terms( $post_id, $names, 'ajanlat_kategoria' );
    }

    if ( $req->get_param( 'seo_title' ) !== null ) {
        update_post_meta( $post_id, '_yoast_wpseo_title', sanitize_text_field( $req->get_param( 'seo_title' ) ) );
    }
    if ( $req->get_param( 'seo_metadesc' ) !== null ) {
        update_post_meta( $post_id, '_yoast_wpseo_metadesc', sanitize_textarea_field( $req->get_param( 'seo_metadesc' ) ) );
    }
    if ( $req->get_param( 'seo_title' ) !== null || $req->get_param( 'seo_metadesc' ) !== null ) {
        tpa_yoast_indexable_frissit( $post_id );
    }

    do_action( 'tpa_after_save_meta', $post_id );
}

// ── Yoast SEO indexable frissítése REST mentés után ───────────────────────────
// A REST API update_post_meta()-val ír, NEM a Yoast admin metabox save_post
// hookján át – ezért a Yoast belső indexable-cache tábláját (wp_yoast_indexable,
// Yoast 14+) direktben kell frissíteni, különben a sitemap/SEO-elemzés a régi
// értékeket mutatja, amíg valaki meg nem nyitja a bejegyzést a klasszikus szerkesztőben.
function tpa_yoast_indexable_frissit( $post_id ) {
    if ( ! function_exists( 'YoastSEO' ) ) {
        clean_post_cache( $post_id );
        return;
    }
    try {
        $repository = YoastSEO()->classes->get( 'Yoast\WP\SEO\Repositories\Indexable_Repository' );
        $builder    = YoastSEO()->classes->get( 'Yoast\WP\SEO\Builders\Indexable_Builder' );
        $indexable  = $repository->find_by_id_and_type( $post_id, 'post', false );
        $builder->build_for_id_and_type( $post_id, 'post', $indexable );
    } catch ( \Throwable $e ) {
        clean_post_cache( $post_id );
    }
}

// ── GET /tpa/v1/ajanlatok – Lista ──────────────────────────────────────────────
function tpa_api_list( WP_REST_Request $req ) {
    $per_page  = (int) $req->get_param( 'per_page' );
    $page      = (int) $req->get_param( 'page' );
    $search    = sanitize_text_field( $req->get_param( 'search' ) );
    $status    = $req->get_param( 'status' );
    $kategoria = sanitize_text_field( $req->get_param( 'kategoria' ) );
    $uticel_id = (int) $req->get_param( 'uticel_id' );

    $args = array(
        'post_type'      => 'ajanlat',
        'post_status'    => $status === 'any' ? array( 'publish', 'draft' ) : $status,
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( $search ) $args['s'] = $search;

    if ( $kategoria ) {
        $args['tax_query'] = array( array(
            'taxonomy' => 'ajanlat_kategoria',
            'field'    => 'name',
            'terms'    => $kategoria,
        ) );
    }

    if ( $uticel_id ) {
        $args['meta_query'] = array( array(
            'key'   => 'tpa_uticel',
            'value' => (string) $uticel_id,
        ) );
    }

    $query = new WP_Query( $args );
    $items = array();
    foreach ( $query->posts as $post ) {
        $items[] = tpa_api_format( $post->ID );
    }

    return rest_ensure_response( array(
        'items'       => $items,
        'total'       => (int) $query->found_posts,
        'total_pages' => (int) $query->max_num_pages,
        'page'        => $page,
        'per_page'    => $per_page,
    ) );
}

// ── GET /tpa/v1/ajanlat/{id} ───────────────────────────────────────────────────
function tpa_api_get( WP_REST_Request $req ) {
    $id   = (int) $req->get_param( 'id' );
    $post = get_post( $id );

    if ( ! $post || $post->post_type !== 'ajanlat' ) {
        return new WP_Error( 'not_found', 'Ajánlat nem található', array( 'status' => 404 ) );
    }

    return rest_ensure_response( tpa_api_format( $id ) );
}

// ── POST /tpa/v1/ajanlat – Létrehozás ──────────────────────────────────────────
function tpa_api_create( WP_REST_Request $req ) {
    $post_id = wp_insert_post( array(
        'post_type'    => 'ajanlat',
        'post_title'   => $req->get_param( 'title' ),
        'post_content' => wp_kses_post( (string) $req->get_param( 'content' ) ),
        'post_status'  => $req->get_param( 'status' ) ?: 'publish',
    ), true );

    if ( is_wp_error( $post_id ) ) {
        return new WP_Error( 'insert_failed', $post_id->get_error_message(), array( 'status' => 500 ) );
    }

    tpa_api_save_fields( $post_id, $req );

    return rest_ensure_response( tpa_api_format( $post_id ) );
}

// ── PUT /tpa/v1/ajanlat/{id} – Frissítés ───────────────────────────────────────
function tpa_api_update( WP_REST_Request $req ) {
    $id   = (int) $req->get_param( 'id' );
    $post = get_post( $id );

    if ( ! $post || $post->post_type !== 'ajanlat' ) {
        return new WP_Error( 'not_found', 'Ajánlat nem található', array( 'status' => 404 ) );
    }

    $update = array( 'ID' => $id );
    if ( $req->get_param( 'title' )   !== null ) $update['post_title']   = sanitize_text_field( $req->get_param( 'title' ) );
    if ( $req->get_param( 'content' ) !== null ) $update['post_content'] = wp_kses_post( $req->get_param( 'content' ) );
    if ( $req->get_param( 'status' )  !== null ) {
        $status               = $req->get_param( 'status' );
        $update['post_status'] = in_array( $status, array( 'publish', 'draft' ), true ) ? $status : $post->post_status;
    }

    wp_update_post( $update );
    tpa_api_save_fields( $id, $req );

    return rest_ensure_response( tpa_api_format( $id ) );
}

// ── Közös: kép letöltése URL-ből és sideload a média könyvtárba ───────────────
// Visszaad: attachment_id (int) vagy WP_Error. Használja a kiemelt kép ÉS a
// galéria sideload is – innen nincs mit duplikálni köztük.
function tpa_download_and_sideload( $post_id, $url ) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url( $url, 30 );
    if ( is_wp_error( $tmp ) ) {
        return new WP_Error( 'download_failed', 'Kép letöltése sikertelen: ' . $tmp->get_error_message(), array( 'status' => 500 ) );
    }

    $file_name = basename( parse_url( $url, PHP_URL_PATH ) );
    if ( ! pathinfo( $file_name, PATHINFO_EXTENSION ) ) $file_name .= '.jpg';
    $file_name = sanitize_file_name( $file_name );

    $attachment_id = media_handle_sideload( array( 'name' => $file_name, 'tmp_name' => $tmp ), $post_id );

    if ( file_exists( $tmp ) ) @unlink( $tmp );

    if ( is_wp_error( $attachment_id ) ) {
        return new WP_Error( 'sideload_failed', 'Importálás sikertelen: ' . $attachment_id->get_error_message(), array( 'status' => 500 ) );
    }

    return (int) $attachment_id;
}

// ── Galéria: nyers ID-lista lekérése + URL-ekre feloldása ─────────────────────
function tpa_api_galeria_ids( $post_id ) {
    $ids = get_post_meta( $post_id, 'tpa_galeria_ids', true );
    return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
}

function tpa_api_galeria_urls( $ids ) {
    return array_values( array_filter( array_map( function( $id ) {
        return wp_get_attachment_image_url( $id, 'medium_large' ) ?: '';
    }, $ids ) ) );
}

// Strukturált galéria: minden kép id + rács-URL + teljes URL + felirat.
function tpa_api_galeria( $ids ) {
    return array_values( array_filter( array_map( function( $id ) {
        $url = wp_get_attachment_image_url( $id, 'medium_large' );
        if ( ! $url ) return null;
        return array(
            'id'       => (int) $id,
            'url'      => $url,
            'full_url' => wp_get_attachment_url( $id ) ?: $url,
            'caption'  => wp_get_attachment_caption( $id ) ?: '',
        );
    }, $ids ) ) );
}

// ── POST /tpa/v1/ajanlat/{id}/kep – Kiemelt kép sideload URL-ből ──────────────
function tpa_api_sideload_image( WP_REST_Request $req ) {
    $post_id = (int) $req->get_param( 'id' );
    $url     = esc_url_raw( $req->get_param( 'url' ) );

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'ajanlat' ) {
        return new WP_Error( 'not_found', 'Ajánlat nem található', array( 'status' => 404 ) );
    }
    if ( ! $url ) {
        return new WP_Error( 'no_url', 'URL megadása kötelező', array( 'status' => 400 ) );
    }

    $attachment_id = tpa_download_and_sideload( $post_id, $url );
    if ( is_wp_error( $attachment_id ) ) return $attachment_id;

    set_post_thumbnail( $post_id, $attachment_id );

    return rest_ensure_response( array(
        'attachment_id' => $attachment_id,
        'url'           => wp_get_attachment_image_url( $attachment_id, 'medium_large' ) ?: wp_get_attachment_url( $attachment_id ),
        'full_url'      => wp_get_attachment_url( $attachment_id ),
    ) );
}

// ── POST /tpa/v1/ajanlat/{id}/galeria – Galéria-kép hozzáadása URL-ből ────────
function tpa_api_galeria_add( WP_REST_Request $req ) {
    $post_id = (int) $req->get_param( 'id' );
    $url     = esc_url_raw( $req->get_param( 'url' ) );

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'ajanlat' ) {
        return new WP_Error( 'not_found', 'Ajánlat nem található', array( 'status' => 404 ) );
    }
    if ( ! $url ) {
        return new WP_Error( 'no_url', 'URL megadása kötelező', array( 'status' => 400 ) );
    }

    $attachment_id = tpa_download_and_sideload( $post_id, $url );
    if ( is_wp_error( $attachment_id ) ) return $attachment_id;

    $ids   = tpa_api_galeria_ids( $post_id );
    $ids[] = $attachment_id;
    update_post_meta( $post_id, 'tpa_galeria_ids', $ids );

    return rest_ensure_response( array(
        'galeria_ids'  => $ids,
        'galeria_urls' => tpa_api_galeria_urls( $ids ),
        'galeria'      => tpa_api_galeria( $ids ),
    ) );
}

// ── DELETE /tpa/v1/ajanlat/{id}/galeria/{kep_id} – Galéria-kép eltávolítása ───
// Csak a galéria-listából veszi ki (nem törli magát a médiatár-elemet – az
// máshol is használatban lehet).
function tpa_api_galeria_remove( WP_REST_Request $req ) {
    $post_id = (int) $req->get_param( 'id' );
    $kep_id  = (int) $req->get_param( 'kep_id' );

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'ajanlat' ) {
        return new WP_Error( 'not_found', 'Ajánlat nem található', array( 'status' => 404 ) );
    }

    $ids = array_values( array_diff( tpa_api_galeria_ids( $post_id ), array( $kep_id ) ) );
    update_post_meta( $post_id, 'tpa_galeria_ids', $ids );

    return rest_ensure_response( array(
        'galeria_ids'  => $ids,
        'galeria_urls' => tpa_api_galeria_urls( $ids ),
        'galeria'      => tpa_api_galeria( $ids ),
    ) );
}

// ── POST /tpa/v1/ajanlat/{id}/galeria/{kep_id} – Galéria-kép feliratának mentése ─
// A felirat a WP-natív attachment-feliratba (post_excerpt) kerül.
function tpa_api_galeria_caption( WP_REST_Request $req ) {
    $post_id = (int) $req->get_param( 'id' );
    $kep_id  = (int) $req->get_param( 'kep_id' );
    $caption = (string) $req->get_param( 'caption' );

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'ajanlat' ) {
        return new WP_Error( 'not_found', 'Ajánlat nem található', array( 'status' => 404 ) );
    }
    if ( ! in_array( $kep_id, tpa_api_galeria_ids( $post_id ), true ) ) {
        return new WP_Error( 'not_in_galeria', 'A kép nem tartozik ehhez az ajánlathoz', array( 'status' => 404 ) );
    }

    wp_update_post( array( 'ID' => $kep_id, 'post_excerpt' => $caption ) );

    $ids = tpa_api_galeria_ids( $post_id );
    return rest_ensure_response( array(
        'galeria_ids'  => $ids,
        'galeria_urls' => tpa_api_galeria_urls( $ids ),
        'galeria'      => tpa_api_galeria( $ids ),
    ) );
}

// ── GET /tpa/v1/meta – Kategóriák + Úticélok (Portál form-mezőkhöz) ───────────
function tpa_api_meta() {
    $terms      = get_terms( array( 'taxonomy' => 'ajanlat_kategoria', 'hide_empty' => false, 'orderby' => 'name' ) );
    $kategoriak = is_wp_error( $terms ) ? array() : array_map( function( $t ) {
        return array( 'id' => $t->term_id, 'name' => $t->name, 'count' => $t->count );
    }, $terms );

    $uticelok = array();
    if ( post_type_exists( 'uticel' ) ) {
        $posts = get_posts( array(
            'post_type'      => 'uticel',
            'post_status'    => array( 'publish', 'draft' ),
            'numberposts'    => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ) );
        foreach ( $posts as $p ) {
            $ancestors = array_reverse( get_post_ancestors( $p->ID ) );
            $label_reszek = array_map( 'get_the_title', $ancestors );
            $label_reszek[] = $p->post_title;
            $uticelok[] = array(
                'id'    => $p->ID,
                'title' => $p->post_title,
                'label' => implode( ' › ', $label_reszek ),
            );
        }
    }

    return rest_ensure_response( array(
        'kategoriak' => $kategoriak,
        'uticelok'   => $uticelok,
    ) );
}

// ── GET /tpa/v1/status – Státusz / ping ────────────────────────────────────────
function tpa_api_status() {
    return rest_ensure_response( array(
        'plugin'     => 'Travelpont Ajánlatok REST API',
        'version'    => TPA_VERSION,
        'endpoint'   => rest_url( 'tpa/v1/ajanlatok' ),
        'cpt_exists' => post_type_exists( 'ajanlat' ),
    ) );
}
