'use strict';

function CookieHandler() {
    this.getCookie = function (cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');

        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];

            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }

            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }

        return "";
    };

    this.setCookie = function (name, value, days, path) {
        if (!path) {
            path = '/';
        }

        if (!days) {
            days = 1;
        }

        var d = new Date();
        d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
        var expires = 'expires=' + d.toUTCString();
        document.cookie = name + '=' + value + ';' + expires + ';path=' + path;
    };
}

window.cookieInformation = new CookieHandler();

window.getCookieContent = function (container) {
    this.container = container;
    this.state = false;

    this.init = function () {
        if (!this.state) {
            this.getContent();
            this.state = true;
        }
    };

    this.getContent = function () {
        var _that = this;

        var request = $.ajax({
            data: {
                'getCookieContent': 'Y'
            }
        });
        request.done(function (response) {
            container.html(response);

            _that.checkState();
        });
        request.fail(function (xhr, e) {
            console.log(e);
        });
    };

    this.checkState = function () {
        if (window.isHidePanel === 0) {
            container.removeClass('hidden');
            var footer = jQuery('.footer');
            var height = jQuery('.cookie-info').height() + 40;
            if (footer.length > 0) {
                footer.css('padding-bottom', height);
            }

        }

    };

    this.init();
};
function setOkCookie()
{
    $.ajax({
        type: 'POST',
        url: '/ajax/ajax_session.php',
        data: {
            'HidePersonalDataNotice': true
        },
        dataType: 'json',
        success: function success(data) {
            if (data.success) {
                document.getElementById('cookieInfo').classList.toggle('hidden');
                var footer = jQuery('.footer');
                if (footer.length > 0) {
                    footer.css('padding-bottom', 'inherit');
                }
            }
        }
    });
}
$(function () {
    var container = $('#cookieInfo');

    if (container.length) {
        window.getCookieContent(container);
    }
});