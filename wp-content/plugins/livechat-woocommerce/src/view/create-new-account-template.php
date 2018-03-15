<?php
/**
 * Create new account template.
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
    <h2>Use an existing LiveChat account</h2>
    <form action="#" id="useExistingAccountForm">
        <div>
            <label for="lc-login">LiveChat login</label><input class="textField" id="lc-login" value="" placeholder="<?php echo $user_email ?>"/>
            <span id="lc-login-error" class="error hidden"></span>
        </div>
        <div class="lc-submit">
            <button class="button"><span>Use existing account</span></button>
        </div>
    </form>
    <h2><strong>or</strong> create a new LiveChat account</h2>
    <form action="#" id="createNewAccountForm">
        <div>
            <label for="full-name">Full name</label><input class="textField" id="full-name" value="<?php echo $username ?>" placeholder="<?php echo $username ?>"/>
            <span id="full-name-error" class="error hidden"></span>
        </div>
        <div>
            <label for="email">Email</label><input class="textField" id="email" value="<?php echo $user_email ?>" placeholder="<?php echo $user_email ?>"/>
            <span id="email-error" class="error hidden"></span>
        </div>
        <div>
            <label for="password">Password</label><input class="textField" id="password" type="password"/>
            <span id="password-error" class="error hidden"></span>
        </div>
        <div class="lc-submit">
            <button class="button green"><span>Create new account</span></button>
        </div>
    </form>
    <div class="links">
        If you have any questions, we're
            <a href="https://secure.livechatinc.com/licence/1520/open_chat.cgi?groups=77&email=<?php echo urlencode($user_email) ?>&name=<?php echo urlencode($username) ?>&params=integration%3DWooCommerce" class="a-important" target="_blank">
                available to chat
            </a>
        24/7/365.
    </div>
    <div class="links">
        By creating an account you agree to
        <a href="http://www.livechatinc.com/terms-and-conditions" target="_blank">Terms &amp; Conditions</a> and
        <a href="http://www.livechatinc.com/privacy-policy" target="_blank">Privacy Policy</a>
    </div>
</div>
