# Travelpont Ajánlatok plugin – dokumentáció

> Verzió: 1.0.0 · Az aktivbalaton.hu egyedi plugin-konvenciók mintájára
> (minta: `E:\aktivbalaton.hu\Saját pluginok\_AKTIV\balaton-szallasok`)
> SZABÁLY: minden módosításkor verziót emelünk a fő fájl fejlécében
> (cache-buster + követhetőség).

## Mit tud?

- **"Ajánlatok" menüpont** a WP adminban → repjegy+szállás kombók felvitele
  űrlapon (nem kell kódolni): célállomás, indulás, időpont, éjszakák, ár,
  érvényességi dátum, Kiwi.com deep link, szállás affiliate link.
- **`[travelpont_ajanlatok]` shortcode** → reszponzív kártyarács bárhová
  (Elementor Shortcode widget VAGY Gutenberg Shortcode blokk – téma-független).
- **Ajánlat aloldal** → minden ajánlatnak saját oldala van (kártya "Megnézem"
  gombja ide visz), rajta a két affiliate gomb + automatikus affiliate
  közzétételi szöveg.
- **Lejárat-kezelés**: az érvényességi dátum után az ajánlat magától eltűnik
  a listából; az aloldalán "lejárt" jelzés jelenik meg a gombok helyett.

## Shortcode használat

```
[travelpont_ajanlatok]
[travelpont_ajanlatok limit="3" kategoria="tengerpart"]
[travelpont_ajanlatok limit="-1" rendezes="ar_novekvo" oszlopok="4"]
```

| Paraméter | Alapérték | Lehetőségek |
|---|---|---|
| `limit` | 6 | szám, `-1` = összes |
| `kategoria` | (mind) | kategória slug, vesszővel több is: `tengerpart,egzotikus` |
| `rendezes` | `ujak` | `ujak`, `ar_novekvo`, `ar_csokkeno`, `lejarat` |
| `lejartak` | `nem` | `igen` = a lejártak is látszanak |
| `oszlopok` | 3 | 2, 3, 4 (széles képernyőn) |

## Fájlszerkezet

```
travelpont-ajanlatok/
├── travelpont-ajanlatok.php   ← fő fájl: konstansok, modul-betöltés, enqueue
├── includes/
│   ├── fields.php             ← ⭐ KÖZPONTI MEZŐ-DEFINÍCIÓK (bővítés itt!)
│   ├── cpt.php                ← "ajanlat" CPT + "ajanlat_kategoria" taxonómia
│   ├── meta-boxes.php         ← admin űrlap (a fields.php-ból épül, generikus)
│   ├── shortcodes.php         ← [travelpont_ajanlatok]
│   ├── single-display.php     ← ajánlat-doboz az aloldalon (the_content elé)
│   └── rest-api.php           ← CSONTVÁZ: csak /status ping (portál helye)
├── templates/
│   ├── lista-template.php     ← kártyarács
│   ├── card-template.php      ← egy kártya
│   └── single-content.php     ← aloldali ajánlat-doboz
└── assets/css/
    ├── frontend.css           ← kártya + aloldal (branding CSS-változókban!)
    └── admin.css              ← admin űrlap
```

## Hogyan bővítsd? (SEMMI SINCS BEÉGETVE)

### Új mező hozzáadása
1. `includes/fields.php` → `tpa_get_fields()` tömbjébe új bejegyzés, pl.:
   ```php
   'tpa_fok_szama' => array(
       'label'   => 'Utasok száma',
       'type'    => 'number',        // text | number | url | date | select | textarea
       'section' => 'utazas',        // melyik admin dobozba kerüljön
       'desc'    => 'Hány főre szól az ár.',
   ),
   ```
   → az admin űrlapon és a mentésben AUTOMATIKUSAN megjelenik.
2. Ha a kártyán/aloldalon is látszódjon: a sablonban kiírod:
   `tpa_mezo( get_the_ID(), 'tpa_fok_szama' )`

### Új admin szekció (meta box)
`fields.php` → `tpa_get_sections()` tömbjébe új sor.

### Hookok (kódból, akár másik pluginból)
- `tpa_fields`, `tpa_sections` – mezők/szekciók módosítása filterrel
- `tpa_lista_query_args` – a lista-lekérdezés módosítása
- `tpa_shortcode_defaults` – shortcode alapértékek
- `tpa_after_save_meta` – fut minden ajánlat-mentés után (portál-szinkron helye!)
- `tpa_single_doboz_vege` – extra tartalom az aloldali doboz aljára
- `tpa_rest_api_init` – későbbi REST endpointok regisztrálása
- `tpa_affiliate_kozzetetel_szoveg`, `tpa_ures_lista_szoveg` – szövegek átírása

### Branding / színek
`assets/css/frontend.css` tetején CSS-változók (`--tpa-primary`,
`--tpa-accent`…) – a végleges arculatnál csak ezeket kell átírni.

## Portál-kommunikáció (MÉG NINCS MEGÉPÍTVE – csak a helye van)

`includes/rest-api.php` – jelenleg csak a publikus
`GET /wp-json/tpa/v1/status` ping él. A tervezett CRUD endpointok listája és
az auth-minta (Application Password + `publish_posts`) kommentben ott van,
az aktivbalaton `bsza/v1` mintáját követve.

## Telepítés

1. A `travelpont-ajanlatok` mappát felmásolni ide:
   `wp-content/plugins/`
2. WP admin → Bővítmények → "Travelpont Ajánlatok" → Bekapcsolás
3. (Aktiváláskor a permalink szabályok automatikusan frissülnek.)
4. Ajánlatok → Új ajánlat → mezők kitöltése + kiemelt kép beállítása
5. A kezdőlapra: `[travelpont_ajanlatok]` shortcode beszúrása
