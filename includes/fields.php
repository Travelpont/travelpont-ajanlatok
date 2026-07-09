<?php
/**
 * Travelpont Ajánlatok – KÖZPONTI MEZŐ-DEFINÍCIÓK
 *
 * EZ A PLUGIN LELKE: minden egyedi mező itt van definiálva, EGY helyen.
 * A meta boxok (admin űrlap), a mentés/sanitizálás és a sablonok is
 * ebből a listából dolgoznak.
 *
 * ÚJ MEZŐ HOZZÁADÁSA = egyetlen új bejegyzés ebbe a tömbbe.
 * Utána a mező automatikusan megjelenik az admin űrlapon és menthető lesz.
 * (A kártyán/aloldalon való megjelenítéshez a sablonban kell kiírni:
 *  tpa_mezo( get_the_ID(), 'tpa_uj_mezo' ) )
 *
 * Támogatott típusok: text, number, url, date, select, textarea, post_select
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Mező-szekciók (admin meta boxok) ──────────────────────────────────────────
function tpa_get_sections() {
    $sections = array(
        'utazas' => array( 'title' => '🧳 Utazás adatai',      'context' => 'normal', 'priority' => 'high' ),
        'ar'     => array( 'title' => '💰 Ár és érvényesség',   'context' => 'normal', 'priority' => 'high' ),
        'linkek' => array( 'title' => '🔗 Affiliate linkek',    'context' => 'normal', 'priority' => 'high' ),
    );
    return apply_filters( 'tpa_sections', $sections );
}

// ── Mezők ─────────────────────────────────────────────────────────────────────
function tpa_get_fields() {
    $fields = array(

        // 🧳 Utazás adatai
        'tpa_celallomas' => array(
            'label'       => 'Célállomás',
            'type'        => 'text',
            'section'     => 'utazas',
            'placeholder' => 'pl. Amalfi-part, Olaszország',
            'desc'        => 'A kártyán megjelenő úti cél neve.',
        ),
        'tpa_indulas' => array(
            'label'       => 'Indulás helye',
            'type'        => 'text',
            'section'     => 'utazas',
            'placeholder' => 'pl. Budapest',
            'desc'        => 'Honnan indul a járat (opcionális).',
        ),
        'tpa_idopont' => array(
            'label'       => 'Utazás időpontja',
            'type'        => 'text',
            'section'     => 'utazas',
            'placeholder' => 'pl. 2026. szeptember 10–14.',
            'desc'        => 'Szabad szöveg – úgy írd, ahogy a látogatónak jó olvasni.',
        ),
        'tpa_ejszakak' => array(
            'label'       => 'Éjszakák száma',
            'type'        => 'number',
            'section'     => 'utazas',
            'placeholder' => 'pl. 4',
        ),
        'tpa_uticel' => array(
            'label'     => 'Úticél',
            'type'      => 'post_select',
            'section'   => 'utazas',
            'post_type' => 'uticel',
            'desc'      => 'Melyik Úticél oldalhoz (ország / tájegység / város) tartozzon ez az ajánlat? Az ajánlat automatikusan megjelenik a kiválasztott Úticél oldalán.',
        ),

        // 💰 Ár és érvényesség
        'tpa_repjegy_ar' => array(
            'label'       => 'Repjegy ár (Ft, oda-vissza)',
            'type'        => 'number',
            'section'     => 'ar',
            'placeholder' => 'pl. 78900',
            'desc'        => 'Csak szám, tagolás nélkül. A kezdőlapi ajánlat-kártyán külön sorban jelenik meg.',
        ),
        'tpa_szallas_ar' => array(
            'label'       => 'Szállás ár (Ft, teljes tartózkodásra)',
            'type'        => 'number',
            'section'     => 'ar',
            'placeholder' => 'pl. 112000',
            'desc'        => 'Csak szám, tagolás nélkül. A kezdőlapi ajánlat-kártyán külön sorban jelenik meg.',
        ),
        'tpa_ar' => array(
            'label'       => 'Ár (Ft) – összesített',
            'type'        => 'number',
            'section'     => 'ar',
            'placeholder' => 'pl. 290900',
            'desc'        => 'Az ajánlat aloldalán megjelenő teljes ár. Ha üresen hagyod, a repjegy + szállás ár összegét használjuk.',
        ),
        'tpa_ar_megjegyzes' => array(
            'label'       => 'Ár megjegyzés',
            'type'        => 'text',
            'section'     => 'ar',
            'default'     => '2 fő, repülőjeggyel együtt',
            'desc'        => 'Az ár alatt megjelenő apróbetűs szöveg. Szabadon átírható.',
        ),
        'tpa_ervenyes' => array(
            'label'       => 'Ajánlat érvényes eddig',
            'type'        => 'date',
            'section'     => 'ar',
            'desc'        => 'A dátum után az ajánlat automatikusan eltűnik a listából. Üresen hagyva soha nem jár le.',
        ),

        // 🔗 Affiliate linkek
        'tpa_kiwi_link' => array(
            'label'       => 'Kiwi.com deep link',
            'type'        => 'url',
            'section'     => 'linkek',
            'placeholder' => 'https://c111.travelpayouts.com/click?shmarker=...',
            'desc'        => 'A Travelpayouts-ba csomagolt követő link (NEM a sima kiwi.com link!).',
        ),
        'tpa_szallas_link' => array(
            'label'       => 'Szállás affiliate link',
            'type'        => 'url',
            'section'     => 'linkek',
            'placeholder' => 'https://...',
            'desc'        => 'Szallas.hu vagy Booking.com partner link.',
        ),
        'tpa_szallas_platform' => array(
            'label'   => 'Szállás platform',
            'type'    => 'select',
            'section' => 'linkek',
            'options' => array(
                ''        => '— válassz —',
                'szallas' => 'Szallas.hu',
                'booking' => 'Booking.com',
                'egyeb'   => 'Egyéb',
            ),
            'desc'    => 'A szállás gomb feliratához használjuk.',
        ),
    );
    return apply_filters( 'tpa_fields', $fields );
}

// ── Mezőérték sanitizálása típus szerint (meta box mentés ÉS REST API közös) ──
function tpa_sanitize_field_value( $type, $raw, $field ) {
    switch ( $type ) {
        case 'number':
            return ( $raw === '' ) ? '' : (string) absint( $raw );
        case 'url':
            return esc_url_raw( $raw );
        case 'date':
            return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
        case 'select':
            $options = isset( $field['options'] ) ? $field['options'] : array();
            return array_key_exists( $raw, $options ) ? $raw : '';
        case 'post_select':
            $post_id_value = absint( $raw );
            return ( $post_id_value && get_post( $post_id_value ) ) ? (string) $post_id_value : '';
        case 'textarea':
            return sanitize_textarea_field( $raw );
        default:
            return sanitize_text_field( $raw );
    }
}

// ── Mezőérték lekérése (default-tal) ──────────────────────────────────────────
function tpa_mezo( $post_id, $key ) {
    $value  = get_post_meta( $post_id, $key, true );
    if ( $value !== '' && $value !== null ) return $value;

    $fields = tpa_get_fields();
    return isset( $fields[ $key ]['default'] ) ? $fields[ $key ]['default'] : '';
}

// ── Ár formázása: 290900 → "290 900 Ft" ───────────────────────────────────────
function tpa_ar_format( $ar ) {
    if ( $ar === '' || $ar === null ) return '';
    return number_format( (float) $ar, 0, ',', ' ' ) . ' Ft';
}

// ── Teljes ár: a kézzel beírt "tpa_ar", vagy ha az üres, a repjegy + szállás ár összege ──
function tpa_teljes_ar( $post_id ) {
    $ar = tpa_mezo( $post_id, 'tpa_ar' );
    if ( $ar !== '' ) return $ar;

    $repjegy = tpa_mezo( $post_id, 'tpa_repjegy_ar' );
    $szallas = tpa_mezo( $post_id, 'tpa_szallas_ar' );
    if ( $repjegy === '' && $szallas === '' ) return '';

    return (string) ( (float) $repjegy + (float) $szallas );
}

// ── Lejárt-e az ajánlat? ──────────────────────────────────────────────────────
function tpa_lejart( $post_id ) {
    $ervenyes = tpa_mezo( $post_id, 'tpa_ervenyes' );
    if ( ! $ervenyes ) return false; // nincs dátum = soha nem jár le
    return $ervenyes < current_time( 'Y-m-d' );
}

// ── Hány nap van még hátra? (null = nincs lejárat) ────────────────────────────
function tpa_hatralevo_napok( $post_id ) {
    $ervenyes = tpa_mezo( $post_id, 'tpa_ervenyes' );
    if ( ! $ervenyes ) return null;
    $diff = strtotime( $ervenyes ) - strtotime( current_time( 'Y-m-d' ) );
    return (int) floor( $diff / DAY_IN_SECONDS );
}

// ── Szállás platform → gombfelirat ────────────────────────────────────────────
function tpa_szallas_platform_nev( $post_id ) {
    $platform = tpa_mezo( $post_id, 'tpa_szallas_platform' );
    $nevek    = apply_filters( 'tpa_szallas_platform_nevek', array(
        'szallas' => 'Szallas.hu',
        'booking' => 'Booking.com',
        'egyeb'   => '',
    ) );
    return isset( $nevek[ $platform ] ) ? $nevek[ $platform ] : '';
}
