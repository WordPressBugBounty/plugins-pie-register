<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
$piereg = $this->get_pr_global_options();
?>

<div class="captcha-settings">
    <h3>
        <?php esc_html_e("reCAPTCHA Settings",'pie-register'); ?>
    </h3>
    <div class="recaptcha-settings">
        <div class="fields">
            <p>
                <?php echo wp_kses_post("Click <a href='https://www.google.com/recaptcha/admin' target='_blank'>here</a> to get reCAPTCHA keys for your site.",'pie-register') ?>
            </p>
            <p id="piereg_reCAPTCHA_Public_Key_error" style="display:none;color:#F00;">
                <strong>
                    <?php esc_html_e("Error : Invalid Re-Captcha keys",'pie-register') ?>
                </strong>
            </p>
        </div>
        <div class="fields">
            <div class="flt_lft width_full">
                <label for="piereg_recaptcha_type">
                    <?php esc_html_e("reCAPTCHA Type",'pie-register') ?>
                </label>
                <select class="piereg_recaptcha_type" name="piereg_recaptcha_type" id="piereg_recaptcha_type">
                    <option <?php selected(isset($piereg['piereg_recaptcha_type']) && $piereg['piereg_recaptcha_type'] == 'v2', true); ?> value="v2">v2</option>
                    <option <?php selected(isset($piereg['piereg_recaptcha_type']) && $piereg['piereg_recaptcha_type'] == 'v3', true); ?> value="v3">v3</option>
                    <option <?php selected(isset($piereg['piereg_recaptcha_type']) && $piereg['piereg_recaptcha_type'] == 'v2_invisible', true); ?> value="v2_invisible"><?php esc_html_e( 'v2 Invisible', 'pie-register' ); ?></option>
                    <option <?php selected(isset($piereg['piereg_recaptcha_type']) && $piereg['piereg_recaptcha_type'] == 'turnstile', true); ?> value="turnstile"><?php esc_html_e( 'Cloudflare Turnstile', 'pie-register' ); ?></option>
                </select>
            </div>
        </div>
        <div class="fields">
            <div class="flt_lft width_full">
                <label for="piereg_reCAPTCHA_Public_Key">
                    <?php esc_html_e("reCAPTCHA Site Key v2",'pie-register') ?>
                </label>
                <input type="text" id="piereg_reCAPTCHA_Public_Key" name="captcha_publc" class="input_fields" value="<?php echo esc_attr($piereg['captcha_publc'])?>" />
            </div>
            <span class="quotation">
                <?php esc_html_e("Required only if you decide to use the reCAPTCHA field. Sign up for a free account to get the key.",'pie-register') ?>
            </span>
        </div>
        <div class="fields">
            <div class="flt_lft width_full">
                <label for="piereg_reCAPTCHA_Private_Key">
                    <?php esc_html_e("reCAPTCHA Secret Key v2",'pie-register') ?>
                </label>
                <input type="text" id="piereg_reCAPTCHA_Private_Key" name="captcha_private" class="input_fields" value="<?php echo esc_attr($piereg['captcha_private'])?>" />
            </div>
            <span class="quotation">
                <?php esc_html_e("Required only if you decide to use the reCAPTCHA field. Sign up for a free account to get the key.",'pie-register') ?>
            </span>
        </div>
        <div class="fields">
            <div class="flt_lft width_full">
                <label for="piereg_reCAPTCHA_Public_Key_v3">
                    <?php esc_html_e("reCAPTCHA Site Key v3",'pie-register') ?>
                </label>
                <input type="text" id="piereg_reCAPTCHA_Public_Key_v3" name="captcha_publc_v3" class="input_fields" value="<?php echo esc_attr($piereg['captcha_publc_v3'])?>" />
            </div>
            <span class="quotation">
                <?php esc_html_e("Required only if you decide to use the reCAPTCHA field. Sign up for a free account to get the key.",'pie-register') ?>
            </span>
        </div>
        <div class="fields">
            <div class="flt_lft width_full">
                <label for="piereg_reCAPTCHA_Private_Key_v3">
                    <?php esc_html_e("reCAPTCHA Secret Key v3",'pie-register') ?>
                </label>
                <input type="text" id="piereg_reCAPTCHA_Private_Key_v3" name="captcha_private_v3" class="input_fields" value="<?php echo esc_attr($piereg['captcha_private_v3'])?>" />
            </div>
            <span class="quotation">
                <?php esc_html_e("Required only if you decide to use the reCAPTCHA field. Sign up for a free account to get the key.",'pie-register') ?>
            </span>
        </div>
        <div class="fields piereg-turnstile-row">
            <div class="flt_lft width_full">
                <label for="piereg_turnstile_site">
                    <?php esc_html_e( 'Turnstile Site Key', 'pie-register' ); ?>
                </label>
                <input type="text" id="piereg_turnstile_site" name="turnstile_site" class="input_fields" value="<?php echo esc_attr( isset( $piereg['turnstile_site'] ) ? $piereg['turnstile_site'] : '' ); ?>" />
            </div>
            <span class="quotation">
                <?php echo wp_kses( __( "Create a free Turnstile widget in your <a href='https://dash.cloudflare.com/?to=/:account/turnstile' target='_blank' rel='noopener noreferrer'>Cloudflare dashboard</a> to get the keys.", 'pie-register' ), array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); // phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent -- plain link to the Cloudflare dashboard, nothing is loaded from it ?>
            </span>
        </div>
        <div class="fields piereg-turnstile-row">
            <div class="flt_lft width_full">
                <label for="piereg_turnstile_secret">
                    <?php esc_html_e( 'Turnstile Secret Key', 'pie-register' ); ?>
                </label>
                <input type="text" id="piereg_turnstile_secret" name="turnstile_secret" class="input_fields" value="<?php echo esc_attr( isset( $piereg['turnstile_secret'] ) ? $piereg['turnstile_secret'] : '' ); ?>" />
            </div>
            <span class="quotation">
                <?php esc_html_e( 'Turnstile works on registration, login and forgot password wherever the reCAPTCHA field or option is used.', 'pie-register' ); ?>
            </span>
        </div>
        <p class="piereg-invisible-note quotation" style="display:none;"><?php esc_html_e( 'v2 Invisible uses the v2 key fields above. Create the keys in Google as reCAPTCHA v2 "Invisible reCAPTCHA badge" (checkbox keys do not work).', 'pie-register' ); ?></p>
        <div class="fields">
            <div class="flt_lft width_full">
                <label for="piereg_recaptcha_language">
                    <?php esc_html_e("reCAPTCHA Language",'pie-register') ?>
                </label>
                <div>
                    <select name="piereg_recaptcha_language" id="piereg_recaptcha_language">
                        <option value="ar"
                            <?php selected(($piereg['piereg_recaptcha_language']=="ar"), true); ?>>
                            <?php esc_html_e("Arabic","pie-register"); ?>
                        </option>
                        <option value="zh-HK"
                            <?php selected(($piereg['piereg_recaptcha_language']=="zh-HK"), true); ?>>
                            <?php esc_html_e("Chinese (Hong Kong)","pie-register"); ?>
                        </option>
                        <option value="zh-CN"
                            <?php selected(($piereg['piereg_recaptcha_language']=="zh-CN"), true); ?>>
                            <?php esc_html_e("Chinese (Simplified)","pie-register"); ?>
                        </option>
                        <option value="zh-TW"
                            <?php selected(($piereg['piereg_recaptcha_language']=="zh-TW"), true); ?>>
                            <?php esc_html_e("Chinese (Traditional)","pie-register"); ?>
                        </option>
                        <option value="en"
                            <?php selected(($piereg['piereg_recaptcha_language']=="en"), true); ?>>
                            <?php esc_html_e("English (US)","pie-register"); ?>
                        </option>
                        <option value="fr"
                            <?php selected(($piereg['piereg_recaptcha_language']=="fr"), true); ?>>
                            <?php esc_html_e("French","pie-register"); ?>
                        </option>
                        <option value="fr-CA"
                            <?php selected(($piereg['piereg_recaptcha_language']=="fr-CA"), true); ?>>
                            <?php esc_html_e("French (Canadian)","pie-register"); ?>
                        </option>
                        <option value="de"
                            <?php selected(($piereg['piereg_recaptcha_language']=="de"), true); ?>>
                            <?php esc_html_e("German","pie-register"); ?>
                        </option>
                        <option value="de-AT"
                            <?php selected(($piereg['piereg_recaptcha_language']=="de-AT"), true); ?>>
                            <?php esc_html_e("German (Austria)","pie-register"); ?>
                        </option>
                        <option value="de-CH"
                            <?php selected(($piereg['piereg_recaptcha_language']=="de-CH"), true); ?>>
                            <?php esc_html_e("German (Switzerland)","pie-register"); ?>
                        </option>
                        <option value="pl"
                            <?php selected(($piereg['piereg_recaptcha_language']=="pl"), true); ?>>
                            <?php esc_html_e("Polish","pie-register"); ?>
                        </option>
                        <option value="pt"
                            <?php selected(($piereg['piereg_recaptcha_language']=="pt"), true); ?>>
                            <?php esc_html_e("Portuguese","pie-register"); ?>
                        </option>
                        <option value="pt-BR"
                            <?php selected(($piereg['piereg_recaptcha_language']=="pt-BR"), true); ?>>
                            <?php esc_html_e("Portuguese (Brazil)","pie-register"); ?>
                        </option>
                        <option value="pt-PT"
                            <?php selected(($piereg['piereg_recaptcha_language']=="pt-PT"), true); ?>>
                            <?php esc_html_e("Portuguese (Portugal)","pie-register"); ?>
                        </option>
                        <option value="ru"
                            <?php selected(($piereg['piereg_recaptcha_language']=="ru"), true); ?>>
                            <?php esc_html_e("Russian","pie-register"); ?>
                        </option>
                        <option value="es"
                            <?php selected(($piereg['piereg_recaptcha_language']=="es"), true); ?>>
                            <?php esc_html_e("Spanish","pie-register"); ?>
                        </option>
                        <option value="es-419"
                            <?php selected(($piereg['piereg_recaptcha_language']=="es-419"), true); ?>>
                            <?php esc_html_e("Spanish (Latin America)","pie-register"); ?>
                        </option>
                        <option value="tr"
                            <?php selected(($piereg['piereg_recaptcha_language']=="tr"), true); ?>>
                            <?php esc_html_e("Turkish","pie-register"); ?>
                        </option>
                    </select>
                </div>
            </div>
        </div>

    </div>
</div>