
             <?php
             include('./constant/connect.php');
             
             ?>
        </div>
          
    </div>

   

    <script>
    (function(){
      try {
        var targets = document.querySelectorAll('footer p, .footer p');
        for (var i = 0; i < targets.length; i++) {
          var el = targets[i];
          var txt = (el.textContent || '').toLowerCase();
          var hasMayuri = txt.indexOf('mayuri') !== -1 || el.querySelector('a[href*="mayuri"], a[href*="mayurik"]');
          if (hasMayuri) {
                          el.innerHTML = 'Copyright © 2025 MIDILINK RWANDA SMARTCARE SYSTEM';
          }
        }
      } catch(e) { /* noop */ }
    })();
    </script>
    
    
    <script src="assets/js/lib/jquery/jquery.min.js"></script>
    
    <script src="assets/js/lib/bootstrap/js/popper.min.js"></script>
    <script src="assets/js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/lib/bootstrap/js/bootstrap.js"></script>
    
    <script src="assets/js/jquery.slimscroll.js"></script>
    
    <script src="assets/js/sidebarmenu.js"></script>
    
    <script src="assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>


 <script src="assets/js/lib/sweetalert/sweetalert.min.js"></script>
    
    <script src="assets/js/lib/sweetalert/sweetalert.init.js"></script>
   

    <script src="assets/js/lib/weather/jquery.simpleWeather.min.js"></script>
    <script src="assets/js/lib/weather/weather-init.js"></script>
    <script src="assets/js/lib/owl-carousel/owl.carousel.min.js"></script>
    <script src="assets/js/lib/owl-carousel/owl.carousel-init.js"></script>


   
    
    <script src="assets/js/custom.min.js"></script>

   
     <script src="assets/js/lib/datatables/datatables.min.js"></script>
    <script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="assets/js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="assets/js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="assets/js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="assets/js/lib/datatables/datatables-init.js"></script>

    

<script>
function alphaOnly(event) {
  var key = event.keyCode;
  return ((key >= 65 && key <= 90) || key == 8);
};
                                        </script>
                                        <script>
    // WRITE THE VALIDATION SCRIPT.
    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }    
</script>
<script type="text/javascript">
function googleTranslateElementInit() {
  try {
    new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
  } catch(e) { /* noop */ }
}
</script>

<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<style>
       .goog-logo-link {
    display:none !important;
} 
    
.goog-te-gadget{
    color: transparent;
}
.goog-te-gadget .goog-te-combo {
    margin: 0px 0;
        padding: 8px;
}
#google_translate_element{
        padding-top: 14px;
}
</style>
<script>
// Ensure sidebar leaf links navigate even if plugins intercept clicks
(function(){
  try {
    document.addEventListener('click', function(e){
      var a = e.target.closest('#sidebarnav a');
      if (!a) return;
      var hrefAttr = a.getAttribute('href');
      var isToggle = a.classList.contains('has-arrow');
      if (!hrefAttr || hrefAttr === '#') return;
      // Resolve absolute URL then sanitize
      var href = a.href;
      // Remove any hash fragment
      if (href.indexOf('#') !== -1) href = href.split('#')[0];
      // If URL accidentally contains duplicated placeholder paths, keep first occurrence only
      var idx = href.indexOf('placeholder.php?');
      if (idx !== -1) {
        var base = href.substring(0, idx + 'placeholder.php'.length);
        var query = href.substring(idx + 'placeholder.php'.length);
        // If another 'http' sneaked in the query, cut before it
        var httpIdx = query.indexOf('http');
        if (httpIdx !== -1) query = query.substring(0, httpIdx);
        href = base + query;
      }
      // Special-cases to force clean URLs without fragment
      if (/placeholder\.php\?title=Expiry%20Alerts#?$/.test(hrefAttr)) {
        href = 'placeholder.php?title=Expiry%20Alerts';
      }
      if (/placeholder\.php\?title=Stock%20Movements#?$/.test(hrefAttr)) {
        href = 'stock_movements.php';
      }
      if (/placeholder\.php\?title=Low%20Stock%20Alerts#?$/.test(hrefAttr)) {
        href = 'low_stock_alerts.php';
      }
      // Generic: if any link under sidebar ends with a '#', drop it
      if (/#?$/.test(hrefAttr) && hrefAttr[hrefAttr.length-1] === '#') {
        href = hrefAttr.slice(0, -1);
      }
      if (isToggle) return; // collapse/expand only
      e.stopImmediatePropagation();
      e.preventDefault();
      window.location.href = href;
    }, true);
  } catch(e) {}
})();
</script>

<script>
// On load: if current URL ends with '#', replace it with the clean URL
(function(){
  try {
    if (/#$/.test(window.location.href)) {
      var clean = window.location.href.replace(/#$/, '');
      if (clean !== window.location.href) window.history.replaceState(null, document.title, clean);
    }
  } catch(e) {}
})();
</script>

<script>
// Safety: always hide any preloader/spinner in case a JS error blocks fadeOut
(function(){
  try {
    function hidePreloader(){
      var p = document.querySelector('.preloader');
      if (p) { p.style.display = 'none'; }
    }
    if (document.readyState !== 'loading') hidePreloader();
    else document.addEventListener('DOMContentLoaded', hidePreloader);
    // Failsafe timeout
    setTimeout(hidePreloader, 1500);
  } catch(e) {}
})();
</script>

<script>
// Global link sanitizer for sidebar only: navigate leaf links; let plugin handle toggles
(function(){
  try {
    document.addEventListener('click', function(e){
      var a = e.target.closest('#sidebarnav a[href]');
      if (!a) return;
      var hrefAttr = a.getAttribute('href');
      if (!hrefAttr || hrefAttr === '#') return;
      if (a.classList.contains('has-arrow')) return; // allow submenu toggle
      var href = a.getAttribute('href').replace(/#$/, '');
      e.preventDefault();
      // allow any menu plugin to finish first, then navigate
      setTimeout(function(){ window.location.assign(href); }, 0);
    }, false);

    // On load: clean any trailing # in sidebar links
    function cleanSidebarHrefs(){
      var links = document.querySelectorAll('#sidebarnav a[href]');
      links.forEach(function(a){
        var h = a.getAttribute('href');
        if (h && /#$/.test(h)) a.setAttribute('href', h.replace(/#$/, ''));
      });
    }
    if (document.readyState !== 'loading') cleanSidebarHrefs();
    else document.addEventListener('DOMContentLoaded', cleanSidebarHrefs);

    // No extra parent toggle binding here; MetisMenu handles it
  } catch(e) {}
})();
</script>
</body>
</html>





