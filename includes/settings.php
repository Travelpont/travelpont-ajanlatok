<?php
/**
 * Travelpont Ajánlatok – Beállítások oldal (Ajánlatok menü → Ajánlat beállítások)
 *
 * Settings API-s aloldal egyetlen beállítással: a frissesség-küszöb napokban.
 * Ha a találat dátuma (tpa_talalat_datuma) ennél régebbi, a kártyán megjelenik
 * az ár-változás figyelmeztetés. Option név a travelpont_ prefixszel.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── A frissesség-küszöb lekérése (nap, default 3) ─────────────────────────────
function tpa_frissesseg_kuszob() {
    return max( 1, (int) get_option( 'travelpont_frissesseg_kuszob', 3 ) );
}

// ── Option + mező regisztrálása ───────────────────────────────────────────────
add_action( 'admin_init', function() {
    register_setting( 'travelpont_ajanlat_beallitasok', 'travelpont_frissesseg_kuszob', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 3,
    ) );

    add_settings_section(
        'tpa_frissesseg_szekcio',
        'Ár-frissesség',
        function() {
            echo '<p>A deal-kártyákon megjelenő ár-változás figyelmeztetés viselkedése. Nincs külső API-hívás: egyszerű dátum-összehasonlítás a találat dátumával, megjelenítéskor.</p>';
        },
        'tpa-beallitasok'
    );

    add_settings_field(
        'travelpont_frissesseg_kuszob',
        'Frissesség küszöb (nap)',
        function() {
            printf(
                '<input type="number" id="travelpont_frissesseg_kuszob" name="travelpont_frissesseg_kuszob" value="%s" min="1" step="1" class="small-text">
                <p class="description">Ha a találat dátuma ennyi napnál régebbi, a kártyán megjelenik: „Az ár azóta változhatott — a friss árat a foglalási linken látod”.</p>',
                esc_attr( tpa_frissesseg_kuszob() )
            );
        },
        'tpa-beallitasok',
        'tpa_frissesseg_szekcio',
        array( 'label_for' => 'travelpont_frissesseg_kuszob' )
    );
} );

// ── Aloldal az Ajánlatok CPT menüje alatt ─────────────────────────────────────
add_action( 'admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=ajanlat',
        'Ajánlat beállítások',
        'Ajánlat beállítások',
        'manage_options',
        'tpa-beallitasok',
        'tpa_render_beallitasok_oldal'
    );
} );

function tpa_render_beallitasok_oldal() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    ?>
    <div class="wrap">
        <h1>Ajánlat beállítások</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'travelpont_ajanlat_beallitasok' );
            do_settings_sections( 'tpa-beallitasok' );
            submit_button( 'Mentés' );
            ?>
        </form>
    </div>
    <?php
}
