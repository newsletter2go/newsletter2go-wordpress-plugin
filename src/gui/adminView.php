<form action="admin.php?page=n2go-api" method="POST">
    <div class="n2go-section">
        <img
                src="<?= plugins_url(__("/lib/banner_wordpress_newsletter2go_COM.png", NEWSLETTER2GO_TEXTDOMAIN), __FILE__) ?>"
                width="91%" class="n2go_logo">
        <div class="n2go-block main-block" style="width:91%; margin-bottom: 30px;">
            <div class="panel">
                <div class="panel-heading text-center">
                    <h3>So benutzen Sie die Anmeldeformulare</h3>
                </div>
                <div class="n2go-row">
                    <div class="n2go-block50">
                        <h4>als Widget</h4>
                        <p>Unter Design -> Widgets können Sie ihr konfiguriertes Formular bequem in ihre
                            Seitenleisten und Menüs einfügen</p>
                        <br>

                        <h4><div class="dashicons dashicons-info"></div> Tipps und Tricks</h4>
                        <p>In unserem <a href="https://hilfe.newsletter2go.com">Hilfebereich</a> finden Sie hilfreiche Anleitungen zu unserer Software und deren erfolgreiche Nutzung.</p>
                        <p>Wie sich unsere Formulare weiter über die "Source" (Rechter Tab) individualisieren lassen erfahren Sie <a href="https://hilfe.newsletter2go.com/empfanger-verwalten/anmeldeformular/wie-kann-ich-das-anmeldeformular-verwenden-einbetten-und-anpassen.html">hier</a></p>
                    </div>

                    <div class="n2go-block50">
                        <h4>in Beiträgen und Seiten</h4>
                        <p>Über den Shortcode <code>[newsletter2go]</code> können Sie ihr
                            konfiguriertes Anmeldeformular in allen Seiten und Beiträgen über den Editor einbinden.<br/>
                            <br/>
                            Durch den Parameter <code>[newsletter2go form_type=subscribe]</code> bzw. <code>[newsletter2go form_type=unsubscribe]</code>
                            erzeugen Sie ein An- bzw. Abmeldeformular, soweit dieser Formular-Typ im Newsletter2Go-System ebenfalls aktiviert wurde.
                            Standardmäßig wird ein Anmeldeformular erzeugt.<br/><br/>
                            Mit der zusätzlichen Option <code>[newsletter2go type=popup]</code> wird aus dem
                            eingebetten Formular ein Popup welches auf der spezifischen Seite eingeblendet wird.<br/><br/>
                            Um dem Formular eine Überschrift/Titel zu geben können Sie den zusätzlichen Parameter <code>[newsletter2go title=mein Titel]</code>
                            nutzen.
                        </p>
                    </div>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>

    </div>


    <div class="n2go-section">
        <div class="n2go-block50 main-block">
            <div class="panel">
                <div class="panel-heading text-center">
                    <h3><?= __("Newsletter2Go Wordpress Plugin", NEWSLETTER2GO_TEXTDOMAIN) ?></h3>
                </div>
                <div class="panel-body">
                    <div class="n2go-row">
                        <div class="n2go-block50">
                            <span><?= __("Connect to Newsletter2Go", NEWSLETTER2GO_TEXTDOMAIN) ?></span></div>
                        <div class="n2go-block25">
                            <?php if (empty($forms)) { ?>
                                <div class="n2go-btn">
                                    <input type="hidden" name="apiKey" placeholder="" value="<?php echo $apiKey; ?>"
                                           style="width:300px" readonly>
                                    <a href="<?php echo $connectUrl; ?>" target="_blank" style="padding:5px"><span
                                                class="fa fa-plug"></span>
                                        <span><?= __("Login or Create Account", NEWSLETTER2GO_TEXTDOMAIN) ?></span></a>
                                </div>
                            <?php } else { ?>
                                <span class="n2go-label-success"> <span class="fa fa-check margin-right-5"></span>
							<span><?= __("Successfully connected", NEWSLETTER2GO_TEXTDOMAIN) ?></span></span>
                                <br><br>
                                <div>
                                    <input type="submit" value="<?= __("Disconnect", NEWSLETTER2GO_TEXTDOMAIN) ?>"
                                           class="save-btn button" name="resetValues"/>
                                </div>
                            <?php } ?>
                        </div>
                        <?php

                        if ($this->apiErrorMessage) {
                            ?>
                            <div class="n2go-row">
                                <p class="n2go-error-general"><?= $this->apiErrorMessage ?></p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="n2go-row">
                        <?php if ($forms !== false) { ?>
                            <div class="n2go-block50">
                                <span><?= __("Choose the connected form", NEWSLETTER2GO_TEXTDOMAIN) ?></span>
                            </div>
                            <div class="n2go-block25">
                                <select id="formUniqueCode" class="n2go-select" name="formUniqueCode">
                                    <option value="" disabled selected><?= __("-- please select --", NEWSLETTER2GO_TEXTDOMAIN) ?></option>
                                    <?php if (!empty($forms)) { ?>
                                        <?php foreach ($forms as $form) { ?>
                                            <option
                                                    value="<?php echo $form['hash']; ?>" <?php if ($form['hash'] == $formUniqueCode) {
                                                echo "selected";
                                            } ?>><?php echo $form['name']; ?></option>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <option value=""></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($formUniqueCode) { ?>
                    <div class="n2go-row">
                        <div class="n2go-block50">
                            <span><?= __("Configure the styling of your form", NEWSLETTER2GO_TEXTDOMAIN) ?></span></div>
                        <div class="n2go-block25">
                            <label
                                    for="formBackgroundColor"><?= __("Form background color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputFBC"
                                                                                    name="container.background-color"
                                                                                    type="text" placeholder=""
                                                                                    value="FFFFFF"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputFBC"
                                        class="input-group-btn jscolor{valueElement:'valueInputFBC', styleElement:'styleInputFBC'}">
                                </button>
                            </div>
                            <label for="labelColor"><?= __("Label text color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputLC" name="label.color"
                                                                                    type="text" placeholder=""
                                                                                    value="222222"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputLC"
                                        class="input-group-btn jscolor{valueElement:'valueInputLC', styleElement:'styleInputLC'}">
                                </button>
                            </div>
                            <label for="textColor"><?= __("Input Text color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputIC" name="input.color"
                                                                                    type="text" placeholder=""
                                                                                    value="222222"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputIC"
                                        class="input-group-btn jscolor{valueElement:'valueInputIC', styleElement:'styleInputIC'}">
                                </button>
                            </div>
                            <label for="borderColor"><?= __("Input border color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputIBrC"
                                                                                    name="input.border-color"
                                                                                    type="text" placeholder=""
                                                                                    value="CCCCCC"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputIBrC"
                                        class="input-group-btn jscolor{valueElement:'valueInputIBrC', styleElement:'styleInputIBrC'}">
                                </button>
                            </div>
                            <label
                                    for="backgroundColor"><?= __("Input background color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputIBC"
                                                                                    name="input.background-color"
                                                                                    type="text" placeholder=""
                                                                                    value="FFFFFF"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputIBC"
                                        class="input-group-btn jscolor{valueElement:'valueInputIBC', styleElement:'styleInputIBC'}">
                                </button>
                            </div>
                            <label for="btnTextColor"><?= __("Button text color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputBC" type="text"
                                                                                    name="button.color" placeholder=""
                                                                                    value="FFFFFF"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputBC"
                                        class="input-group-btn jscolor{valueElement:'valueInputBC', styleElement:'styleInputBC'}">
                                </button>
                            </div>
                            <label
                                    for="btnBackgroundColor"><?= __("Button background color", NEWSLETTER2GO_TEXTDOMAIN) ?></label>
                            <div class="n2go-cp input-group">
                                <span class="n2go-input-group-addon">#</span><input id="valueInputBBC" type="text"
                                                                                    name="button.background-color"
                                                                                    placeholder="" value="00BAFF"
                                                                                    class="n2go-colorField form-control n2go-text-right">
                                <button id="styleInputBBC"
                                        class="input-group-btn jscolor{valueElement:'valueInputBBC', styleElement:'styleInputBBC'}">
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if ($formUniqueCode) {
            ?>
            <div class="n2go-block50 main-block">
                <div class="panel">
                    <div class="panel-heading text-center">
                        <h3><?= __("This is how your form will look like", NEWSLETTER2GO_TEXTDOMAIN) ?></h3>
                    </div>
                    <div class="panel-body">
                        <ul id="n2gButtons" class="nav nav-tabs">
                            <?php $active = false;
                            if ($forms[$formUniqueCode]['type_subscribe']) {
                                ?>
                                <li id="btnShowPreviewSubscribe"
                                    class="active" ><?= __("Subscription-Form", NEWSLETTER2GO_TEXTDOMAIN) ?></li>
                                <?php
                                $active = true;
                            }
                            if ($forms[$formUniqueCode]['type_unsubscribe']) {
                                ?>
                                <li id="btnShowPreviewUnsubscribe" <?= (!$active ? 'class="active"' : '') ?>><?= __("Unsubscription-Form", NEWSLETTER2GO_TEXTDOMAIN) ?></li>
                            <?php } ?>
                            <li id="btnShowConfig" class="" ><?= __("Source", NEWSLETTER2GO_TEXTDOMAIN) ?></li>
                        </ul>
                        <!-- Tab panes-->
                        <div id="preview-form-panel" class="preview-pane">
                            <div id="widgetPreviewSubscribe" <?= (!$active ? 'style="display:none"' : '')?>>
                                <?php if (!isset($errorMessage)) { ?>
                                    <script id="n2g_script_subscribe">
                                    </script>
                                <?php } else { ?>
                                    <h3 class="n2go-error-general"><?= __($errorMessage) ?></h3>
                                <?php } ?>
                            </div>
                            <div id="widgetPreviewUnsubscribe" <?= ($active ? 'style="display:none"' : '')?>>
                                <?php if (!isset($errorMessage)) { ?>
                                    <script id="n2g_script_unsubscribe">
                                    </script>
                                <?php } else { ?>
                                    <h3 class="n2go-error-general"><?= __($errorMessage) ?></h3>
                                <?php } ?>
                            </div>
                            <div id="nl2gStylesConfig" class="preview-pane">
                                <textarea id="widgetStyleConfig"
                                          name="widgetStyleConfig"><?php echo $nl2gStylesConfigObject; ?></textarea>

                                <input type="submit" value="<?= __("Save settings", NEWSLETTER2GO_TEXTDOMAIN) ?>"
                                       class="save-btn button button-primary n2go-btn" name="saveApiKey"
                                       style="margin-top:15px"/>
                            </div>
                        </div>

                        <br>
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="n2go-section">
        <br/>
        <input type="submit" value="<?= __("Save settings", NEWSLETTER2GO_TEXTDOMAIN) ?>"
               class="save-btn button button-primary n2go-btn" name="saveApiKey"/>
        <a id="resetStyles" value="resetStyles" class="save-btn button"
           name="resetStyles"><?= __("Reset settings", NEWSLETTER2GO_TEXTDOMAIN) ?></a>
    </div>

</form>




