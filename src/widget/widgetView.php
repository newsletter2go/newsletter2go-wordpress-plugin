<?php
return '<script id="'.($uniqueId ? $uniqueId : "n2g_script").'">
    !function (e, t, n, c, r, a, i) {
        e.Newsletter2GoTrackingObject = r, e[r] = e[r] || function () {
                (e[r].q = e[r].q || []).push(arguments)
            }, e[r].l = 1 * new Date, a = t.createElement(n), i = t.getElementsByTagName(n)[0], a.async = 1, a.src = c, i.parentNode.insertBefore(a, i)
    }(window, document, "script", "'.plugins_url(__("/utils.js"), __FILE__).'", "n2g");
    n2g("create", "'.$formUniqueCode.'");
    n2g('. $n2gParams.''.($uniqueId ? ',"'.$uniqueId.'"' : "").');
</script>';
