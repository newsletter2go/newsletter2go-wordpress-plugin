<div class="wrap">
    <?php if ($curl_error != null) { ?>
        <div class="n2go-error"><?= $curl_error ?></div>

    <?php } ?>
    <div id="icon-options-general" class="icon32"><br/></div>
    <form action="admin.php?page=n2go-api" method="POST">
        <div>
            <img src="https://www.newsletter2go.de/pr/150204_WP_Banner.png"/>

            <h2>Connect to Newsletter2Go</h2>

            <div class="n2go-container">
                <h3 id="n2goHeaderConnection">
                    <?php echo $response['success'] ? 'Connected!' : 'Not connected yet'; ?>
                </h3>
                <input type="text" name="apiKey" placeholder="Insert your Newsletter2Go API key"
                       value="<?php echo $apiKey; ?>" class="nl2g-settings-input-fields"/>
                <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
                <br/>
                <a href="https://app.newsletter2go.com/en/settings/#/api" target="_blank">Where do I find my API
                    key?</a>
            </div>
            <hr/>
        </div>

        <div>
            <h2>Unique Form Code</h2>

            <div class="n2go-container">
                <input type="text" id="formUniqueCode" name="formUniqueCode" placeholder="Insert your form unique code"
                       value="<?php echo $formUniqueCode; ?>" class="nl2g-settings-input-fields"/>
                <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
                <br/>
                <a href="https://www.newsletter2go.de/hilfe/empfaenger-verwalten/wo-kann-ich-double-op-in-einstellen/"
                   target="_blank">Where can I find the unique form code?</a>
            </div>
            <hr/>
        </div>

        <div>
            <h2>Configure subscription form</h2>
            <div class="n2go-container">
                <div class="nl2g-inner-container">
                    <h3>Visual appearance</h3>
                    <table>
                       <tr>
                            <th>Form background color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="form.background-color"
                                        size="7"/></td>
                        </tr>
                        <tr>
                            <th>Label text color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="label.color"
                                        size="7"/></td>
                        </tr>
                        <tr>
                            <th>Input Text color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="input.color"
                                       size="7"/></td>
                        </tr>
                        <tr>
                            <th>Input border color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="input.border-color"
                                        size="7"/></td>
                        </tr>
                        <tr>
                            <th>Input background color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="input.background-color"
                                        size="7"/></td>
                        </tr>
                        <tr>
                            <th>Button text color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="button.color"
                                        size="7"/></td>
                        </tr>
                        <tr>
                            <th>Button background color</th>
                            <td><input class="js-n2go-widget-field color-picker nl2g-fields" type="text" name="button.background-color"
                                        size="7"/></td>
                        </tr>
                    </table>

                    <div id="colorPicker"></div>
                    <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
                </div>
                <div id="n2goWidget">
                    <h3>Here is the preview for you</h3>
                    <div id="n2gButtons">
                    <input type="button" value="Preview" class="button btn-nl2go" id="btnShowPreview" />
                     <input type="button" value="Configure Styles" class="button" id="btnShowConfig"/>
                    </div>
                        <script>

                    </script>

                    <div id="preview-form-panel" class="preview-pane">
                        <div id="widgetPreview">

                            <?php
                            if(!isset($errorMessage)){ ?>
                                <script id="n2g_script">
                                </script>
                            <?php }else{ ?>
                                <h3 class="n2go-error-general"><?= $errorMessage ?></h3>
                            <?php } ?>
                        </div>
                        <textarea id="widgetStyleConfig" name="widgetStyleConfig"><?php echo $nl2gStylesConfigObject; ?></textarea>
                    </div>

                    <p>Your subscription form will show up under "widgets". Feel free to place it on any page.</p>
                    <input type="submit" value="Save subscription form" class="button button-primary btn-nl2go"
                           name="saveApiKey"/>
                </div>
            </div>
        </div>
    </form>
</div>


