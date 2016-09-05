
<form action="admin.php?page=n2go-api" method="POST">
<div class="n2go-section">
    <img src="<?=plugins_url(__("/lib/banner_wordpress_newsletter2go_COM.png"), __FILE__)?>" width="91%" class="n2go_logo">
    <div class="n2go-block main-block" style="width:91%; margin-bottom: 30px;"> So benutzen Sie die neuen Anmeldeformulare: Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</div>
</div>

<div class="n2go-section">
        <div class="n2go-block50 main-block">
            <div class="panel">
                <div class="panel-heading text-center">
                    <h3><?=__("Newsletter2Go Wordpress Plugin")?></h3>
                </div>
                <div class="panel-body">
                    <div class="n2go-row">
                        <div class="n2go-block50"><span><?=__("Connect to Newsletter2Go")?></span></div>
                        <div class="n2go-block25">
                            <?php if ($forms === false){ ?>
                                <div class="n2go-btn">
                                    <input type="hidden" name="apiKey" placeholder="" value="<?php echo $apiKey; ?>" style="width:300px" readonly>
                                    <a href="<?php echo $connectUrl; ?>" target="_blank" style="padding:5px"><span class="fa fa-plug"></span> <span><?=__("Login or Create Account")?></span></a>
                                </div>
                            <?php } else { ?>
                                <span class="n2go-label-success"> <span class="fa fa-check margin-right-5"></span>
							<span><?=__("Successfully connected")?></span></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="n2go-row">
                        <?php if ($forms !== false){ ?>
                        <div class="n2go-block50">
                            <span><?=__("Choose the connected subscribe form")?></span>
                        </div>
                        <div class="n2go-block25">
                            <select id="formUniqueCode" class="n2go-select" name="formUniqueCode">
                                <?php if (!empty($forms)){ ?>
                                    <?php foreach ($forms as $form) { ?>
                                        <option value="<?php echo $form['hash']; ?>" <?php if ($form['hash'] == $formUniqueCode) { echo "selected"; }?>><?php echo $form['name']; ?></option>
                                    <?php } ?>
                                <?php } else { ?>
                                    <option value=""></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($forms !== false){ ?>
                <div class="n2go-row">
                    <div class="n2go-block50"><span><?=__("Configure your Wordpress widget")?></span></div>
                    <div class="n2go-block25">
                        <label for="formBackgroundColor"><?=__("Form background color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputFBC" name="form.background-color" type="text" placeholder="" value="FFFFFF" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputFBC" class="input-group-btn jscolor{valueElement:'valueInputFBC', styleElement:'styleInputFBC'}">
                            </button>
                        </div>
                        <label for="labelColor"><?=__("Label text color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputLC" name="label.color" type="text" placeholder="" value="222222" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputLC" class="input-group-btn jscolor{valueElement:'valueInputLC', styleElement:'styleInputLC'}">
                            </button>
                        </div>
                        <label for="textColor"><?=__("Input Text color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputIC" name="input.color" type="text" placeholder="" value="222222" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputIC" class="input-group-btn jscolor{valueElement:'valueInputIC', styleElement:'styleInputIC'}">
                            </button>
                        </div>
                        <label for="borderColor"><?=__("Input border color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputIBrC" name="input.border-color" type="text" placeholder="" value="CCCCCC" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputIBrC" class="input-group-btn jscolor{valueElement:'valueInputIBrC', styleElement:'styleInputIBrC'}">
                            </button>
                        </div>
                        <label for="backgroundColor"><?=__("Input background color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputIBC" name="input.background-color" type="text" placeholder="" value="FFFFFF" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputIBC" class="input-group-btn jscolor{valueElement:'valueInputIBC', styleElement:'styleInputIBC'}">
                            </button>
                        </div>
                        <label for="btnTextColor"><?=__("Button text color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputBC" type="text" name="button.color" placeholder="" value="FFFFFF" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputBC" class="input-group-btn jscolor{valueElement:'valueInputBC', styleElement:'styleInputBC'}">
                            </button>
                        </div>
                        <label for="btnBackgroundColor"><?=__("Button background color")?></label>
                        <div class="n2go-cp input-group">
                            <span class="n2go-input-group-addon">#</span><input id="valueInputBBC" type="text" name="button.background-color" placeholder="" value="00BAFF" class="n2go-colorField form-control n2go-text-right">
                            <button id="styleInputBBC" class="input-group-btn jscolor{valueElement:'valueInputBBC', styleElement:'styleInputBBC'}">
                            </button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    <?php if ($forms !== false){ ?>
        <div class="n2go-block50 main-block">
            <div class="panel">
                <div class="panel-heading text-center">
                    <h3><?=__("This is how your form will look like")?></h3>
                </div>
                <div class="panel-body">
                    <ul id="n2gButtons" class="nav nav-tabs">
                        <li id="btnShowPreview" class="active"><?=__("Preview")?></li>
                        <li id="btnShowConfig" class=""><?=__("Source")?></li>
                    </ul>
                    <!-- Tab panes-->
                    <div id="preview-form-panel" class="preview-pane">
                        <div id="widgetPreview">
                            <?php if(!isset($errorMessage)){ ?>
                                <script id="n2g_script">
                                </script>
                            <?php } else { ?>
                                <h3 class="n2go-error-general"><?=__($errorMessage) ?></h3>
                            <?php } ?>
                        </div>
                        <div id="nl2gStylesConfig" class="preview-pane">
                            <textarea id="widgetStyleConfig" name="widgetStyleConfig"><?php echo $nl2gStylesConfigObject; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="n2go-section">
    <br />
    <input type="submit" value="<?=__("Save settings")?>" class="save-btn button button-primary n2go-btn" name="saveApiKey"/>
</div>

</form>



