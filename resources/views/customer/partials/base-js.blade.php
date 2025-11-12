<script>
  // Dynamic spacer: add bottom padding only when content exceeds viewport so fixed footer doesn't overlap
  (function () {
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
    var observer = new MutationObserver(function(){ adjustSpacer(); });
    window.addEventListener('DOMContentLoaded', function(){
      var app = document.querySelector('.app');
      if (app) observer.observe(app, { childList: true, subtree: true });
    });
  })();

  // Lightweight notification poller for real-time-ish updates
  (function(){
    var bell = null;
    var badge = null;
    function ensureBellRef(){
      bell = document.querySelector('#notifBell');
      if (bell && !badge) {
        badge = document.createElement('span');
        badge.className = 'notif-dot';
        bell.appendChild(badge);
      }
    }
    function setDot(show){
      ensureBellRef();
      if (badge) {
        badge.style.display = show ? 'block' : 'none';
      }
    }
    async function poll(){
      try {
        ensureBellRef();
        const res = await fetch('{{ route('customer.notifications') }}', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();
        // Show dot if there are open orders or a recent change within last 2 minutes
        var open = Number(data.open_orders || 0);
        var changedAt = Date.parse(data.last_change_at || 0);
        var now = Date.now();
        var recent = (now - changedAt) < (2 * 60 * 1000);
        setDot(open > 0 || recent);
      } catch(e) {
        // silent
      }
    }
    window.addEventListener('DOMContentLoaded', function(){
      poll();
      setInterval(poll, 10000); // every 10s
    });
  })();
  
  // Perpanjang latar gradien di belakang konten hingga persis di atas label "Layanan"
  (function(){
    function adjustGradientHeight(){
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
      } catch(e) { /* ignore */ }
    }
    window.addEventListener('DOMContentLoaded', adjustGradientHeight);
    window.addEventListener('load', adjustGradientHeight);
    window.addEventListener('resize', adjustGradientHeight);
  })();
</script>
