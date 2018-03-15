<?php
/**
 * Settings template. Allows admin to manage LiveChat window settings.
 *
 * @category Admin pages
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="woocommerce-livechat-container">
    <div class="login-box-header">
        <span class="logo-woo"></span>
        <span class="logo"></span>
    </div>
    <h2>LiveChat installed successfully!</h2>
    <div class="installation-desc">
        Sign in to LiveChat and start chatting with your customers.
    </div>
    <p class="lc-submit multi-buttons">
        <a href="https://my.livechatinc.com/" target="_blank">
            <button class="button"><span>Launch web app</span></button>
        </a>
        <a href="http://www.livechatinc.com/product" target="_blank">
            <button class="button green"><span>Download desktop app</span></button>
        </a>
    </p>
    <h2>Integration settings</h2>
    <div class="installation-desc">
        The below information about customers will be available during chat. You can manage which details to display.
    </div>
    <div class="settings">
        <div>
            <div class="title">
                <span>Products details</span>
            </div>
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?php echo $settings_products_key ?>" <?php echo (isset($settings[$settings_products_key]) && $settings[$settings_products_key]) ? 'checked': '' ?>>
                <label class="onoffswitch-label" for="<?php echo $settings_products_key ?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <div>
            <div class="title">
                <span>Products count</span>
            </div>
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?php echo $settings_products_count_key ?>" <?php echo (isset($settings[$settings_products_count_key]) && $settings[$settings_products_count_key]) ? 'checked': '' ?>>
                <label class="onoffswitch-label" for="<?php echo $settings_products_count_key ?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <div>
            <div class="title">
                <span>Total value</span>
            </div>
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?php echo $settings_total_value_key ?>" <?php echo (isset($settings[$settings_total_value_key]) && $settings[$settings_total_value_key]) ? 'checked': '' ?>>
                <label class="onoffswitch-label" for="<?php echo $settings_total_value_key ?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <div>
            <div class="title">
                <span>Shipping address</span>
            </div>
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?php echo $settings_shipping_address_key ?>" <?php echo (isset($settings[$settings_shipping_address_key]) && $settings[$settings_shipping_address_key]) ? 'checked': '' ?>>
                <label class="onoffswitch-label" for="<?php echo $settings_shipping_address_key ?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <div>
            <div class="title">
                <span>Last order details</span>
            </div>
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?php echo $settings_last_order_key ?>" <?php echo (isset($settings[$settings_last_order_key]) && $settings[$settings_last_order_key]) ? 'checked': '' ?>>
                <label class="onoffswitch-label" for="<?php echo $settings_last_order_key ?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="links">
        Your LiveChat account is: <?php echo $license_email ?>. <a href="?page=wc-livechat&amp;reset=1" id="resetAccount" class="a-important">Connect a different LiveChat account.</a>
    </div>
    <div class="links">
        If you have any questions, we're
            <a href="https://secure.livechatinc.com/licence/1520/open_chat.cgi?groups=77&email=<?php echo urlencode($user_email) ?>&name=<?php echo urlencode($username) ?>&params=integration%3DWooCommerce" class="a-important" target="_blank">
                available to chat
            </a>
        24/7/365.
    </div>
</div>
