<?php

if (empty($formTypeAvaliable[$type])) {
    return '<div class="nl2go-widget">This form type is not available for selected form</div>';
} else {
    return '<div class="nl2go-widget"><script id="' . ($uniqueId && !$popup ? $uniqueId : "n2g_script") . '">
    !function (e, t, n, c, r, a, i) {
        e.Newsletter2GoTrackingObject = r, e[r] = e[r] || function () {
                (e[r].q = e[r].q || []).push(arguments)
            }, e[r].l = 1 * new Date, a = t.createElement(n), i = t.getElementsByTagName(n)[0], a.async = 1, a.src = c, i.parentNode.insertBefore(a, i)
    }(window, document, "script", "https://static.newsletter2go.com/utils.js", "n2g");
    n2g("create", "' . $formUniqueCode . '");
    n2g(' . $n2gParams . '' . ($uniqueId && !$popup ? ',"' . $uniqueId . '"' : "") . ');
</script></div>';
}
