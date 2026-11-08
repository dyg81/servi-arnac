(function ($) {
    'use strict';

    var notificationRoot = document.querySelector('[data-notifications-url]');

    if (!notificationRoot) {
        return;
    }

    var notificationBell = document.querySelector('#notificationBell');
    var notificationsBlock = document.querySelector('#notificationsBlock');
    var notificationsType = notificationRoot.getAttribute('data-notifications-type');

    function toCount(value) {
        var count = parseInt(value, 10);

        return isNaN(count) || count < 0 ? 0 : count;
    }

    function updateBell(count) {
        while (notificationBell.firstChild) {
            notificationBell.removeChild(notificationBell.firstChild);
        }

        var icon = document.createElement('i');
        icon.className = 'fas fa-bell mt-1';
        notificationBell.appendChild(icon);

        if (count > 0) {
            var badge = document.createElement('span');
            badge.className = 'badge badge-danger navbar-badge';
            badge.textContent = String(count);
            notificationBell.appendChild(badge);
        }
    }

    function updateNotificationItem(item, count) {
        var mediaBody = item.querySelector('.media-body');
        var oldMessage = mediaBody.querySelector('.notification-count');

        if (oldMessage) {
            oldMessage.remove();
        }

        item.hidden = count === 0;

        if (count > 0) {
            var message = document.createElement('em');
            message.className = 'text-xs text-muted notification-count';
            message.textContent = count + ' ' + item.getAttribute('data-notification-message');
            mediaBody.appendChild(message);
        }
    }

    function updateBlock(count) {
        notificationsBlock.hidden = count === 0;

        if (count === 0) {
            notificationsBlock.classList.remove('show');
        }

        updateBell(count);
    }

    function updateSingleNotification(data) {
        var count = toCount(data[0]);

        updateNotificationItem(notificationRoot, count);
        updateBlock(count);
    }

    function updateSalaNotifications(data) {
        var counts = [toCount(data[0]), toCount(data[1]), toCount(data[2])];
        var items = notificationsBlock.querySelectorAll('[data-notification-index]');
        var total = 0;

        Array.prototype.forEach.call(items, function (item) {
            var index = toCount(item.getAttribute('data-notification-index'));
            var count = counts[index] || 0;

            updateNotificationItem(item, count);
            total += count;
        });

        var vitrinas = counts[1];
        var investigaciones = counts[0];
        var cartas = counts[2];
        var divisor1 = document.querySelector('#notificacionesDivisor1');
        var divisor2 = document.querySelector('#notificacionesDivisor2');

        divisor1.hidden = !(vitrinas > 0 && (investigaciones > 0 || cartas > 0));
        divisor2.hidden = !(investigaciones > 0 && cartas > 0);

        updateBlock(total);
    }

    function checkNewInfo() {
        $.ajax({
            url: notificationRoot.getAttribute('data-notifications-url'),
            type: 'GET',
            dataType: 'JSON',
            success: function (data) {
                if (notificationsType === 'sala') {
                    updateSalaNotifications(data);
                } else {
                    updateSingleNotification(data);
                }
            },
            complete: function () {
                window.setTimeout(checkNewInfo, 300000);
            }
        });
    }

    window.setTimeout(checkNewInfo, 1000);
})(jQuery);
