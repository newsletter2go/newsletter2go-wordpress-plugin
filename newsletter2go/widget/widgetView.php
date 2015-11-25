
<div id="n2goResponseArea">
    <form method="post" id="n2goForm">
        <?php foreach ($attributes as $v): ?>
            <?php echo $v['realName']; ?><br/>
            <input type="<?=($v['name'] == 'email' ? 'email' : 'text')?>" name="<?php echo $v['name']; ?>" required /><br />
        <?php endforeach; ?>
        <br />
        <input name="action" type="hidden" value="n2go_subscribe" />
        <input id="n2goButton" type="button" value="<?php echo $texts['buttonText']; ?>" onClick="n2goAjaxFormSubmit();" />
    </form>
</div>
