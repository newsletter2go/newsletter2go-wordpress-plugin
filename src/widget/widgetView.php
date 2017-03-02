<?php

if (empty($formTypeAvaliable[$type])) {
    return '<div class="widget nl2go-widget">This form type is not available for selected form</div>';
} else {
    return '<div class="widget nl2go-widget"><script id="' . ($uniqueId && !$popup ? $uniqueId : "n2g_script") . '">
    n2g(' . $n2gParams . '' . ($uniqueId && !$popup ? ',"' . $uniqueId . '"' : "") . ');
</script></div>';
}
