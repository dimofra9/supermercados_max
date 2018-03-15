// WooCommerce LiveChat
//
// @category Admin pages
(function ($) {
    var isLoginAutoFilled = false;
    var WooCommerceLiveChat = {
        init: function () {
            this.alreadyHaveAccountForm();
            this.newLicenseForm();
            this.settingsForm();
        },
        alreadyHaveAccountForm: function () {
            $('#useExistingAccountForm').submit(function () {
                var button = $(this).find('button');
                var orginalButtonText = button.children('span').text();
                WooCommerceLiveChat.showLoader(button);

                WooCommerceLiveChat.hideErrors('lc-login');
                var login = $.trim($('#lc-login').val());
                if (!login.length) {
                    if (isLoginAutoFilled === false) {
                        login = $.trim($('#lc-login').attr('placeholder'));
                        isLoginAutoFilled = true;
                    } else {
                        WooCommerceLiveChat.showError('lc-login', 'Please provide your login');
                        return false;
                    }
                }

                $.ajax({
                    url: WcLcUrls.checkLicense + login + '?callback=?',
                    type: "GET",
                    dataType: 'jsonp',
                    cache: false,
                    success: function (data, status, error) {
                        if (data.error) {
                            WooCommerceLiveChat.showError('lc-login', 'Incorrect LiveChat login.');
                        } else {
                            WooCommerceLiveChat.setSettings('licenseEmail', login);
                            WooCommerceLiveChat.setSettings('licenseId', data.number, 1);
                        }
                        WooCommerceLiveChat.hideLoader(button, orginalButtonText);
                    },
                    error: function (data, status, error) {
                        WooCommerceLiveChat.showError('lc-login', 'Something went wrong. Please try again or contact our support team.');
                        WooCommerceLiveChat.hideLoader(button, orginalButtonText);
                    }
                });

                return false;
            });
        },
        setSettings: function(key, value, reload) {
            var data = {action: 'wc-livechat-update-settings'};
            data[key] = value;

            $.ajax({
                url: WcLcUrls.setSettings,
                type: "POST",
                data: data,
                dataType: 'json',
                cache: false,
                async: false,
                success: function (data, status, error) {
                    if (data == 'ok') {
                        if (reload == 1) {
                            location.reload();
                        }
                    }
                },
                error: function (data, status, error) {
                    alert('Something went wrong. Please try again or contact our support team.');
                }
            });

        },
        showError: function(id, message) {
            $('#' + id).addClass('error').focus();
            $('#' + id + '-error').removeClass('hidden').html(message);
        },
        hideErrors: function(id) {
            $('#' + id).removeClass('error');
            $('#' + id + '-error').addClass('hidden');
        },
        showLoader: function(button) {
            button.attr('readonly', 'readonly').css('opacity', '0.5').children('span').html('Loading...');
        },
        hideLoader: function(button, text) {
            button.attr('readonly', false).css('opacity', '').children('span').html(text);
        },
        newLicenseForm: function () {
            $('#createNewAccountForm').submit(function () {
                WooCommerceLiveChat.hideErrors('full-name');
                WooCommerceLiveChat.hideErrors('email');
                WooCommerceLiveChat.hideErrors('password');

                if (WooCommerceLiveChat.validateNewLicenseForm()) {
                    var button = $(this).find('button');
                    var orginalButtonText = button.children('span').text();
                    WooCommerceLiveChat.showLoader(button);

                    $.ajax({
                        url: WcLcUrls.newLicense,
                        data: {
                            name: $('#full-name').val(), 
                            email: $('#email').val(),
                            password: $('#password').val(),
                            promo_code: 'woocommerce',
                            timezone: 'utc'
                        },
                        type: "POST",
                        dataType: 'json',
                        cache: false,
                        success: function (data, status, error) {
                            if (data.error) {
                                WooCommerceLiveChat.showError('password', data.error);
                            } else {
                                WooCommerceLiveChat.setSettings('licenseEmail', $('#email').val());
                                WooCommerceLiveChat.setSettings('licenseId', data.license, 1);
                            }
                            WooCommerceLiveChat.hideLoader(button, orginalButtonText);
                        },
                        error: function (data, status, error) {
                            var response = jQuery.parseJSON(data.responseText);
                            if (response.error) {
                                WooCommerceLiveChat.showError('password', response.error);
                            } else {
                                WooCommerceLiveChat.showError('password', 'Something went wrong. Please try again or contact our support team.');
                            }
                            WooCommerceLiveChat.hideLoader(button, orginalButtonText);
                        }
                    });
                }

                return false;
            });
        },
        settingsForm: function() {
            $('#resetAccount').click(function() {
                return confirm('This will reset your LiveChat plugin settings. Do you want to continue?');
            });
            $('.settings .title').click(function() {
                $(this).next('.onoffswitch').children('label').click();
            });
            $('.onoffswitch-checkbox').change(function() {
                var paramName = $(this).attr('id');
                if ($('#' + paramName).is(':checked')) {
                    WooCommerceLiveChat.setSettings('customDataSettings', paramName + ':' + 1);
                } else {
                    WooCommerceLiveChat.setSettings('customDataSettings', paramName + ':' + 0);
                }
            });
        },
        validateNewLicenseForm: function () {
            if ($('#full-name').val().length < 1) {
                WooCommerceLiveChat.showError('full-name', 'Please enter your name.');
                return false;
            }

            if (/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i.test($('#email').val()) == false) {
                WooCommerceLiveChat.showError('email', 'Please enter a valid email address.');
                return false;
            }

            if ($.trim($('#password').val()).length < 6) {
                WooCommerceLiveChat.showError('password', 'Password must be at least 6 characters long.');
                return false;
            }

            return true;
        }
    };

    $(document).ready(function ()
    {
        WooCommerceLiveChat.init();
    });
})(jQuery);