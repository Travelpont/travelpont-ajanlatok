<?php
/**
 * Travelpont Ajánlatok – Custom Post Type + taxonómia
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Custom Post Type: ajanlat ─────────────────────────────────────────────────
function tpa_register_cpt() {
    $labels = array(
        'name'               => 'Ajánlatok',
        'singular_name'      => 'Ajánlat',
        'menu_name'          => 'Ajánlatok',
        'add_new'            => 'Új ajánlat',
        'add_new_item'       => 'Új ajánlat hozzáadása',
        'edit_item'          => 'Ajánlat szerkesztése',
        'new_item'           => 'Új ajánlat',
        'view_item'          => 'Ajánlat megtekintése',
        'search_items'       => 'Ajánlatok keresése',
        'not_found'          => 'Nem található ajánlat',
        'not_found_in_trash' => 'Nincs ajánlat a kukában',
    );

    $args = array(
        'labels'          => $labels,
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'query_var'       => true,
        'rewrite'         => array( 'slug' => 'ajanlat' ),
        'capability_type' => 'post',
        'has_archive'     => false, // a listázást a shortcode adja
        'hierarchical'    => false,
        'menu_position'   => 25,
        'menu_icon'       => 'dashicons-airplane',
        'supports'        => array( 'title', 'editor', 'thumbnail' ),
        'show_in_rest'    => false, // klasszikus szerkesztő + saját REST namespace később
    );

    register_post_type( 'ajanlat', apply_filters( 'tpa_cpt_args', $args ) );
}
add_action( 'init', 'tpa_register_cpt' );

// ── Taxonómia: Úti cél kategória ──────────────────────────────────────────────
function tpa_register_kategoria_taxonomy() {
    register_taxonomy( 'ajanlat_kategoria', 'ajanlat', array(
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'uti-cel' ),
        'labels'            => array(
            'name'          => 'Kategóriák',
            'singular_name' => 'Kategória',
            'search_items'  => 'Kategória keresése',
            'all_items'     => 'Összes kategória',
            'edit_item'     => 'Kategória szerkesztése',
            'update_item'   => 'Kategória frissítése',
            'add_new_item'  => 'Új kategória hozzáadása',
            'new_item_name' => 'Új kategória neve',
            'menu_name'     => 'Kategóriák',
        ),
    ) );

    // Alapértelmezett kategóriák – szabadon átnevezhetők/törölhetők az adminban,
    // csak akkor jönnek létre újra, ha egyik sem létezik már.
    $defaults = apply_filters( 'tpa_default_kategoriak', array(
        'Tengerpart'      => 'tengerpart',
        'Városlátogatás'  => 'varoslatogatas',
        'Egzotikus'       => 'egzotikus',
        'Természet, túra' => 'termeszet-tura',
    ) );
    foreach ( $defaults as $name => $slug ) {
        if ( ! term_exists( $name, 'ajanlat_kategoria' ) && ! term_exists( $slug, 'ajanlat_kategoria' ) ) {
            wp_insert_term( $name, 'ajanlat_kategoria', array( 'slug' => $slug ) );
        }
    }
}
add_action( 'init', 'tpa_register_kategoria_taxonomy' );

// ── Admin lista: hasznos oszlopok (ár, érvényesség) ───────────────────────────
add_filter( 'manage_ajanlat_posts_columns', function( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['tpa_ar']       = 'Ár';
            $new['tpa_ervenyes'] = 'Érvényes eddig';
        }
    }
    return $new;
} );

add_action( 'manage_ajanlat_posts_custom_column', function( $column, $post_id ) {
    if ( $column === 'tpa_ar' ) {
        $ar = tpa_mezo( $post_id, 'tpa_ar' );
        echo $ar !== '' ? esc_html( tpa_ar_format( $ar ) ) : '—';
    }
    if ( $column === 'tpa_ervenyes' ) {
        $ervenyes = tpa_mezo( $post_id, 'tpa_ervenyes' );
        if ( ! $ervenyes ) {
            echo '—';
        } elseif ( tpa_lejart( $post_id ) ) {
            echo '<span style="color:#b91c1c;font-weight:600;">Lejárt (' . esc_html( $ervenyes ) . ')</span>';
        } else {
            echo esc_html( $ervenyes );
        }
    }
}, 10, 2 );
