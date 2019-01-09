<?php

if (empty($formTypeAvaliable[$type])) {
    return '<div class="widget nl2go-widget">This form type is not available for selected form</div>';
} else {
    return '<div class="widget nl2go-widget">

    <script>
        var formUniqueCode = "'.$formUniqueCode.'";
        var n2gParams = "' . $n2gParams . '".split(",");
        var uniqueId = "'.$uniqueId.'";
        var popup = "'.$popup.'";
    </script>
    <script src="'. plugin_dir_url(__FILE__) . 'widgetView.js" id="' . ($uniqueId && !$popup ? $uniqueId : "n2g_script") . '"></script>
</div>';
}
