<style type="text/css">
    .n2go-container {
        padding: 25px 0 30px 20px;
    }

    .n2go-container a {
        margin: auto;
    }

    .n2go-container table {
        text-align: left;
    }

    .n2go-container table td {
        padding: 5px;
    }

    .preview-pane {
        border: 1px solid #ccc;
        width: 400px;
        padding: 15px;
        height: 300px;
    }

    .preview-pane iframe {
        height: 100%;
    }

    #n2goHeaderConnection {
        display: table;
        background-color: <?php echo $attributesApi['success'] ? 'greenyellow' : 'yellow'; ?>;
        padding: 10px;
        border-radius: 10px;
    }

    #n2goHeaderDOI {
        display: table;
        background-color: <?php echo (isset($attributesApi['doi-success']) &&$attributesApi['doi-success'] ? 'greenyellow' : 'yellow'); ?>;
        padding: 10px;
        border-radius: 10px;
    }

    .widgetField {
        display: table;
        cursor: move;
    }

    #widgetSourceCode {
        font-family: Consolas, Monaco, monospace;
        font-size: 13px;
        background: #f9f9f9;
        outline: 0;
        width: 100%;
        height: 100%;
    }

    .n2go-container label {
        display: block;
        font-weight: bold;
        margin-top: 10px;
    }

    .n2go-editable-label {
        margin-left: 10px;
        display: inline-block;
        font-weight: bold;
        cursor: text;
        transition: padding 0.2s ease;
        border: solid 1px transparent;
        padding: 2px;
    }

    .n2go-editable-label:hover {
        background-color: #ffffff;
        padding: 4px;
        border: solid 1px #5b9dd9;
        border-radius: 4px;
    }

    #n2goWidget {
        position: fixed;
        top: 50%;
        margin-top: -215px;
        right: 25px;
        width: 435px;
    }

    .btn-nl2go {
        float: right;
        padding: 15px;
    }

    .btn-nl2go::after {
        content: '';
        clear: both;
    }

    .wrap hr {
        max-width: 600px;
        margin-left: 0;
    }

    @media screen and (max-width: 1270px) {
        #n2goWidget {
            float: left;
            position: relative;
            top: inherit;
            margin-top: inherit;
            right: inherit;
        }
    }

    input[type=checkbox].n2go-required:checked:before {
        color: red;
    }

    .n2go-error {
        margin: 15px 0;
        color: #b94a48;
        background-color: #f2dede;
        padding: 10px;
        border: 1px solid #b94a48;
        display: inline-block;
    }

    .widgetField label {
        float:left;
        text-align: right;
        overflow: hidden;
        width: 150px;
        display: inline-block;
        font-weight: bold;
        margin: 0 5px 0 5px;
        padding: 3px;
    }

    .n2go-table-header {
        font-size: 14px;
        width: 150px;
        display: inline-block;
        margin: 0 5px;
        font-weight: bold;
        color: #2ea2cc;
    }

</style>
<div class="wrap">
    <?php if ($curl_error != null) { ?>
        <div class="n2go-error"><?= $curl_error ?></div>

    <?php } ?>
    <div id="icon-options-general" class="icon32"><br/></div>
    <form action="admin.php?page=n2go-api" method="POST">
        <div>
            <img src="https://www.newsletter2go.de/pr/150204_WP_Banner.png"/>

            <h2>Connect to Newsletter2Go</h2>

            <div class="n2go-container" style="width: 600px;">
                <h3 id="n2goHeaderConnection">
                    <?php echo $attributesApi['success'] ? 'Connected!' : 'Not connected yet!'; ?>
                </h3>
                <input type="text" name="apiKey" placeholder="Insert your Newsletter2Go API key"
                       value="<?php echo $apiKey; ?>" style="width:300px"/>
                <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
                <br/>
                <a href="https://app.newsletter2go.com/en/settings/#/api" target="_blank">Where do I find my API
                    key?</a>

            </div>
            <hr/>
        </div>
        <div>
            <h2>Double Opt In Code</h2>

            <div class="n2go-container" style="width: 600px;">
                <?php if (isset($attributesApi['doi-success'])) { ?>
                    <h3 id="n2goHeaderDOI">
                        <?php echo $attributesApi['doi-success'] ? 'Valid DOI-Code ( Host: ' . $attributesApi['doi-name'] . ')' : 'Invalid DOI-Code'; ?>
                    </h3>
                <?php } ?>
                <input type="text" name="doiCode" placeholder="Insert your Newsletter2Go DOI Code"
                       value="<?php echo $doiCode; ?>" style="width:300px"/>
                <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
                <br/>
                <a href="https://www.newsletter2go.de/hilfe/empfaenger-verwalten/wo-kann-ich-double-op-in-einstellen/"
                   target="_blank">Where can I find the double opt in code?</a>
            </div>
            <hr/>
        </div>
        <div>
            <h2>Configure subscription form</h2>

            <div class="n2go-container" style="width:600px;">
                <h3>Which data fields should be visible in your subscription form?</h3>
                <div class="alert alert-info">Attention: In germany, according to german law, it's only allowed to set email-address as required</div>
                <ul id="widgetFields">
                    <li>
                        <span class="n2go-table-header" style="text-align: right;">Newsletter2Go field</span>
                        <span class="n2go-table-header" style="padding-left: 30px;">Title label</span>
                    </li>
                    <?php
                    $i = 1;
                    foreach ($attributes as $value) {
                        ?>
                        <li class="widgetField" draggable="true">
                            <input type="checkbox" <?= $value['disabled']; ?> id="<?= $value['id']; ?>"
                                   name="attributes[]" title="<?= $value['title']; ?>" class="js-n2go-widget-field <?php echo $value['required'] ? 'n2go-required' : ''?>"
                                   value="<?= $value['id']; ?>" <?= $value['checked']; ?> />
                            <input type="hidden" value="<?= $i++; ?>" name="<?= $value['id']; ?>Sort"/>
                            <label for="<?= $value['id']; ?>"><?= $value['label']; ?>: </label>
                            <div class="n2go-editable-label">
                                <?= $value['title']; ?><?php echo $value['required'] ? ' (required)' : ''?>
                            </div>
                            <input type="hidden" value="<?= $value['required']; ?>" name="<?= $value['id']; ?>Required"/>
                            <input type="hidden" value="<?= $value['title']; ?>" name="fieldTitles[<?= $value['id']; ?>]"/>
                        </li>
                    <?php } ?>
                </ul>
                <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
            </div>
            <hr/>
            <div class="n2go-container" style="width:600px;">
                <h3>General settings</h3>
                <label for="success">Success</label>
                <input type="text" name="success" id="success" value="<?= $texts['success']; ?>" size="75"/>

                <label for="failureSubsc">Failure (already subscribed)</label>
                <input type="text" id="failureSubsc" name="failureSubsc" value="<?= $texts['failureSubsc']; ?>"
                       size="75"/>

                <label for="failureEmail">Failure (wrong email syntax)</label>
                <input type="text" id="failureEmail" name="failureEmail" value="<?= $texts['failureEmail']; ?>"
                       size="75"/>

                <label for="failureRequired">Failure (required fields)</label>
                <input type="text" id="failureRequired" name="failureRequired" value="<?= $texts['failureRequired']; ?>"
                       size="75"/>

                <label for="failureError">Failure (general error)</label>
                <input type="text" id="failureError" name="failureError" value="<?= $texts['failureError']; ?>"
                       size="75"/>

                <label for="buttonText">Text on button</label>
                <input class="js-n2go-widget-field" type="text" id="buttonText" name="buttonText"
                       value="<?= $texts['buttonText']; ?>" size="75"/>
                <br/>
                <br/>
                <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
            </div>
            <hr/>
            <div class="n2go-container">
                <div style="width: 600px;float:left;" valign="top">
                    <h3>Visual appearance</h3>
                    <table>
                        <tr>
                            <th>Text color</th>
                            <td><input class="js-n2go-widget-field color-picker" type="text" name="textColor"
                                       value="<?= $colors['textColor']; ?>" size="7"/></td>
                        </tr>
                        <tr>
                            <th>Input border color</th>
                            <td><input class="js-n2go-widget-field color-picker" type="text" name="borderColor"
                                       value="<?= $colors['borderColor']; ?>" size="7"/></td>
                        </tr>
                        <tr>
                            <th>Input background color</th>
                            <td><input class="js-n2go-widget-field color-picker" type="text" name="backgroundColor"
                                       value="<?= $colors['backgroundColor']; ?>" size="7"/></td>
                        </tr>
                        <tr>
                            <th>Button text color</th>
                            <td><input class="js-n2go-widget-field color-picker" type="text" name="btnTextColor"
                                       value="<?= $colors['btnTextColor']; ?>" size="7"/></td>
                        </tr>
                        <tr>
                            <th>Button background color</th>
                            <td><input class="js-n2go-widget-field color-picker" type="text" name="btnBackgroundColor"
                                       value="<?= $colors['btnBackgroundColor']; ?>" size="7"/></td>
                        </tr>
                    </table>

                    <div id="colorPicker"></div>
                    <input type="submit" value="Save" class="button button-primary btn-nl2go"/>
                </div>
                <div id="n2goWidget">
                    <h3>Here is the preview for you</h3>
                    <input type="button" value="Preview" class="button" id="btnShowPreview"/>
                    <input type="button" value="Source Code" class="button" id="btnShowSource"/>

                    <div class="preview-pane">
                        <iframe id="widgetPreview" style="width: 100%"
                                src="<?= $previewUrl . urlencode($widget) ?>"></iframe>
                        <textarea id="widgetSourceCode" name="widgetSourceCode"
                                  style="display: none;"><?= $widget ? $widget : '' ?></textarea>
                    </div>
                    <p>Your subscription form will show up under "widgets". Feel free to place it on any page.</p>
                    <input type="submit" value="Save subscription form" class="button button-primary btn-nl2go"
                           name="saveApiKey"/>
                </div>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        window.onload = function (e) {

            var dragSrcEl = null,
                farb = jQuery.farbtastic('#colorPicker'),
                elements = document.getElementsByClassName('js-n2go-widget-field'),
                i,
                renderHTML = function () {
                    var widget = document.getElementById('widgetSourceCode'),
                        view = document.getElementById('widgetPreview');
                    widget.style.display = 'none';
                    view.src = '<?= $previewUrl ?>' + encodeURIComponent(widget.value);
                    view.style.display = 'block';

                    document.getElementById('btnShowPreview').className = 'button button-primary btn-nl2go';
                    document.getElementById('btnShowSource').className = 'button';
                };

            function buildWidgetForm(sourceCode) {
                if (!sourceCode) {
                    var checkBoxes = document.getElementsByName('attributes[]'),
                        fields = [], i, elem,
                        texts, styles, inputStyle = '';

                    for (i = 0; i < checkBoxes.length; i++) {
                        if (checkBoxes[i].checked === true) {
                            elem = [];
                            elem['sort'] = document.getElementsByName(checkBoxes[i].value + 'Sort')[0].value;
                            elem['required'] = document.getElementsByName(checkBoxes[i].value + 'Required')[0].value;
                            elem['name'] = checkBoxes[i].title;
                            elem['id'] = checkBoxes[i].value;

                            fields.push(elem);
                        }
                    }

                    texts = [];
                    texts['button'] = document.getElementsByName('buttonText')[0].value;

                    styles = [];
                    styles['textColor'] = document.getElementsByName('textColor')[0].value;
                    styles['borderColor'] = document.getElementsByName('borderColor')[0].value;
                    styles['backgroundColor'] = document.getElementsByName('backgroundColor')[0].value;
                    styles['btnTextColor'] = document.getElementsByName('btnTextColor')[0].value;
                    styles['btnBackgroundColor'] = document.getElementsByName('btnBackgroundColor')[0].value;

                    fields.sort(function (a, b) {
                        return a['sort'] - b['sort'];
                    });

                    sourceCode = '<div ' + (styles['textColor'] ? 'style="color:' + styles['textColor'] + '"' : '') + '>';
                    sourceCode += '\n  <form method="post">';

                    if (styles['borderColor'] || styles['backgroundColor'] || styles['textColor']) {
                        inputStyle = 'style="';
                        inputStyle += styles['borderColor'] ? 'border-color:' + styles['borderColor'] + '; ' : '';
                        inputStyle += styles['backgroundColor'] ? 'background-color:' + styles['backgroundColor'] + '; ' : '';
                        inputStyle += styles['textColor'] ? 'color:' + styles['textColor'] + '; ' : '';
                        inputStyle += '" ';
                    }

                    for (i = 0; i < fields.length; i++) {
                        if (fields[i]['name'] === 'Gender') {
                            sourceCode += '\n    ' + fields[i]['name'] + '<br />\n    ' + '<select ' + inputStyle + 'name="' + fields[i]['id'] + '" ' + fields[i]['required'] + '>';
                            sourceCode += '\n      <option disabled selected label=" -- select an option -- "></option>';
                            sourceCode += '\n      <option value="m">Male</option>';
                            sourceCode += '\n      <option value="f">Female</option>';
                            sourceCode += '\n    </select><br>';
                        } else {
                            sourceCode += '\n    ' + fields[i]['name'] + '<br />\n    ' + '<input ' + inputStyle + 'type="text" name="' + fields[i]['id'] + '" ' +  fields[i]['required'] + ' /><br />';
                        }
                    }

                    sourceCode += '\n    <br />\n    <div class="message"></div>';
                    sourceCode += '\n    <input name="action" type="hidden" value="n2go_subscribe" />';
                    sourceCode += '\n    <input ';
                    if (styles['btnTextColor'] || styles['btnBackgroundColor']) {
                        sourceCode += 'style="';
                        sourceCode += styles['btnTextColor'] ? 'color:' + styles['btnTextColor'] + ';' : '';
                        sourceCode += styles['btnBackgroundColor'] ? 'background-color:' + styles['btnBackgroundColor'] + ';' : '';
                        sourceCode += '"';
                    }

                    sourceCode += ' type="button" value="' + texts['button'] + '" onClick="n2goAjaxFormSubmit(this);" />\n  </form>\n</div>';
                    document.getElementById('widgetSourceCode').innerHTML = sourceCode;
                    document.getElementById('widgetSourceCode').value = sourceCode;
                }

                renderHTML();
            }

            function extractValues(elem) {
                return {
                    id: elem.children[0].id,
                    className: elem.children[0].className,
                    title: elem.children[0].title,
                    value: elem.children[0].value,
                    checked: elem.children[0].checked,
                    disabled: elem.children[0].disabled,
                    label: elem.children[2].innerHTML,
                    required: elem.children[4].value,
                    displayTitle: elem.children[3].innerHTML
                };
            }

            function importValues(elem, values) {
                elem.children[0].id = values.id;
                elem.children[0].className = values.className;
                elem.children[0].title = values.title;
                elem.children[0].value = values.value;
                elem.children[0].checked = values.checked;
                elem.children[0].disabled = values.disabled;
                elem.children[1].name = values.value + 'Sort';
                elem.children[2].innerHTML = values.label;
                elem.children[2].htmlFor = values.id;
                elem.children[3].innerHTML = values.displayTitle;
                elem.children[4].name = values.value + 'Required';
                elem.children[4].value = values.required;
                elem.children[5].value = values.title;
                elem.children[5].name = 'fieldTitles[' + values.id + ']';
            }

            function handleDragStart(e) {
                dragSrcEl = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('Text', JSON.stringify(extractValues(this)));
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                return false;
            }

            function handleDragEnter(e) {
                e.preventDefault();
                this.classList.add('over');
            }

            function handleDragLeave(e) {
                e.preventDefault();
                this.classList.remove('over');
            }

            function handleDrop(e) {
                e.stopPropagation();
                e.preventDefault();

                if (dragSrcEl != this) {
                    var a = JSON.parse(e.dataTransfer.getData('Text'));
                    var b = extractValues(this);
                    importValues(dragSrcEl, b);
                    importValues(this, a);
                }

                return false;
            }

            function handleDragEnd(e) {
                [].forEach.call(document.querySelectorAll('#widgetFields .widgetField'), function (field) {
                    field.classList.remove('over');
                });

                buildWidgetForm();
            }

            function transformToEditBox(e) {
                var me = this,
                    textField = document.createElement('input'),
                    oldText = me.innerHTML.replace(' (required)', '').trim();

                textField.value = oldText;
                textField.addEventListener('blur', function(){
                    var val = this.value,
                        required = this.parentElement.children[4].value;

                    this.parentElement.draggable = true;
                    val = val ? val : oldText;
                    if (oldText === val) {
                        this.parentNode.replaceChild(me, this);
                        return true;
                    }

                    this.parentElement.children[0].title = val;
                    this.parentElement.children[4].value = val;
                    me.innerHTML = val + (required ? ' (' + required + ')' : '');

                    this.parentNode.replaceChild(me, this);
                    buildWidgetForm();
                }, false);

                me.parentNode.replaceChild(textField, me);
                textField.parentElement.draggable = false;
                textField.focus();
            }

            [].forEach.call(document.querySelectorAll('#widgetFields .widgetField'), function (field) {
                field.addEventListener('dragstart', handleDragStart, false);
                field.addEventListener('dragenter', handleDragEnter, false);
                field.addEventListener('dragover', handleDragOver, false);
                field.addEventListener('dragleave', handleDragLeave, false);
                field.addEventListener('drop', handleDrop, false);
                field.addEventListener('dragend', handleDragEnd, false);
            });

            [].forEach.call(document.querySelectorAll('.n2go-editable-label'), function (field) {
                field.addEventListener('click', transformToEditBox, false);
            });


            buildWidgetForm(<?= $widget ? '1' : '' ?>);

            document.getElementById('btnShowSource').onclick = function () {
                var view = document.getElementById('widgetSourceCode');
                view.style.display = 'block';
                document.getElementById('widgetPreview').style.display = 'none';
                this.className = 'button button-primary btn-nl2go';
                document.getElementById('btnShowPreview').className = 'button';
            };

            document.getElementById('btnShowPreview').onclick = function () {
                renderHTML();
            };

            function hookClickHandler(checkbox) {
                checkbox.onclick = function (e) {
                    if (!this.checked) {
                        if (this.parentElement.children[4].value === 'required') {
                            e.preventDefault();
                            this.checked = true;
                            this.className = 'js-n2go-widget-field';
                            this.parentElement.children[4].value = '';
                            this.parentElement.children[3].innerHTML = this.parentElement.children[2].innerHTML.replace(' (required)', '');
                            buildWidgetForm();

                            return false;
                        }
                    } else {
                        this.className = 'js-n2go-widget-field n2go-required';
                        this.parentElement.children[4].value = 'required';
                        this.parentElement.children[3].innerHTML += ' (required)';
                    }
                };
            }

            for (i = 0; i < elements.length; i++) {
                if (elements[i].type === 'checkbox') {
                    hookClickHandler(elements[i]);
                }

                elements[i].onchange = function () {
                    buildWidgetForm();
                };
            }

            jQuery('.color-picker').focus(function () {
                var input = this;

                // reset to start position before linking to current input
                farb.linkTo(function () {
                }).setColor('#000');
                farb.linkTo(function (color) {
                    input.style.backgroundColor = color;
                    input.style.color = farb.RGBToHSL(farb.unpack(color))[2] > 0.5 ? '#000' : '#fff';
                    input.value = color;
                }).setColor(input.value);
            }).blur(function () {
                farb.linkTo(function () {
                }).setColor('#000');
                if (!this.value) {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                }
                buildWidgetForm();
            });
        };
    </script>
</div>
