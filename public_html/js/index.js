$(document).ready(function ($) {
    $('#tel').mask("+7 (000) 000-00-00");

    //Проверка на заполненность обязательных полей
    $('*[data-rule-required="true"]').on('blur change', function () {
        var count = 0;
        var required = $('*[data-rule-required="true"]').length;

        $('*[data-rule-required="true"]').each(function () {
            if ($(this).val() != '' || ($(this).is(':checked') && $(this).attr('id') == 'js-check-info')) {
                count++
            }
        })

        if (count >= required && $('#form-to-fill').valid()) {
            $('#create-draft').removeAttr('disabled')
        } else {
            $('#create-draft').attr('disabled', true);
        }
    })

    $('.activate-policy-check').on('change', function () {
        if ($(this).is(':checked')) {
            $('#activate-policy').removeAttr('disabled')
        } else {
            $('#activate-policy').attr('disabled', true)
        }
    })
    $('[name=PolicyId]').on('change', function () {
        $('#submit-button').removeAttr('disabled');
    })
    $('.popup-open').click(function () {
        window.popupWidget.open('name');
    })
    $('.link-dwnl').click(function () {
        window.preloader.loaderShow();
        setTimeout(window.preloader.loaderHide, 5000);
    })
    $('#form-to-fill').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        if (form.valid()) {
            window.preloader.loaderShow();
            let data = form.serialize();
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'JSON',
                success: function (data) {
                    if (data.success === true) {
                        window.location.href = data.url
                    } else {
                        window.preloader.loaderHide();
                        if (typeof data.message != 'undefined') {
                            $('.error-message').html(data.message);
                            $('.js-popup-close').click(function () {
                                window.location.href = '/policies/';
                            })
                            $('.mfp-bg').click(function () {
                                window.location.href = '/policies/';
                            })
                        }
                        window.popupWidget.open('error');
                    }
                },
                error: function () {
                    window.preloader.loaderHide();
                    window.popupWidget.open('error');
                }
            });
        }
    })
    let name = document.querySelector('.js-language-convert'); // Получаем input
    let regex = /[^A-Za-z\-\s]/g; // регулярка только цифры

    if (name) {
        name.oninput = function () {
            this.value = this.value.replace(regex, '');
        }
    }

    //разрешен ввод только цифр
    $('.d-replace').bind('keyup', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    //показать кнопку если заполнены все поля
    $('.rf-input').bind('keyup', function () {
        let filled = $('.rf-input').filter(function () {
            return !this.value;
        })
        if (filled.length === 0) {
            $('#do-form').prop("disabled", false);
        } else {
            $('#do-form').prop("disabled", true);
        }
    });

    //отправка запроса на формирование заявки
    $('#do-form').bind('click', function () {
        $('#formresponse').html('<div class="preloader__content" style="margin-top: 10px;"><div class="preloader__image"></div><div class="preloader__text js-loader-text"></div></div>');
        $('#do-form').prop("disabled", true).addClass('btn-disabled2');
        $.post('/refund/form', {
            'email': $('#email').val(),
            'original-email': $('#original-email').val(),
            'police-number': $('#police-number').val(),
            'phone': $('#phone').val(),
            'pass-ser': $('#pass-ser').val(),
            'pass-no': $('#pass-no').val(),
            'pass-issue-place': $('#pass-issue-place').val(),
            'pass-issue-date': $('#pass-issue-date').val(),
            'bank-name': $('#bank-name').val(),
            'bank-corr-account': $('#bank-corr-account').val(),
            'bank-bik': $('#bank-bik').val(),
            'bank-account': $('#bank-account').val(),
            'fio': $('#fio').val(),
            'operation': $('#operation').val(),
            'key': $('#key').val()
        }, function (re) {
            $('#do-form').prop("disabled", false).removeClass('btn-disabled2');
            $('#formresponse').html(re);
            $('#operation').val('update');
            $('#sendmail').prop("disabled", false).removeClass('btn-disabled2');
            $('#do-sms').prop("disabled", false).removeClass('btn-disabled2');
        })
    });

    //отправка sms
    $('#do-sms').bind('click', function () {
        $.get('/refund/sendsms', {phone: $('#phone').val()});
        $('#smscontainer').html('<div class="sms-input-container"><input id="num" class="input__control"></div>\n' +
            '                <div class="sms-info-container">\n' +
            '                    <div class="sms-status-text">введите 4-х значный код из СМС, отправленный на номер ' + $('#phone').val() + '</div>\n' +
            '                    <div class="sms-error-text"></div>\n' +
            '                    <div class="sms-repeat"><span class="sms-time">04:59</span> Отправить код в СМС повторно</div>\n' +
            '                </div><button id="send-sms" class="btn btn--middle rf-btn" style="margin-top: 20px;">Отправить документы на возврат аванса</button>');

        //set timer
        let start = Date.now(),
            diff,
            minutes,
            seconds,
            timer = function () {
                diff = 30 - (((Date.now() - start) / 1000) | 0);
                minutes = (diff / 60) | 0;
                seconds = (diff % 60) | 0;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                $('.sms-time').html(minutes + ":" + seconds);
                if (diff <= 0) {
                    //повторная отправка смс
                    clearInterval(smsint);
                    $('.sms-repeat').addClass('sms-repeat-red');
                    $('.sms-repeat').bind('click', function () {
                        $.get('/refund/sendsms', {phone: $('#phone').val()});
                        $('#num').prop("disabled", false);
                        $('#send-sms').prop("disabled", false).removeClass('btn-disabled2');
                        $('.sms-repeat').unbind().hide();
                        $('.sms-error-text').hide();
                    });
                }
            };
        timer();
        var smsint = setInterval(timer, 1000);

        //отправка заявления
        let sms_err_count = 0;
        $('#send-sms').bind('click', function () {
            $.get('/refund/checksms', {num: $('#num').val()}, function (re) {
                if (re == 'OK') {
                    $('#operation').val('accept');
                    $('#refund').submit();
                } else {
                    sms_err_count++;
                    if (sms_err_count < 4) {
                        $('.sms-error-text').html('Не верно введены цифры из СМС');
                    } else {
                        $('.sms-error-text').html('Попробуйте позже');
                        $('#num').prop("disabled", true);
                        $('#send-sms').prop("disabled", true).addClass('btn-disabled2');
                    }
                }
            });
        });
    });

    //отправка письма в фоне
    $('#sendmail').bind('click', function () {
        let formemail = $('#formemail').val();
        $('#sendmail').prop("disabled", true).addClass('btn-disabled2');
        $('#preloadercont').html('<div class="preloader__content" style="margin-top: 10px;"><div class="preloader__image"></div><div class="preloader__text js-loader-text"></div></div>');
        $.post('/refund/mail', {
            'email': formemail,
            'key': $('#key').val()
        }, function (re) {
            $('#preloadercont').html(re);
            $('#do-sms').prop("disabled", false).removeClass('btn-disabled2');
        });
    });

});

function deletePolicy(id) {
    $.ajax({
        type: "POST",
        url: '/api/deletepolicy/',
        data: 'policyId=' + id,
        dataType: 'JSON',
        success: function (data) {
            if (data.success === true) {
                window.location.href = data.url
            } else {
                window.popupWidget.open('error');
            }
        },
        error: function () {
            window.popupWidget.open('error');
        }
    });
}