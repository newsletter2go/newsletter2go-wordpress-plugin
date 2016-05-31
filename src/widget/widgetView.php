<?php
/**
 * @var string $formUniqueCode
 * @var string $n2gConfig
 * @var array $formType
*/
?>

<script id="n2g_script">
    !function(e,t,n,c,r,a,i){e.Newsletter2GoTrackingObject=r,e[r]=e[r]||function(){(e[r].q=e[r].q||[]).push(arguments)},e[r].l=1*new Date,a=t.createElement(n),i=t.getElementsByTagName(n)[0],a.async=1,a.src=c,i.parentNode.insertBefore(a,i)}(window,document,"script","//static.newsletter2go.com/utils.js","n2g");
    n2g('create','<?=$formUniqueCode?>');

    if ('<?=$formType['type']?>' == 'plugin') {
        n2g('subscribe:createForm', <?=$n2gConfig?>);
    } else if ('<?=$formType['type']?>' == 'popup'){
        n2g('subscribe:createPopup', <?=$n2gConfig?>, 10);
    } else {
        alert(
            'Sorry, wrong Shortcode syntax or argument.\n\n'
            + "\tit must be like one of the following:\n"
            + '\t[n2go-form type="plugin]"\n'
            + '\t[n2go-form type="popup]"\n\n'
        );
    }
</script>