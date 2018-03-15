<?php
/**
 * Connection error template.
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
    <h1>Something went wrong...</h1>
    <div class="installation-desc">
        <p class="intro">
            Your WordPress is not working properly. "wp_remote_get" function is on <?php echo $error ?> error with error details: <?php echo $message ?>.
        </p>
        <p class="intro">
            Please <a class="a-important" href="#" onClick="window.location.reload()">reload this page</a> or <a href="https://secure.livechatinc.com/licence/1520/open_chat.cgi?groups=77&email=<?php echo urlencode($user_email) ?>&name=<?php echo urlencode($username) ?>&params=integration%3DWooCommerce" class="a-important" target="_blank">contact our support team</a>.
        </p>
    </div>
    <div class="links">
        If you have any questions, we're
            <a href="https://secure.livechatinc.com/licence/1520/open_chat.cgi?groups=77&email=<?php echo urlencode($user_email) ?>&name=<?php echo urlencode($username) ?>&params=integration%3DWooCommerce%26errorMessage%3<?php echo $message ?>%26errorCode%3<?php echo $code ?>" class="a-important" target="_blank">
                available to chat
            </a>
        24/7/365.
    </div>
</div>
