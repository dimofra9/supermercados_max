<?php
/**
 * Script template. This template is included to all public pages. Includes LiveChat window script and function for
 * updating LiveChat custom parameters.
 *
 * @category Public pages
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
header( "Content-type: text/javascript; charset: UTF-8" );

?>
var __lc = {};
__lc.license = <?php echo $license_id ?>;
__lc.visitor = {
    name: "<?php echo $visitor_name ?>",
    email: "<?php echo $visitor_email ?>"
};
__lc.params = [
    <?php foreach ($custom_data as $key => $value): ?>
            { name: '<?php echo $key ?>', value: '<?php echo $value ?>' },
    <?php endforeach ?>
];
(function() {
var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
})();

function checkCart() {
    var params = 'action=wc-livechat-check-cart';
    var obj;
    try {
        obj = new XMLHttpRequest();
    } catch(e){
        try {
        obj = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try {
            obj = new ActiveXObject("Microsoft.XMLHTTP");
            } catch(e) {
                return false;
            }
        }
    }
    obj.onreadystatechange = function() {
        if(obj.readyState === 4) {
            var currentParams = JSON.parse(obj.responseText);
            if (JSON.stringify(__lc.params) !== JSON.stringify(currentParams)) {
                LC_API.set_custom_variables(currentParams);
                __lc.params = currentParams;
            }
        }
    }
    obj.open('POST', '<?php echo $ajax_url ?>', true);
    obj.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    obj.send(params);
    return obj;
}

setInterval(function() { checkCart(); }, 10000);
