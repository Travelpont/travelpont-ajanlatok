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
        'utazas'  => array( 'title' => '🧳 Utazás adatai',      'context' => 'normal', 'priority' => 'high' ),
        'szallas' => array( 'title' => '🏨 Szállás adatai',     'context' => 'normal', 'priority' => 'high' ),
        'ar'      => array( 'title' => '💰 Ár és érvényesség',   'context' => 'normal', 'priority' => 'high' ),
        'linkek'  => array( 'title' => '🔗 Affiliate linkek',    'context' => 'normal', 'priority' => 'high' ),
    );
    return apply_filters( 'tpa_sections', $sections );
}

// ── Mezők ─────────────────────────────────────────────────────────────────────
function tpa_get_fields() {
    $fields = array(

        // 🧳 Utazás adatai
        'tpa_ajanlat_tipus' => array(
            'label'   => 'Ajánlat típusa',
            'type'    => 'select',
            'section' => 'utazas',
            'options' => array(
                'repulo_szallas' => '✈️ Repülő + szállás',
                'busz_szallas'   => '🚌 Busz (Flixbus) + szállás',
                'csak_szallas'   => '🏨 Csak szállás (egyéni utazás)',
            ),
            'default' => 'repulo_szallas',
            'desc'    => 'Ez szabja meg, mely mezők (repjegy/busz link és ár) jelennek meg lent.',
        ),
        'tpa_celallomas' => array(
            'label'       => 'Célállomás',
            'type'        => 'text',
            'section'     => 'utazas',
            'placeholder' => 'pl. Amalfi-part, Olaszország',
            'desc'        => 'A kártyán megjelenő úti cél neve.',
        ),
        'tpa_indulas' => array(
            'label'       => 'Indulás helye (város)',
            'type'        => 'text',
            'section'     => 'utazas',
            'placeholder' => 'pl. Budapest',
            'desc'        => 'Honnan indul az utazás – VÁROSNÉV, ne reptérkód. Repülős ajánlatnál a Portál a reptér kiválasztásakor automatikusan kitölti.',
        ),
        'tpa_indulas_iata' => array(
            'label'         => 'Indulási reptér (IATA-kód)',
            'type'          => 'text',
            'section'       => 'utazas',
            'placeholder'   => 'pl. BUD',
            'desc'          => 'A kártyán az útvonal reptérkódokkal jelenik meg (BUD → PVK), alatta kisbetűvel a városok. A Portálon legördülőből választható.',
            'show_if_tipus' => array( 'repulo_szallas' ),
        ),
        'tpa_cel_iata' => array(
            'label'         => 'Érkezési reptér (IATA-kód)',
            'type'          => 'text',
            'section'       => 'utazas',
            'placeholder'   => 'pl. PVK',
            'show_if_tipus' => array( 'repulo_szallas' ),
        ),
        'tpa_cel_varos' => array(
            'label'         => 'Érkezési reptér városa',
            'type'          => 'text',
            'section'       => 'utazas',
            'placeholder'   => 'pl. Preveza (Lefkada)',
            'desc'          => 'A Portál a reptér kiválasztásakor automatikusan kitölti.',
            'show_if_tipus' => array( 'repulo_szallas' ),
        ),
        'tpa_indulas_datum' => array(
            'label'   => 'Odaút / érkezés dátuma',
            'type'    => 'date',
            'section' => 'utazas',
            'desc'    => 'Az utazás első napja (repülő/busz indulása, ill. szállásnál az érkezés). Ebből + a hazaút dátumából automatikusan számoljuk a kiírt időpontot és az éjszakák számát.',
        ),
        'tpa_hazaut_datum' => array(
            'label'   => 'Hazaút / távozás dátuma',
            'type'    => 'date',
            'section' => 'utazas',
            'desc'    => 'Az utazás utolsó napja (visszaút, ill. szállásnál a távozás napja).',
        ),
        'tpa_idopont' => array(
            'label'       => 'Utazás időpontja (csak ha nincs pontos dátum)',
            'type'        => 'text',
            'section'     => 'utazas',
            'placeholder' => 'pl. 2026. szeptember 10–14.',
            'desc'        => 'Csak akkor töltsd ki, ha nincsenek pontos dátumok – ha a fenti két dátum meg van adva, azokból képezzük a kiírt időpontot, és ezt a mezőt figyelmen kívül hagyjuk.',
        ),
        'tpa_ejszakak' => array(
            'label'       => 'Éjszakák száma (csak ha nincs pontos dátum)',
            'type'        => 'number',
            'section'     => 'utazas',
            'placeholder' => 'pl. 4',
            'desc'        => 'Csak akkor kell, ha nincsenek pontos dátumok – dátumokból automatikusan számoljuk.',
        ),
        'tpa_uticel' => array(
            'label'     => 'Úticél',
            'type'      => 'post_select',
            'section'   => 'utazas',
            'post_type' => 'uticel',
            'desc'      => 'Melyik Úticél oldalhoz (ország / tájegység / város) tartozzon ez az ajánlat? Az ajánlat automatikusan megjelenik a kiválasztott Úticél oldalán.',
        ),
        'tpa_miert_szuper' => array(
            'label'       => 'Miért szuper ez az ajánlat? (soronként egy pont)',
            'type'        => 'textarea',
            'section'     => 'utazas',
            'placeholder' => "közvetlen járat Budapestről\na strand 200 méterre\nszeptemberben még 26 fok",
            'desc'        => '2-4 rövid, személyes érv – az aloldalon pipás listaként jelenik meg. Ez a Travelpont hangja: miért pont ezt vadásztuk le neked.',
        ),

        // 🏨 Szállás adatai
        'tpa_szallas_nev' => array(
            'label'       => 'Szállás neve',
            'type'        => 'text',
            'section'     => 'szallas',
            'placeholder' => 'pl. Emelias Residences',
            'desc'        => 'A hotel/apartman neve, ahogy a foglalási oldalon szerepel – a kártyán és az aloldalon is megjelenik.',
        ),
        'tpa_szallas_csillag' => array(
            'label'   => 'Csillagbesorolás',
            'type'    => 'select',
            'section' => 'szallas',
            'options' => array(
                ''  => '— nincs megadva —',
                '1' => '1 csillagos (★)',
                '2' => '2 csillagos (★★)',
                '3' => '3 csillagos (★★★)',
                '4' => '4 csillagos (★★★★)',
                '5' => '5 csillagos (★★★★★)',
            ),
            'desc'    => 'A szállás hivatalos kategóriája. Apartmannál/magánszállásnál hagyd üresen.',
        ),
        'tpa_szallas_ellatas' => array(
            'label'   => 'Ellátás',
            'type'    => 'select',
            'section' => 'szallas',
            'options' => array(
                ''              => '— nincs megadva —',
                'onellatas'     => 'Önellátás',
                'reggeli'       => 'Reggelivel',
                'felpanzio'     => 'Félpanzió (reggeli + vacsora)',
                'teljes_panzio' => 'Teljes panzió',
                'all_inclusive' => 'All inclusive',
            ),
            'desc'    => 'Mit tartalmaz az ár étkezésből – a vendég döntéséhez kulcsadat.',
        ),

        // 💰 Ár és érvényesség
        'tpa_repjegy_ar' => array(
            'label'         => 'Repjegy ára (Ft/fő, oda-vissza)',
            'type'          => 'number',
            'section'       => 'ar',
            'placeholder'   => 'pl. 39450',
            'desc'          => 'Csak szám, tagolás nélkül, EGY FŐRE. Az összesen árhoz a „Hány főre szól” mezővel szorozzuk.',
            'show_if_tipus' => array( 'repulo_szallas' ),
        ),
        'tpa_busz_ar' => array(
            'label'         => 'Buszjegy ára (Ft/fő, oda-vissza)',
            'type'          => 'number',
            'section'       => 'ar',
            'placeholder'   => 'pl. 12450',
            'desc'          => 'Csak szám, tagolás nélkül, EGY FŐRE. Az összesen árhoz a „Hány főre szól” mezővel szorozzuk.',
            'show_if_tipus' => array( 'busz_szallas' ),
        ),
        'tpa_szallas_ar' => array(
            'label'       => 'Szállás ára (Ft, összesen)',
            'type'        => 'number',
            'section'     => 'ar',
            'placeholder' => 'pl. 112000',
            'desc'        => 'Csak szám, tagolás nélkül – a TELJES csomagra (nem főnkénti ár).',
        ),
        'tpa_fo_szam' => array(
            'label'       => 'Hány főre szól',
            'type'        => 'number',
            'section'     => 'ar',
            'default'     => '2',
            'placeholder' => '2',
            'desc'        => 'A csomagár ennyi főre értendő – a repjegy/buszjegy főnkénti árát ezzel szorozzuk az összesen sorban.',
        ),
        'tpa_ar' => array(
            'label'       => 'Ár (Ft) – összesített',
            'type'        => 'number',
            'section'     => 'ar',
            'placeholder' => 'pl. 290900',
            'desc'        => 'Az ajánlat aloldalán megjelenő teljes ár. Ha üresen hagyod, a repjegy + szállás ár összegét használjuk.',
        ),
        'tpa_ar_tol' => array(
            'label'   => 'Ár kiírása',
            'type'    => 'select',
            'section' => 'ar',
            'options' => array(
                'tol'    => '„Ft-tól” ár (alapértelmezett – az árak változhatnak)',
                'pontos' => 'Pontos ár (fix áras deal)',
            ),
            'default' => 'tol',
            'desc'    => 'Alapból minden összár „Ft-tól” toldalékot kap (az árak és a szállás-opciók folyamatosan változnak – így le vagyunk védve). Csak akkor válts pontosra, ha tényleg fix áras a találat.',
        ),
        'tpa_ar_megjegyzes' => array(
            'label'       => 'Ár megjegyzés',
            'type'        => 'text',
            'section'     => 'ar',
            'placeholder' => 'üresen: típus szerinti alapszöveg (pl. „2 fő, repülőjeggyel együtt”)',
            'desc'        => 'Az ár alatt megjelenő apróbetűs szöveg. Üresen hagyva az ajánlat típusához illő alapszöveget írjuk ki (2 fős csomagár-értelmezés).',
        ),
        'tpa_poggyasz' => array(
            'label'         => 'Poggyász az árban',
            'type'          => 'select',
            'section'       => 'ar',
            'options'       => array(
                ''            => '— nincs megadva —',
                'kis_kezi'    => 'Kis kézipoggyász (ülés alá férő)',
                'kezi'        => 'Kézipoggyász (fedélzeti táska/trolley)',
                'feladott'    => 'Kézipoggyász + feladott bőrönd',
            ),
            'desc'          => 'Mit tartalmaz a repjegy ára poggyászból. A fapados árak jellemzően csak kis kézipoggyászt tartalmaznak – ezt őszintén jelezzük a vendégnek.',
            'show_if_tipus' => array( 'repulo_szallas' ),
        ),
        'tpa_ervenyes' => array(
            'label'       => 'Ajánlat érvényes eddig',
            'type'        => 'date',
            'section'     => 'ar',
            'desc'        => 'A dátum után az ajánlat automatikusan eltűnik a listából. Üresen hagyva soha nem jár le.',
        ),
        'tpa_statusz' => array(
            'label'   => 'Deal státusza',
            'type'    => 'select',
            'section' => 'ar',
            'options' => array(
                'aktiv'  => 'Aktív',
                'lejart' => 'Lejárt',
            ),
            'default' => 'aktiv',
            'desc'    => 'Lejártra állítva az ajánlat NEM tűnik el: a kártyán halványított árakkal és „a jó árak visszatérnek” üzenettel jelenik meg.',
        ),
        'tpa_talalat_datuma' => array(
            'label'    => 'Találat dátuma',
            'type'     => 'date',
            'section'  => 'ar',
            'readonly' => true,
            'desc'     => 'Automatikus: az első publikáláskor az aznapi dátum mentődik – kézzel nem szerkeszthető. Ettől számítjuk a kártyán az ár-frissesség figyelmeztetést.',
        ),

        // 🔗 Affiliate linkek
        'tpa_kiwi_link' => array(
            'label'         => 'Repjegy affiliate link (Kiwi deep link)',
            'type'          => 'url',
            'section'       => 'linkek',
            'placeholder'   => 'https://c111.travelpayouts.com/click?shmarker=...',
            'desc'          => 'A Travelpayouts-ba csomagolt követő link (NEM a sima kiwi.com link!).',
            'show_if_tipus' => array( 'repulo_szallas' ),
        ),
        'tpa_busz_link' => array(
            'label'         => 'Flixbus / busz link',
            'type'          => 'url',
            'section'       => 'linkek',
            'placeholder'   => 'https://www.flixbus.hu/...',
            'desc'          => 'A busztársaság (pl. Flixbus) affiliate vagy közvetlen linkje.',
            'show_if_tipus' => array( 'busz_szallas' ),
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

// ── "-tól" áras-e az ajánlat? (alapértelmezés: IGEN – az árak változnak) ─────
// A tpa_ar_tol mező 'pontos' értéke kapcsolja ki; üres/hiányzó meta = "-tól",
// így a mező bevezetése előtti ajánlatok is automatikusan "-tól" árat írnak.
function tpa_ar_tol_e( $post_id ) {
    return tpa_mezo( $post_id, 'tpa_ar_tol' ) !== 'pontos';
}

// ── Az ÖSSZÁR kiírása: "562 275 Ft-tól" vagy (fix árnál) "562 275 Ft" ────────
// Csak az összesen árra való – a részár-sorok (repjegy/szállás) pontos,
// talált árak maradnak, azokat az "Árak ellenőrizve" dátum fedi.
function tpa_osszeg_format( $post_id, $ar ) {
    if ( $ar === '' || $ar === null ) return '';
    return tpa_ar_format( $ar ) . ( tpa_ar_tol_e( $post_id ) ? '-tól' : '' );
}

// ── Hány főre szól az ajánlat (default 2, minimum 1) ─────────────────────────
function tpa_fo_szam( $post_id ) {
    return max( 1, (int) tpa_mezo( $post_id, 'tpa_fo_szam' ) );
}

// ── Az utazási költség (repjegy VAGY buszjegy) FŐNKÉNTI ára + címkéje ─────────
// TÍPUSFÜGGŐ: csak az ajánlat típusához tartozó mező számít (repülős → repjegy,
// buszos → buszjegy, csak szállás → nincs) – a típusváltás után bent ragadt
// régi részár így nem torzít. Visszaad: array( 'ar' => '39450'|'', 'cimke' => 'Repjegy'|'Buszjegy'|'' )
function tpa_utazas_ar_fo( $post_id ) {
    $tipus = tpa_mezo( $post_id, 'tpa_ajanlat_tipus' );
    if ( $tipus === 'csak_szallas' ) {
        return array( 'ar' => '', 'cimke' => '' );
    }
    if ( $tipus === 'busz_szallas' ) {
        return array( 'ar' => tpa_mezo( $post_id, 'tpa_busz_ar' ), 'cimke' => 'Buszjegy' );
    }
    // repulo_szallas + régi, típus nélküli ajánlatok
    return array( 'ar' => tpa_mezo( $post_id, 'tpa_repjegy_ar' ), 'cimke' => 'Repjegy' );
}

// ── Teljes ár: a kézzel beírt "tpa_ar", vagy ha az üres, a részárak összege ───
// Képlet: (főnkénti utazási ár × fő-szám) + szállás ár (a szállás ár már a
// teljes csomagra értendő). A repjegy/buszjegy mező FŐNKÉNTI árat tárol.
function tpa_teljes_ar( $post_id ) {
    $ar = tpa_mezo( $post_id, 'tpa_ar' );
    if ( $ar !== '' ) return $ar;

    $utazas  = tpa_utazas_ar_fo( $post_id );
    $szallas = tpa_mezo( $post_id, 'tpa_szallas_ar' );

    if ( $utazas['ar'] === '' && $szallas === '' ) return '';

    return (string) ( (float) $utazas['ar'] * tpa_fo_szam( $post_id ) + (float) $szallas );
}

// ── Számított ár eltárolása meta-ként (ár szerinti rendezéshez) ───────────────
// Minden mentés után frissül (admin mentés és REST is a tpa_after_save_meta
// hookot süti el), így a lista "ar_novekvo/ar_csokkeno" rendezése azoknál az
// ajánlatoknál is működik, ahol a tpa_ar üres és az ár a részárakból adódik.
function tpa_ar_szamitott_frissit( $post_id ) {
    update_post_meta( $post_id, 'tpa_ar_szamitott', tpa_teljes_ar( $post_id ) );
}
add_action( 'tpa_after_save_meta', 'tpa_ar_szamitott_frissit' );

// ── Dátum magyar formázása: "2026-07-22" → "2026. július 22." ─────────────────
function tpa_datum_magyar( $iso, $format = 'Y. F j.' ) {
    $ts = $iso ? strtotime( $iso ) : false;
    return $ts ? date_i18n( $format, $ts ) : '';
}

// ── Rövid magyar dátum: "2026-07-14" → "júl. 14." ─────────────────────────────
// A WP hu_HU 'M' formátuma pont nélkül rövidít ("júl"), a magyar helyesírás
// szerint viszont pont kell a rövidítés után – ezért saját lista.
function tpa_datum_magyar_rovid( $iso ) {
    $ts = $iso ? strtotime( $iso ) : false;
    if ( ! $ts ) return '';
    $honapok = array( 1 => 'jan.', 'febr.', 'márc.', 'ápr.', 'máj.', 'jún.', 'júl.', 'aug.', 'szept.', 'okt.', 'nov.', 'dec.' );
    return $honapok[ (int) gmdate( 'n', $ts ) ] . ' ' . (int) gmdate( 'j', $ts ) . '.';
}

// ── Éjszakák száma: a dátumokból számolva, vagy (ha nincsenek) a kézi mezőből ─
function tpa_ejszakak_szam( $post_id ) {
    $indul = tpa_mezo( $post_id, 'tpa_indulas_datum' );
    $haza  = tpa_mezo( $post_id, 'tpa_hazaut_datum' );
    if ( $indul && $haza ) {
        $diff = (int) floor( ( strtotime( $haza ) - strtotime( $indul ) ) / DAY_IN_SECONDS );
        if ( $diff > 0 ) return (string) $diff;
    }
    return (string) tpa_mezo( $post_id, 'tpa_ejszakak' );
}

// ── Az utazás időpontjának kiírása ────────────────────────────────────────────
// A dátummezőkből képzett magyar tartomány ("2026. szeptember 20–27." /
// "2026. szeptember 28. – október 2." / évhatáron át teljes dátumokkal);
// ha nincsenek dátumok, a kézi tpa_idopont szöveg.
function tpa_idopont_megjelenites( $post_id ) {
    $indul = tpa_mezo( $post_id, 'tpa_indulas_datum' );
    $haza  = tpa_mezo( $post_id, 'tpa_hazaut_datum' );
    $i = $indul ? strtotime( $indul ) : false;
    $h = $haza  ? strtotime( $haza )  : false;

    if ( $i && $h && $h > $i ) {
        if ( gmdate( 'Y-m', $i ) === gmdate( 'Y-m', $h ) ) {
            return date_i18n( 'Y. F j', $i ) . '–' . date_i18n( 'j', $h ) . '.';
        }
        if ( gmdate( 'Y', $i ) === gmdate( 'Y', $h ) ) {
            return date_i18n( 'Y. F j', $i ) . '. – ' . date_i18n( 'F j', $h ) . '.';
        }
        return date_i18n( 'Y. F j', $i ) . '. – ' . date_i18n( 'Y. F j', $h ) . '.';
    }

    // Kézi szöveg – tipikus elütés javítása kiíráskor: "2026.augusztus" →
    // "2026. augusztus" (a mentett érték változatlan marad).
    return preg_replace( '/^(\d{4})\.(?=\S)/u', '$1. ', tpa_mezo( $post_id, 'tpa_idopont' ) );
}

// ── Ár-megjegyzés kiírása: kézi szöveg, vagy típus szerinti alapszöveg ────────
// Kanonikus ár-értelmezés: 2 fős csomagár (a Kiwi/Booking linkek is 2 főre
// szólnak) – ezért az alapszövegek mind a 2 főt nevesítik.
function tpa_ar_megjegyzes_megjelenites( $post_id ) {
    $sajat = tpa_mezo( $post_id, 'tpa_ar_megjegyzes' );
    if ( $sajat !== '' ) return $sajat;

    $tipus = tpa_mezo( $post_id, 'tpa_ajanlat_tipus' );
    $fo    = tpa_fo_szam( $post_id );
    $alap  = apply_filters( 'tpa_ar_megjegyzes_alapok', array(
        'repulo_szallas' => $fo . ' fő, repülőjeggyel együtt',
        'busz_szallas'   => $fo . ' fő, buszjeggyel együtt',
        'csak_szallas'   => $fo . ' fő részére',
    ), $fo );
    if ( isset( $alap[ $tipus ] ) ) return $alap[ $tipus ];
    return isset( $alap['repulo_szallas'] ) ? $alap['repulo_szallas'] : '';
}

// ── "Mit tartalmaz az ár" tájékoztató sor (típus szerint, filterrel átírható) ─
// Repülősnél a poggyász-mező is beleszól: ha a feladott bőrönd benne van az
// árban, nem állítjuk az ellenkezőjét.
function tpa_ar_tartalom_szoveg( $post_id ) {
    $tipus = tpa_mezo( $post_id, 'tpa_ajanlat_tipus' );

    if ( $tipus === 'busz_szallas' ) {
        $szoveg = 'Az ár a buszjegyet és a szállást tartalmazza.';
    } elseif ( $tipus === 'csak_szallas' ) {
        $szoveg = 'Az ár a szállást tartalmazza – az odautazás egyénileg szervezendő.';
    } else {
        $szoveg = ( tpa_mezo( $post_id, 'tpa_poggyasz' ) === 'feladott' )
            ? 'Az ár a repülőjegyet és a szállást tartalmazza – reptéri transzfert nem.'
            : 'Az ár a repülőjegyet és a szállást tartalmazza – feladott poggyászt és reptéri transzfert nem.';
    }

    return apply_filters( 'tpa_ar_tartalom_szoveg', $szoveg, $post_id, $tipus );
}

// ── Poggyász megnevezése (a select-opció felirata) ────────────────────────────
function tpa_poggyasz_nev( $post_id ) {
    $ertek = tpa_mezo( $post_id, 'tpa_poggyasz' );
    if ( $ertek === '' ) return '';
    $fields  = tpa_get_fields();
    $options = isset( $fields['tpa_poggyasz']['options'] ) ? $fields['tpa_poggyasz']['options'] : array();
    return isset( $options[ $ertek ] ) ? $options[ $ertek ] : '';
}

// ── "Miért szuper?" pontok: a textarea sorai tömbként (üres sorok kiszűrve) ───
function tpa_miert_szuper_pontok( $post_id ) {
    $nyers = tpa_mezo( $post_id, 'tpa_miert_szuper' );
    if ( $nyers === '' ) return array();
    $sorok = array_map( 'trim', preg_split( '/\r\n|\r|\n/', $nyers ) );
    return array_values( array_filter( $sorok, 'strlen' ) );
}

// ── Közös meta_query: a le nem járt ajánlatok szűrője ─────────────────────────
// (a shortcode-lista és az aloldali "hasonló ajánlatok" is ezt használja)
function tpa_nem_lejart_meta_query() {
    return array(
        'relation' => 'OR',
        array( 'key' => 'tpa_ervenyes', 'value' => current_time( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
        array( 'key' => 'tpa_ervenyes', 'compare' => 'NOT EXISTS' ),
        array( 'key' => 'tpa_ervenyes', 'value' => '', 'compare' => '=' ),
    );
}

// ── Repülős útvonal: reptérkódok + városok ────────────────────────────────────
// null, ha nincs mindkét IATA-kód kitöltve. Különben:
//   array( 'kod' => 'BUD → PVK', 'varos' => 'Budapest → Preveza (Lefkada)' )
// (a 'varos' üres string is lehet, ha a városnevek nincsenek kitöltve)
function tpa_utvonal( $post_id ) {
    $indulas_iata = strtoupper( trim( tpa_mezo( $post_id, 'tpa_indulas_iata' ) ) );
    $cel_iata     = strtoupper( trim( tpa_mezo( $post_id, 'tpa_cel_iata' ) ) );
    if ( $indulas_iata === '' || $cel_iata === '' ) return null;

    $indulas_varos = tpa_mezo( $post_id, 'tpa_indulas' );
    $cel_varos     = tpa_mezo( $post_id, 'tpa_cel_varos' );
    if ( $indulas_varos !== '' && $cel_varos !== '' ) {
        $varos = $indulas_varos . ' → ' . $cel_varos;
    } else {
        $varos = $indulas_varos !== '' ? $indulas_varos : $cel_varos;
    }

    return array(
        'kod'   => $indulas_iata . ' → ' . $cel_iata,
        'varos' => $varos,
    );
}

// ── Szállás-csillagok HTML-je: "★★★★" (biztonságosan echózható) ──────────────
function tpa_szallas_csillag_html( $post_id ) {
    $db = (int) tpa_mezo( $post_id, 'tpa_szallas_csillag' );
    if ( $db < 1 || $db > 5 ) return '';
    return '<span class="tpa-csillagok" aria-label="' . esc_attr( $db . ' csillagos' ) . '">'
        . str_repeat( '★', $db ) . '</span>';
}

// ── Ellátás megnevezése (a select-opció felirata) ─────────────────────────────
function tpa_ellatas_nev( $post_id ) {
    $ertek = tpa_mezo( $post_id, 'tpa_szallas_ellatas' );
    if ( $ertek === '' ) return '';
    $fields  = tpa_get_fields();
    $options = isset( $fields['tpa_szallas_ellatas']['options'] ) ? $fields['tpa_szallas_ellatas']['options'] : array();
    return isset( $options[ $ertek ] ) ? $options[ $ertek ] : '';
}

// ── Összekötött úticél "morzsamenüje": Ország › Régió › Város ──────────────────
// A tpa_uticel mezőben tárolt úticél-ID ős-lánca + saját címe. Ugyanaz a minta,
// mint a REST /meta végponton (rest-api.php, tpa_api_meta()).
//   $args['linkelt'] = true → biztonságos HTML <a href> elemekből épül (aloldali
//     morzsamenühöz) – közvetlenül echózható.
//   különben NYERS szöveg, egymást › jellel elválasztva (a hívó felel az
//     escape-elésért, pl. esc_html; így REST/JSON-ban sincs dupla escape).
//   $args['elvalaszto'] felülírható (alapból ' › ').
function tpa_uticel_breadcrumb( $uticel_id, $args = array() ) {
    $uticel_id = absint( $uticel_id );
    if ( ! $uticel_id ) return '';

    $post = get_post( $uticel_id );
    if ( ! $post || $post->post_type !== 'uticel' ) return '';

    $linkelt    = ! empty( $args['linkelt'] );
    $elvalaszto = isset( $args['elvalaszto'] ) ? $args['elvalaszto'] : ' › ';

    $idk = array_reverse( get_post_ancestors( $uticel_id ) ); // ős-lánc: legfelső → közvetlen szülő
    $idk[] = $uticel_id;                                        // majd maga az úticél

    $reszek = array();
    foreach ( $idk as $id ) {
        $cim = get_the_title( $id );
        if ( $cim === '' ) continue;
        if ( $linkelt ) {
            $reszek[] = '<a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html( $cim ) . '</a>';
        } else {
            $reszek[] = $cim; // nyers – a hívó escapel
        }
    }

    return implode( $linkelt ? '<span class="tpa-morzsa-sep">' . esc_html( $elvalaszto ) . '</span>' : $elvalaszto, $reszek );
}

// ── Az ajánlat "hol" megjelenítése (kártya) ───────────────────────────────────
// Prioritás: a kézzel írt tpa_celallomas felülír; ha üres, az összekötött
// úticélból "Város, Ország" formátum (pl. "Barcelona, Spanyolország") – a
// teljes morzsalánc a kártyán zajos volt, az a single oldal morzsamenüjéé.
// Ország/régió szintű úticélnál csak a saját neve. Így a meglévő, kézi
// célállomású ajánlatok változatlanok, az új workflow-ban elég az úticélt
// bekötni. NYERS szöveget ad vissza – a hívó felel az escape-elésért.
function tpa_hely_megjelenites( $post_id ) {
    $celallomas = tpa_mezo( $post_id, 'tpa_celallomas' );
    if ( $celallomas !== '' ) return $celallomas;

    $uticel_id = absint( tpa_mezo( $post_id, 'tpa_uticel' ) );
    if ( ! $uticel_id ) return '';

    $uticel = get_post( $uticel_id );
    if ( ! $uticel || $uticel->post_type !== 'uticel' ) return '';

    $cim  = get_the_title( $uticel_id );
    $osok = get_post_ancestors( $uticel_id ); // közvetlen szülő → ... → legfelső ős
    if ( $osok ) {
        $orszag = get_the_title( end( $osok ) ); // a legfelső ős az ország
        if ( $orszag !== '' && $orszag !== $cim ) {
            return $cim . ', ' . $orszag;
        }
    }
    return $cim;
}

// ── Lejárt-e az ajánlat? ──────────────────────────────────────────────────────
function tpa_lejart( $post_id ) {
    $ervenyes = tpa_mezo( $post_id, 'tpa_ervenyes' );
    if ( ! $ervenyes ) return false; // nincs dátum = soha nem jár le
    return $ervenyes < current_time( 'Y-m-d' );
}

// ── Lejárt-e a DEAL? (kézi státusz VAGY érvényességi dátum) ───────────────────
// A tpa_statusz a kézi kapcsoló ("lejart" = az árak már nem élnek, de az
// ajánlat maradjon kint halványítva) – a tpa_ervenyes dátum-lejárat mellett
// ez is lejárt megjelenítést vált ki a kártyán és az aloldalon.
function tpa_deal_lejart( $post_id ) {
    return tpa_mezo( $post_id, 'tpa_statusz' ) === 'lejart' || tpa_lejart( $post_id );
}

// ── Régi-e már a találat? (frissesség-küszöb a beállításokból) ────────────────
// true, ha a találat dátuma a beállított küszöbnél (nap) régebbi – ilyenkor a
// kártyán ár-változás figyelmeztetés jelenik meg. Egyszerű dátum-összehasonlítás
// megjelenítéskor, nincs cron/API.
function tpa_talalat_regi( $post_id ) {
    $talalat = tpa_mezo( $post_id, 'tpa_talalat_datuma' );
    if ( ! $talalat ) return false;
    $eltelt_nap = ( strtotime( current_time( 'Y-m-d' ) ) - strtotime( $talalat ) ) / DAY_IN_SECONDS;
    return $eltelt_nap > tpa_frissesseg_kuszob();
}

// ── Találat dátuma: első publikáláskor automatikusan rögzül ───────────────────
// A transition_post_status az admin mentésre ÉS a Portál REST create-jére is
// lefut; a mező readonly, a save-utak kihagyják, így utólag nem íródik felül.
add_action( 'transition_post_status', function( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'ajanlat' ) return;
    if ( $new_status !== 'publish' || $old_status === 'publish' ) return;
    if ( get_post_meta( $post->ID, 'tpa_talalat_datuma', true ) !== '' ) return;
    update_post_meta( $post->ID, 'tpa_talalat_datuma', current_time( 'Y-m-d' ) );
}, 10, 3 );

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
