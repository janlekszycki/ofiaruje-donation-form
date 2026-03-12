<?php
/**
 * Plugin Name:  Ofiaruje – Formularz Darowizn
 * Plugin URI:   https://ofiaruje.pl
 * Description:  Osadza formularz darowizny platformy Ofiaruje.pl na dowolnej stronie WordPress za pomocą shortcode [ofiaruje_formularz].
 * Version:      1.0.0
 * Author:       Ofiaruje.pl
 * Author URI:   https://ofiaruje.pl
 * Text Domain:  ofiaruje-donation-form
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

// ═══════════════════════════════════════════════════════════════
//  1.  ADMIN MENU & SETTINGS
// ═══════════════════════════════════════════════════════════════

add_action( 'admin_menu', 'ofiaruje_admin_menu' );
function ofiaruje_admin_menu() {
    add_options_page(
        'Ofiaruje – Formularz Darowizn',
        'Ofiaruje',
        'manage_options',
        'ofiaruje-donation-form',
        'ofiaruje_render_settings_page'
    );
}

add_action( 'admin_init', 'ofiaruje_register_settings' );
function ofiaruje_register_settings() {
    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_fundraiser_id',
        [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_preset_amounts',
        [
            'sanitize_callback' => 'ofiaruje_sanitize_preset_amounts',
            'default'           => '50,100,200,500',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_base_url',
        [
            'sanitize_callback' => 'esc_url_raw',
            'default'           => 'https://ofiaruje.pl',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_custom_css',
        [
            'sanitize_callback' => 'ofiaruje_sanitize_custom_css',
            'default'           => '',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_show_payment_icons',
        [
            'sanitize_callback' => 'ofiaruje_sanitize_toggle',
            'default'           => '1',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_utm_enabled',
        [
            'sanitize_callback' => 'ofiaruje_sanitize_toggle',
            'default'           => '0',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_utm_source',
        [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'wordpress',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_utm_medium',
        [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'plugin',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_utm_campaign',
        [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_utm_term',
        [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]
    );

    register_setting(
        'ofiaruje_settings_group',
        'ofiaruje_utm_content',
        [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]
    );
}

function ofiaruje_sanitize_toggle( $input ) {
    return (string) ( ! empty( $input ) ? '1' : '0' );
}

/**
 * Validate and sanitize comma-separated preset amounts.
 * Each value must be an integer >= 20.
 */
function ofiaruje_sanitize_preset_amounts( $input ) {
    $parts  = explode( ',', (string) $input );
    $clean  = [];
    foreach ( $parts as $part ) {
        $n = absint( trim( $part ) );
        if ( $n >= 20 ) {
            $clean[] = $n;
        }
    }
    if ( empty( $clean ) ) {
        add_settings_error(
            'ofiaruje_preset_amounts',
            'ofiaruje_preset_amounts_error',
            'Przynajmniej jedna kwota musi być podana i musi wynosić co najmniej 20 PLN. Przywrócono wartości domyślne.',
            'error'
        );
        return '50,100,200,500';
    }
    return implode( ',', $clean );
}

/**
 * Sanitize custom CSS entered by admin users.
 * Removes style-tag breaking sequences to avoid script injection.
 */
function ofiaruje_sanitize_custom_css( $input ) {
    $css = (string) $input;
    $css = preg_replace( '#</\s*style#i', '', $css );
    return trim( $css );
}

// ─────────────────────────────────────────────────────────────
//  Settings page HTML
// ─────────────────────────────────────────────────────────────

function ofiaruje_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $saved_custom_css = (string) get_option( 'ofiaruje_custom_css', '' );
    $default_css      = ofiaruje_default_inline_css();
    $textarea_css     = '' !== trim( $saved_custom_css )
        ? $saved_custom_css
        : $default_css;
    ?>
    <div class="wrap">
        <h1>Ofiaruje – Formularz Darowizn</h1>

        <div style="background:#fff;border-left:4px solid #f0a500;padding:12px 16px;margin:16px 0;border-radius:0 6px 6px 0;max-width:600px;">
            <strong>Shortcode:</strong>
            <code style="background:#f5f5f5;padding:2px 8px;border-radius:4px;font-size:1em;">[ofiaruje_formularz]</code>
            &nbsp;– wklej na dowolnej stronie lub wpisie.
        </div>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'ofiaruje_settings_group' ); ?>
            <?php do_settings_sections( 'ofiaruje-donation-form' ); ?>

            <table class="form-table" role="presentation" style="max-width:720px;">
                <tbody>

                    <!-- Fundraiser ID -->
                    <tr>
                        <th scope="row">
                            <label for="ofiaruje_fundraiser_id">ID Zbiórki <abbr title="Pole wymagane">*</abbr></label>
                        </th>
                        <td>
                            <input
                                type="text"
                                id="ofiaruje_fundraiser_id"
                                name="ofiaruje_fundraiser_id"
                                value="<?php echo esc_attr( get_option( 'ofiaruje_fundraiser_id', '' ) ); ?>"
                                class="regular-text"
                                placeholder="np. 64f2a3b0c1d2e3f4a5b6c7d8"
                                spellcheck="false"
                            />
                            <p class="description">
                                Identyfikator zbiórki (MongoDB <code>_id</code>) skopiowany z panelu Ofiaruje.pl.<br>
                                Widoczny w adresie URL zbiórki: <code>ofiaruje.pl/f/<strong>&lt;ID&gt;</strong></code>
                            </p>
                        </td>
                    </tr>

                    <!-- Preset amounts -->
                    <tr>
                        <th scope="row">
                            <label for="ofiaruje_preset_amounts">Predefiniowane kwoty (PLN)</label>
                        </th>
                        <td>
                            <input
                                type="text"
                                id="ofiaruje_preset_amounts"
                                name="ofiaruje_preset_amounts"
                                value="<?php echo esc_attr( get_option( 'ofiaruje_preset_amounts', '50,100,200,500' ) ); ?>"
                                class="regular-text"
                                placeholder="np. 50,100,200,500"
                            />
                            <p class="description">
                                Kwoty oddzielone przecinkami, wyświetlane jako przyciski wyboru.<br>
                                Minimalna prawidłowa wartość to <strong>20 PLN</strong>. Mniejsze zostaną pominięte.
                            </p>
                        </td>
                    </tr>

                    <!-- Base URL -->
                    <tr>
                        <th scope="row">
                            <label for="ofiaruje_base_url">Adres platformy</label>
                        </th>
                        <td>
                            <input
                                type="url"
                                id="ofiaruje_base_url"
                                name="ofiaruje_base_url"
                                value="<?php echo esc_attr( get_option( 'ofiaruje_base_url', 'https://ofiaruje.pl' ) ); ?>"
                                class="regular-text"
                            />
                            <p class="description">
                                Adres URL platformy Ofiaruje.pl – zazwyczaj nie wymaga zmiany.<br>
                                Domyślnie: <code>https://ofiaruje.pl</code>
                            </p>
                        </td>
                    </tr>

                    <!-- UTM tracking -->
                    <tr>
                        <th scope="row">UTM tracking</th>
                        <td>
                            <label>
                                <input type="checkbox" name="ofiaruje_utm_enabled" value="1" <?php checked( get_option( 'ofiaruje_utm_enabled', '0' ), '1' ); ?>>
                                Dodawaj parametry UTM do adresu formularza
                            </label>
                            <p class="description" style="margin-top:8px;">
                                Gdy opcja jest włączona, formularz automatycznie doda parametry <code>utm_*</code> do URL akcji.
                            </p>

                            <div style="margin-top:12px;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px;max-width:700px;">
                                <div style="min-width:0;">
                                    <label for="ofiaruje_utm_source" style="display:block;margin-bottom:4px;">UTM Source</label>
                                    <input
                                        type="text"
                                        id="ofiaruje_utm_source"
                                        name="ofiaruje_utm_source"
                                        value="<?php echo esc_attr( get_option( 'ofiaruje_utm_source', 'wordpress' ) ); ?>"
                                        class="regular-text"
                                        style="width:100%;max-width:none;box-sizing:border-box;"
                                        placeholder="wordpress"
                                    />
                                </div>

                                <div style="min-width:0;">
                                    <label for="ofiaruje_utm_medium" style="display:block;margin-bottom:4px;">UTM Medium</label>
                                    <input
                                        type="text"
                                        id="ofiaruje_utm_medium"
                                        name="ofiaruje_utm_medium"
                                        value="<?php echo esc_attr( get_option( 'ofiaruje_utm_medium', 'plugin' ) ); ?>"
                                        class="regular-text"
                                        style="width:100%;max-width:none;box-sizing:border-box;"
                                        placeholder="plugin"
                                    />
                                </div>

                                <div style="min-width:0;">
                                    <label for="ofiaruje_utm_campaign" style="display:block;margin-bottom:4px;">UTM Campaign</label>
                                    <input
                                        type="text"
                                        id="ofiaruje_utm_campaign"
                                        name="ofiaruje_utm_campaign"
                                        value="<?php echo esc_attr( get_option( 'ofiaruje_utm_campaign', '' ) ); ?>"
                                        class="regular-text"
                                        style="width:100%;max-width:none;box-sizing:border-box;"
                                        placeholder="np. homepage"
                                    />
                                </div>

                                <div style="min-width:0;">
                                    <label for="ofiaruje_utm_term" style="display:block;margin-bottom:4px;">UTM Term</label>
                                    <input
                                        type="text"
                                        id="ofiaruje_utm_term"
                                        name="ofiaruje_utm_term"
                                        value="<?php echo esc_attr( get_option( 'ofiaruje_utm_term', '' ) ); ?>"
                                        class="regular-text"
                                        style="width:100%;max-width:none;box-sizing:border-box;"
                                        placeholder="opcjonalnie"
                                    />
                                </div>

                                <div style="min-width:0;">
                                    <label for="ofiaruje_utm_content" style="display:block;margin-bottom:4px;">UTM Content</label>
                                    <input
                                        type="text"
                                        id="ofiaruje_utm_content"
                                        name="ofiaruje_utm_content"
                                        value="<?php echo esc_attr( get_option( 'ofiaruje_utm_content', '' ) ); ?>"
                                        class="regular-text"
                                        style="width:100%;max-width:none;box-sizing:border-box;"
                                        placeholder="opcjonalnie"
                                    />
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Custom CSS -->
                    <tr>
                        <th scope="row">
                            <label for="ofiaruje_custom_css">Własny CSS formularza</label>
                        </th>
                        <td>
                            <textarea
                                id="ofiaruje_custom_css"
                                name="ofiaruje_custom_css"
                                class="large-text code"
                                rows="14"
                                spellcheck="false"
                                placeholder="/* Wklej tutaj własne style CSS dla formularza */"
                            ><?php echo esc_textarea( $textarea_css ); ?></textarea>
                            <p style="margin-top:10px;">
                                <button type="button" class="button" id="ofiaruje-set-default-css">Ustaw domyślne</button>
                            </p>
                            <p class="description">
                                Pole startowo zawiera domyślne style wtyczki, które możesz edytować.<br>
                                Kliknięcie <strong>Ustaw domyślne</strong> przywraca domyślny CSS do pola.
                            </p>
                        </td>
                    </tr>

                    <!-- Payment badges toggle -->
                    <tr>
                        <th scope="row">Ikony płatności pod przyciskiem</th>
                        <td>
                            <label>
                                <input type="checkbox" name="ofiaruje_show_payment_icons" value="1" <?php checked( get_option( 'ofiaruje_show_payment_icons', '1' ), '1' ); ?>>
                                Wyświetlaj ikony płatności pod przyciskiem „Przejdź do płatności”
                            </label>
                            <p class="description" style="margin-top:8px;">
                                Ikony są automatycznie ładowane z folderu <code>assets/</code> wtyczki.
                                Obsługiwane pliki: <code>blik-logo.svg</code>, <code>visa-logo.svg</code>, <code>mastercard-logo.svg</code>, <code>apple-pay-logo.svg</code>, <code>google-pay-logo.svg</code>, <code>revolut-logo.svg</code> (lub <code>revolut-pay-logo.svg</code>).
                            </p>
                        </td>
                    </tr>

                </tbody>
            </table>

            <?php submit_button( 'Zapisz ustawienia' ); ?>
        </form>

        <script>
            (function () {
                var setDefaultsBtn = document.getElementById('ofiaruje-set-default-css');
                var cssTextarea = document.getElementById('ofiaruje_custom_css');
                var defaultCss = <?php echo wp_json_encode( $default_css ); ?>;

                if (!setDefaultsBtn || !cssTextarea) {
                    return;
                }

                setDefaultsBtn.addEventListener('click', function () {
                    cssTextarea.value = defaultCss;
                    cssTextarea.focus();
                });
            }());
        </script>
    </div>
    <?php
}

// ═══════════════════════════════════════════════════════════════
//  2.  FRONTEND ASSETS  (CSS + JS loaded only when shortcode is used)
// ═══════════════════════════════════════════════════════════════

/**
 * Track whether our shortcode was rendered on the current page so we can
 * conditionally enqueue assets via wp_footer.
 */
$ofiaruje_shortcode_used = false;

add_shortcode( 'ofiaruje_formularz', 'ofiaruje_render_form' );

add_action( 'wp_footer', 'ofiaruje_maybe_enqueue_assets' );
function ofiaruje_maybe_enqueue_assets() {
    global $ofiaruje_shortcode_used;
    if ( ! $ofiaruje_shortcode_used ) {
        return;
    }

    // ── Inline CSS ──────────────────────────────────────────────────
    echo '<style id="ofiaruje-form-style">' . ofiaruje_inline_css() . '</style>' . "\n";

    // ── Inline JS ───────────────────────────────────────────────────
    echo '<script id="ofiaruje-form-script">' . ofiaruje_inline_js() . '</script>' . "\n";
}

// ─────────────────────────────────────────────────────────────
//  CSS
// ─────────────────────────────────────────────────────────────
function ofiaruje_inline_css() {
    $custom_css = trim( (string) get_option( 'ofiaruje_custom_css', '' ) );
    if ( '' !== $custom_css ) {
        return $custom_css;
    }

    return ofiaruje_default_inline_css();
}

function ofiaruje_default_inline_css() {
    return '
/* ── Ofiaruje Donation Form ─────────────────────────────────── */
.ofiaruje-wrap *{box-sizing:border-box}
.ofiaruje-wrap{max-width:520px;margin:0 auto;font-family:inherit;color:#222}

/* Section titles */
.ofiaruje-section-title{font-size:.78rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#888;margin:0 0 .7rem}

/* Preset amount buttons */
.ofiaruje-amounts{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:1rem}
.ofiaruje-amounts input[type="radio"]{position:absolute;opacity:0;pointer-events:none}
.ofiaruje-amounts label{
    flex:1 1 calc(25% - 8px);min-width:64px;padding:12px 6px;
    text-align:center;border:2px solid #e0e0e0;border-radius:10px;
    cursor:pointer;font-weight:700;font-size:1.05rem;
    background:#fafafa;color:#333;user-select:none;
    transition:border-color .15s,background .15s,color .15s
}
.ofiaruje-amounts label:hover{border-color:#f0a500;background:#fffbf0}
.ofiaruje-amounts input[type="radio"]:checked+label{
    border-color:#f0a500;background:#fff8e6;color:#b37d00
}

/* Custom amount input */
.ofiaruje-custom-wrap{
    display:flex;align-items:center;
    border:2px solid #f0a500;border-radius:10px;
    padding:6px 16px;background:#fff;margin-bottom:.5rem
}
.ofiaruje-custom-wrap .ofiaruje-currency{
    font-weight:700;font-size:1.1rem;color:#888;margin-right:10px;white-space:nowrap
}
.ofiaruje-custom-wrap input{
    border:none;outline:none;font-size:1.6rem;font-weight:700;
    text-align:right;width:100%;color:#222;background:transparent;
    -moz-appearance:textfield
}
.ofiaruje-custom-wrap input::-webkit-outer-spin-button,
.ofiaruje-custom-wrap input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}

/* Error messages */
.ofiaruje-error{
    font-size:.82rem;color:#dc3545;margin:.25rem 0 .75rem;display:none
}
.ofiaruje-error.visible{display:block}

/* Divider */
.ofiaruje-hr{border:none;border-top:1px solid #eee;margin:1.5rem 0}

/* Donor field group */
.ofiaruje-fields{display:flex;flex-direction:column;gap:14px;margin-bottom:1.2rem}
.ofiaruje-row{display:flex;gap:12px}
.ofiaruje-field{display:flex;flex-direction:column;flex:1}
.ofiaruje-field label{font-size:.83rem;font-weight:600;color:#555;margin-bottom:4px}
.ofiaruje-field input{
    padding:10px 13px;border:1.5px solid #ddd;border-radius:8px;
    font-size:.97rem;color:#222;background:#fff;
    transition:border-color .15s
}
.ofiaruje-field input:focus{border-color:#f0a500;outline:none}
.ofiaruje-field input.is-invalid{border-color:#dc3545}
.ofiaruje-field .ofiaruje-field-error{font-size:.8rem;color:#dc3545;margin-top:3px;display:none}
.ofiaruje-field .ofiaruje-field-error.visible{display:block}
.ofiaruje-optional{font-size:.78rem;font-weight:400;color:#aaa;margin-left:4px}

/* Anonymous checkbox */
.ofiaruje-anon-label{
    display:flex;align-items:center;gap:8px;
    font-size:.9rem;color:#555;margin-bottom:1.4rem;cursor:pointer
}
.ofiaruje-anon-label input[type="checkbox"]{
    width:16px;height:16px;cursor:pointer;accent-color:#f0a500;flex-shrink:0
}

/* Submit button */
.ofiaruje-btn{
    display:block;width:100%;padding:14px;
    background:#f0a500;color:#fff;border:none;border-radius:10px;
    font-size:1.05rem;font-weight:700;letter-spacing:.02em;cursor:pointer;
    transition:background .15s,transform .1s
}
.ofiaruje-btn:hover{background:#d49300}
.ofiaruje-btn:active{transform:scale(.99)}

/* Branding */
.ofiaruje-branding{text-align:center;margin-top:1.2rem;font-size:.76rem;color:#bbb}
.ofiaruje-branding a{color:#bbb;text-decoration:none}
.ofiaruje-branding a:hover{color:#f0a500}

/* Payment methods row under CTA */
.ofiaruje-payments{display:flex;gap:8px;justify-content:center;margin-top:12px}
.ofiaruje-pay-badge{
    height:28px;min-width:72px;padding:0 10px;
    display:inline-flex;align-items:center;justify-content:center;
    font-size:12px;line-height:1;font-weight:700;letter-spacing:.01em;
}
.ofiaruje-pay-badge[data-brand="blik"]{}
.ofiaruje-pay-badge[data-brand="visa"]{}
.ofiaruje-pay-badge[data-brand="mastercard"]{}
.ofiaruje-pay-badge[data-brand="apple_pay"]{}
.ofiaruje-pay-badge[data-brand="google_pay"]{}
.ofiaruje-pay-badge[data-brand="revolut_pay"]{}
.ofiaruje-pay-logo{height:16px;width:52px;object-fit:contain;display:block}

@media(max-width:480px){
    .ofiaruje-amounts label{flex:1 1 calc(50% - 5px)}
    .ofiaruje-row{flex-direction:column}
}
';
}

// ─────────────────────────────────────────────────────────────
//  JavaScript
// ─────────────────────────────────────────────────────────────
function ofiaruje_inline_js() {
    return '
(function () {
    "use strict";

    var form = document.getElementById("ofiaruje-donation-form");
    if (!form) return;

    var amountInput  = document.getElementById("ofiaruje-amount-input");
    var amountHidden = document.getElementById("ofiaruje-amount-hidden");
    var amountError  = document.getElementById("ofiaruje-amount-error");
    var radios       = form.querySelectorAll("input[name=\'ofiaruje_preset\']");

    // ── Initialise hidden amount from the pre-checked radio ──────────────
    (function init() {
        var checked = form.querySelector("input[name=\'ofiaruje_preset\']:checked");
        if (checked) {
            amountInput.value  = checked.value;
            amountHidden.value = checked.value;
        }
    })();

    // ── Preset radio → fill amount input ────────────────────────────────
    radios.forEach(function (radio) {
        radio.addEventListener("change", function () {
            amountInput.value  = this.value;
            amountHidden.value = this.value;
            amountError.classList.remove("visible");
        });
    });

    // ── Custom input → deselect preset radios ───────────────────────────
    amountInput.addEventListener("input", function () {
        radios.forEach(function (r) { r.checked = false; });
        amountHidden.value = amountInput.value;
    });

    // ── Required-field validation helpers ───────────────────────────────
    function validateField(input) {
        var wrapper  = input.closest(".ofiaruje-field");
        var errorEl  = wrapper ? wrapper.querySelector(".ofiaruje-field-error") : null;
        var value    = input.value.trim();
        var invalid  = false;

        if (value === "") {
            invalid = true;
        } else if (input.type === "email" && !isValidEmail(value)) {
            invalid = true;
        }

        input.classList.toggle("is-invalid", invalid);
        if (errorEl) errorEl.classList.toggle("visible", invalid);
        return !invalid;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // ── Live error clearing ──────────────────────────────────────────────
    form.querySelectorAll("input[data-required]").forEach(function (input) {
        input.addEventListener("input", function () {
            if (input.value.trim() !== "") {
                validateField(input);
            }
        });
        input.addEventListener("blur", function () {
            validateField(input);
        });
    });

    // ── Form submit ──────────────────────────────────────────────────────
    form.addEventListener("submit", function (e) {
        var valid = true;
        var firstInvalid = null;

        // Validate amount
        var amount = parseFloat(amountInput.value);
        if (!amountInput.value || isNaN(amount) || amount < 20) {
            amountError.classList.add("visible");
            if (!firstInvalid) firstInvalid = amountInput;
            valid = false;
        } else {
            amountError.classList.remove("visible");
            amountHidden.value = Math.floor(amount);
        }

        // Validate required donor fields
        form.querySelectorAll("input[data-required]").forEach(function (input) {
            if (!validateField(input)) {
                if (!firstInvalid) firstInvalid = input;
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            if (firstInvalid) firstInvalid.focus();
        }
    });
}());
';
}

// ═══════════════════════════════════════════════════════════════
//  3.  SHORTCODE  [ofiaruje_formularz]
// ═══════════════════════════════════════════════════════════════

function ofiaruje_render_form( $atts ) {
    global $ofiaruje_shortcode_used;
    $ofiaruje_shortcode_used = true;

    $fundraiser_id  = get_option( 'ofiaruje_fundraiser_id', '' );
    $preset_amounts = get_option( 'ofiaruje_preset_amounts', '50,100,200,500' );
    $base_url       = rtrim( get_option( 'ofiaruje_base_url', 'https://ofiaruje.pl' ), '/' );
    $show_payment_icons = get_option( 'ofiaruje_show_payment_icons', '1' ) === '1';
    $payment_methods = ofiaruje_get_payment_methods();

    // Show a friendly notice to admins when the plugin isn't configured yet.
    if ( empty( $fundraiser_id ) ) {
        if ( current_user_can( 'manage_options' ) ) {
            return sprintf(
                '<p style="color:#dc3545;border:1px solid #dc3545;padding:10px 14px;border-radius:6px;max-width:520px;">'
                . '<strong>Ofiaruje:</strong> Brak ID zbiórki. '
                . '<a href="%s">Przejdź do ustawień wtyczki</a>, aby go ustawić.'
                . '</p>',
                esc_url( admin_url( 'options-general.php?page=ofiaruje-donation-form' ) )
            );
        }
        return '';
    }

    // Parse and filter preset amounts.
    $amounts = [];
    foreach ( explode( ',', $preset_amounts ) as $part ) {
        $n = absint( trim( $part ) );
        if ( $n >= 20 ) {
            $amounts[] = $n;
        }
    }

    $first_amount = ! empty( $amounts ) ? $amounts[0] : 50;

    $action_params = [
        'fid' => $fundraiser_id,
    ];

    if ( '1' === get_option( 'ofiaruje_utm_enabled', '0' ) ) {
        $utm_values = [
            'utm_source'   => (string) get_option( 'ofiaruje_utm_source', 'wordpress' ),
            'utm_medium'   => (string) get_option( 'ofiaruje_utm_medium', 'plugin' ),
            'utm_campaign' => (string) get_option( 'ofiaruje_utm_campaign', '' ),
            'utm_term'     => (string) get_option( 'ofiaruje_utm_term', '' ),
            'utm_content'  => (string) get_option( 'ofiaruje_utm_content', '' ),
        ];

        foreach ( $utm_values as $key => $value ) {
            $value = trim( $value );
            if ( '' !== $value ) {
                $action_params[ $key ] = $value;
            }
        }
    }

    /*
     * The form POSTs directly to ofiaruje.pl/d?fid=…
     * Standard cross-site HTML form submission – no CORS restriction applies.
     * The ofiaruje.pl app handles the payment flow and redirects the donor.
     */
    $action_url = esc_url( add_query_arg( $action_params, $base_url . '/d' ) );

    ob_start();
    ?>
    <div class="ofiaruje-wrap">
        <form
            id="ofiaruje-donation-form"
            method="POST"
            action="<?php echo $action_url; ?>"
            novalidate
        >
            <!-- ── Hidden system fields ── -->
            <input type="hidden" name="donation[type]"     value="single">
            <input type="hidden" name="donation[tip]"      value="0">
            <input type="hidden" name="donation[currency]" value="PLN">
            <!-- This field carries the validated integer amount to the server -->
            <input
                type="hidden"
                id="ofiaruje-amount-hidden"
                name="donation[amount]"
                value="<?php echo esc_attr( $first_amount ); ?>"
            >

            <!-- ════════════════════════════════════ -->
            <!-- KWOTA                                -->
            <!-- ════════════════════════════════════ -->
            <div class="ofiaruje-section-title">Wybierz kwotę darowizny</div>

            <?php if ( ! empty( $amounts ) ) : ?>
            <div class="ofiaruje-amounts" role="group" aria-label="Predefiniowane kwoty darowizny">
                <?php foreach ( $amounts as $i => $amount ) :
                    $uid = 'ofiaruje-preset-' . $i;
                ?>
                <input
                    type="radio"
                    name="ofiaruje_preset"
                    id="<?php echo esc_attr( $uid ); ?>"
                    value="<?php echo esc_attr( $amount ); ?>"
                    <?php checked( $i, 0 ); ?>
                >
                <label for="<?php echo esc_attr( $uid ); ?>">
                    <?php echo esc_html( $amount ); ?>&nbsp;zł
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="ofiaruje-custom-wrap">
                <span class="ofiaruje-currency">PLN</span>
                <input
                    type="number"
                    id="ofiaruje-amount-input"
                    min="20"
                    step="1"
                    inputmode="numeric"
                    placeholder="<?php echo esc_attr( $first_amount ); ?>"
                    value="<?php echo esc_attr( $first_amount ); ?>"
                    aria-label="Kwota darowizny w PLN"
                >
            </div>

            <div id="ofiaruje-amount-error" class="ofiaruje-error" role="alert">
                Minimalna kwota darowizny to 20&nbsp;PLN.
            </div>

            <hr class="ofiaruje-hr">

            <!-- ════════════════════════════════════ -->
            <!-- DANE DARCZYŃCY                       -->
            <!-- ════════════════════════════════════ -->
            <div class="ofiaruje-section-title">Dane darczyńcy</div>

            <div class="ofiaruje-fields">

                <!-- Imię + Nazwisko -->
                <div class="ofiaruje-row">
                    <div class="ofiaruje-field">
                        <label for="ofiaruje-firstname">Imię</label>
                        <input
                            type="text"
                            id="ofiaruje-firstname"
                            name="donation[donor][details][firstname]"
                            placeholder="Jan"
                            autocomplete="given-name"
                            data-required="1"
                        >
                        <span class="ofiaruje-field-error" role="alert">Podaj imię.</span>
                    </div>

                    <div class="ofiaruje-field">
                        <label for="ofiaruje-lastname">Nazwisko</label>
                        <input
                            type="text"
                            id="ofiaruje-lastname"
                            name="donation[donor][details][lastname]"
                            placeholder="Kowalski"
                            autocomplete="family-name"
                            data-required="1"
                        >
                        <span class="ofiaruje-field-error" role="alert">Podaj nazwisko.</span>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="ofiaruje-field">
                    <label for="ofiaruje-email">Adres e-mail</label>
                    <input
                        type="email"
                        id="ofiaruje-email"
                        name="donation[donor][details][email]"
                        placeholder="jan@kowalski.pl"
                        autocomplete="email"
                        inputmode="email"
                        data-required="1"
                    >
                    <span class="ofiaruje-field-error" role="alert">Podaj prawidłowy adres e-mail.</span>
                </div>

                <!-- Organizacja (opcjonalne) -->
                <div class="ofiaruje-field">
                    <label for="ofiaruje-orgname">
                        Nazwa firmy lub organizacji
                        <span class="ofiaruje-optional">(opcjonalnie)</span>
                    </label>
                    <input
                        type="text"
                        id="ofiaruje-orgname"
                        name="donation[donor][details][orgname]"
                        placeholder="Przykładowa Sp. z o.o."
                        autocomplete="organization"
                    >
                </div>

            </div><!-- /.ofiaruje-fields -->

            <!-- Anonimowość -->
            <label class="ofiaruje-anon-label">
                <input type="checkbox" name="donation[anonymous]" value="true">
                Nie wyświetlaj moich danych na stronie zbiórki
            </label>

            <!-- Submit -->
            <button type="submit" class="ofiaruje-btn">
                Przejdź do płatności &rarr;
            </button>

            <?php if ( $show_payment_icons ) : ?>
            <div class="ofiaruje-payments" aria-label="Obsługiwane metody płatności">
                <?php foreach ( $payment_methods as $method ) : ?>
                    <span class="ofiaruje-pay-badge" data-brand="<?php echo esc_attr( $method['key'] ); ?>">
                        <?php if ( ! empty( $method['logo'] ) ) : ?>
                            <img
                                src="<?php echo esc_url( $method['logo'] ); ?>"
                                alt="<?php echo esc_attr( $method['label'] ); ?>"
                                class="ofiaruje-pay-logo"
                                loading="lazy"
                                decoding="async"
                            >
                        <?php else : ?>
                            <?php echo esc_html( $method['label'] ); ?>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </form>

        <p class="ofiaruje-branding">
            Darowizny obsługiwane przez Fundację "Ofiaruję" &mdash;
            <a href="https://ofiaruje.pl" target="_blank" rel="noopener noreferrer">ofiaruje.pl</a>
        </p>
    </div><!-- /.ofiaruje-wrap -->
    <?php
    return ob_get_clean();
}

function ofiaruje_get_payment_methods() {
    $assets_dir = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'assets/';
    $base_url   = trailingslashit( plugins_url( 'assets', __FILE__ ) );

    $map = [
        [
            'key'     => 'blik',
            'label'   => 'BLIK',
            'file'    => 'blik-logo.svg',
        ],
        [
            'key'     => 'visa',
            'label'   => 'Visa',
            'file'    => 'visa-logo.svg',
        ],
        [
            'key'     => 'mastercard',
            'label'   => 'Mastercard',
            'file'    => 'mastercard-logo.svg',
        ],
        [
            'key'     => 'apple_pay',
            'label'   => 'Apple Pay',
            'file'    => 'apple-pay-logo.svg',
        ],
        [
            'key'     => 'google_pay',
            'label'   => 'Google Pay',
            'file'    => 'google-pay-logo.svg',
        ],
        [
            'key'     => 'revolut_pay',
            'label'   => 'Revolut Pay',
            'file'    => [ 'revolut-logo.svg', 'revolut-pay-logo.svg' ],
        ],
    ];

    $methods = [];
    foreach ( $map as $item ) {
        $files = is_array( $item['file'] ) ? $item['file'] : [ $item['file'] ];
        $url   = '';
        foreach ( $files as $file ) {
            $path = $assets_dir . $file;
            if ( file_exists( $path ) ) {
                $url = $base_url . $file;
                break;
            }
        }

        $methods[] = [
            'key'   => $item['key'],
            'label' => $item['label'],
            'logo'  => $url,
        ];
    }

    return $methods;
}
