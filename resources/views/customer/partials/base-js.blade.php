<script>
    // Dynamic spacer: add bottom padding only when content exceeds viewport so fixed footer doesn't overlap
    (function() {
        function adjustSpacer() {
            try {
                var app = document.querySelector('.app');
                var footer = document.querySelector('.footer');
                if (!app || !footer) return;
                var footerH = footer.offsetHeight || 0;
                var viewportH = window.innerHeight;
                var appScrollH = app.scrollHeight;
                // If content height is at least viewport (full or more), add padding for footer
                if (appScrollH >= viewportH) {
                    app.style.paddingBottom = (footerH + 8) + 'px';
                } else {
                    // Otherwise keep content snug without extra scroll gap
                    app.style.paddingBottom = '0px';
                }
            } catch (e) {
                // noop
            }
        }
        window.addEventListener('DOMContentLoaded', adjustSpacer);
        window.addEventListener('load', adjustSpacer);
        window.addEventListener('resize', adjustSpacer);
        // Mutation observer to re-adjust when dynamic content changes
        var observer = new MutationObserver(function() {
            adjustSpacer();
        });
        window.addEventListener('DOMContentLoaded', function() {
            var app = document.querySelector('.app');
            if (app) observer.observe(app, {
                childList: true,
                subtree: true
            });
        });
    })();
    (function() {
        var lastNotified = 0;

        function notify(title, body, url) {
            try {
                if (Notification.permission === 'granted') {
                    var n = new Notification(title || '', {
                        body: body || '',
                        icon: '/icons/solusita1.jpg'
                    });
                    n.onclick = function() {
                        try {
                            window.location.href = url || '/customer/home';
                        } catch (e) {}
                    };
                }
            } catch (e) {}
        }
        async function ensureSubscription() {
            try {
                if (!('serviceWorker' in navigator)) return;
                var reg = await navigator.serviceWorker.getRegistration('/customer/');
                if (!reg) reg = await navigator.serviceWorker.register('/service-worker.js', {
                    scope: '/customer/'
                });
                if (!('pushManager' in reg)) return;
                var keyRes = await fetch('{{ route('customer.push.key') }}', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!keyRes.ok) return;
                var keyJson = await keyRes.json();
                var vapid = (keyJson && keyJson.public_key) || '';
                var sub = await reg.pushManager.getSubscription();
                if (!sub && vapid) {
                    var converted = (function(base64) {
                        var padding = '='.repeat((4 - base64.length % 4) % 4);
                        var b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
                        var raw = window.atob(b64);
                        var out = new Uint8Array(raw.length);
                        for (var i = 0; i < raw.length; i++) {
                            out[i] = raw.charCodeAt(i);
                        }
                        return out;
                    })(vapid);
                    sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: converted
                    });
                }
                if (sub) {
                    await fetch('{{ route('customer.push.subscribe') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            subscription: sub
                        })
                    });
                }
            } catch (e) {}
        }
        window.addEventListener('DOMContentLoaded', function() {
            try {
                if (Notification && Notification.permission === 'default') {
                    Notification.requestPermission().then(function(p) {
                        if (p === 'granted') {
                            ensureSubscription();
                        }
                    });
                } else {
                    ensureSubscription();
                }
            } catch (e) {}
        });
        async function pollAndNotify() {
            try {
                const res = await fetch('{{ route('customer.notifications') }}', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return;
                const data = await res.json();
                var changedAt = Date.parse(data.last_change_at || 0);
                var now = Date.now();
                var recent = (now - changedAt) < (2 * 60 * 1000);
                if (recent && changedAt && changedAt !== lastNotified) {
                    lastNotified = changedAt;
                    var m = {
                        'pending': 'Order telah kami terima, Menunggu Verifikasi',
                        'scheduled': 'Cleaner Segera kelokasi anda',
                        'in_progress': 'Pengerjaan dilakukan',
                        'completed': 'Order telah selesai dikerjakan'
                    };
                    var body = m[(data && data.last_status) || ''] || 'Ada pembaruan pada pesanan Anda';
                    notify('Order diperbarui', body, '/customer/home');
                }
            } catch (e) {}
        }
        window.addEventListener('DOMContentLoaded', function() {
            setInterval(pollAndNotify, 10000);
        });
    })();
    (function() {
        var overlay = document.createElement('div');
        overlay.className = 'page-spinner-overlay';
        var spinner = document.createElement('div');
        spinner.className = 'page-spinner';
        overlay.appendChild(spinner);

        function show() {
            overlay.style.display = 'flex';
        }

        function hide() {
            overlay.style.display = 'none';
        }
        window.addEventListener('DOMContentLoaded', function() {
            document.body.appendChild(overlay);
            hide();
        });
        window.addEventListener('pageshow', hide);
        window.addEventListener('load', hide);
        window.addEventListener('beforeunload', function() {
            show();
        });
        document.addEventListener('click', function(e) {
            var a = e.target.closest ? e.target.closest('a') : null;
            if (!a) return;
            var href = a.getAttribute('href') || '';
            var tgt = a.getAttribute('target') || '_self';
            if (tgt && tgt !== '_self') return;
            if (href === '' || href.charAt(0) === '#') return;
            show();
        });
        document.addEventListener('submit', function() {
            show();
        });
    })();

    // Lightweight notification poller for real-time-ish updates
    (function() {
        var bell = null;
        var badge = null;

        function ensureBellRef() {
            bell = document.querySelector('#notifBell');
            if (bell && !badge) {
                badge = document.createElement('span');
                badge.className = 'notif-dot';
                bell.appendChild(badge);
            }
        }

        function setDot(show) {
            ensureBellRef();
            if (badge) {
                badge.style.display = show ? 'block' : 'none';
            }
        }
        async function poll() {
            try {
                ensureBellRef();
                const res = await fetch('{{ route('customer.notifications') }}', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return;
                const data = await res.json();
                // Show dot if there are open orders or a recent change within last 2 minutes
                var open = Number(data.open_orders || 0);
                var changedAt = Date.parse(data.last_change_at || 0);
                var now = Date.now();
                var recent = (now - changedAt) < (2 * 60 * 1000);
                setDot(open > 0 || recent);
            } catch (e) {
                // silent
            }
        }
        window.addEventListener('DOMContentLoaded', function() {
            poll();
            setInterval(poll, 10000); // every 10s
        });
    })();

    // Perpanjang latar gradien di belakang konten hingga persis di atas label "Layanan"
    (function() {
        function adjustGradientHeight() {
            try {
                var app = document.querySelector('.app');
                var bg = document.querySelector('.bg-extend');
                var title = document.querySelector('.content .section-title');
                if (!app || !bg || !title) return;
                // Hitung tinggi dari atas .app ke posisi label pertama ("Layanan")
                var appRect = app.getBoundingClientRect();
                var titleRect = title.getBoundingClientRect();
                var height = (titleRect.top - appRect.top) - 6; // beri jarak kecil
                if (height < 0) height = 0;
                bg.style.height = height + 'px';
            } catch (e) {
                /* ignore */
            }
        }
        window.addEventListener('DOMContentLoaded', adjustGradientHeight);
        window.addEventListener('load', adjustGradientHeight);
        window.addEventListener('resize', adjustGradientHeight);
    })();
</script>
