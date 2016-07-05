window.addEventListener('load', function () {
    var farb = jQuery.farbtastic('#colorPicker'),
        renderHTML = function () {
            window.formUniqueCode = document.getElementById('formUniqueCode').innerHTML.trim();
            if (formUniqueCode) {
                var view = document.getElementById('widgetPreview'), formScriptTag;
                //toggle between config styles and preview to preserve space for rendering
                view.style.display = 'block';
                document.getElementById('widgetStyleConfig').style.display = 'none';
                //end of toggling

                formScriptTag = document.getElementById('n2g_script');
                window.nl2gScriptTagParent = formScriptTag.parentElement;

                //checking if formScriptTag already exists.If exist,delete it and make another.
                if (formScriptTag) {
                    nl2gScriptTagParent.removeChild(formScriptTag);
                }

                formScriptTag = document.createElement('script');
                formScriptTag.setAttribute('id', 'n2g_script');

                formScriptTag.innerHTML = "n2g('create',formUniqueCode);n2g('subscribe:createForm',n2gConfig)";

                nl2gScriptTagParent.appendChild(formScriptTag);

            }
        };

    function buildWidgetForm() {
        var nl2gStylesConfigObject = document.getElementById('nl2gStylesConfigObject').innerHTML;

        if (nl2gStylesConfigObject.length === 0 || nl2gStylesConfigObject === null || nl2gStylesConfigObject.trim() === "") {
            //default n2gCongig key
            n2gConfig = {
                "form": {
                    "class": "",
                    "style": ""
                },
                "container": {
                    "type": "table",
                    "class": "",
                    "style": "width: 100%;"
                },
                "row": {
                    "type": "tr",
                    "class": "",
                    "style": ""
                },
                "columnLeft": {
                    "type": "td",
                    "class": "",
                    "style": "width: 40%; padding: 10px 5px;"
                },
                "columnRight": {
                    "type": "td",
                    "class": "",
                    "style": ""
                },
                "checkbox": {
                    "type": "input",
                    "class": "",
                    "style": ""
                },
                "separator": {
                    "type": "br",
                    "class": "",
                    "style": ""
                },
                "input": {
                    "class": "",
                    "style": "padding: 5px 10px; border-radius: 2px; border: 1px solid #d8dee4; "
                },
                "dropdown": {
                    "type": "select",
                    "class": "",
                    "style": "padding: 3px 5px; border-radius: 2px; border: 1px solid #d8dee4;"
                },
                "button": {
                    "type": "button",
                    "class": "",
                    "id": "",
                    "style": "background-color: #00baff; border: none; border-radius: 4px; padding: 10px 20px; color: #ffffff; margin-top: 20px; cursor: pointer;"
                },
                "label": {
                    "type": "label",
                    "class": "",
                    "style": "padding-left: 5px;"
                },
                "loader": {
                    "type": "img",
                    "src": "//www.newsletter2go.com/images/loader.svg",
                    "class": "",
                    "style": "margin:; auto; display:block;"
                },
                "message": {
                    "type": "h2",
                    "class": "",
                    "id": "",
                    "style": "text-align: center;"
                }
            };
        } else {
            //config from the database
            n2gConfig = JSON.parse(nl2gStylesConfigObject);
        }

        //mandatory class for all fields which change n2gConfig - 'nl2g-fields'
        var fields = document.getElementsByClassName('nl2g-fields');

        //going trough every input field the admin page
        [].forEach.call(document.querySelectorAll('.nl2g-fields'), function (field) {

            //Setting up all we need for formatting json config file
            var fieldParts = field.name.split('.');
            var fieldTag = fieldParts[0];
            var cssFieldPropertyValue = fieldParts[1].split(':');
            var cssFieldProperty = cssFieldPropertyValue[0];
            var cssFieldValue = field.value;
            var cssConfigStyle = n2gConfig[fieldTag].style;
            var cssConfigPropValues = cssConfigStyle.split(';');

            //setting up array for tracking duplicates
            var duplicates = [];

            //going trough every n2gConfig property value for the comparing purposes
            cssConfigPropValues.forEach(function (cssConfigPropertyValue, index) {
                var cssConfigPropValueSplit = cssConfigPropertyValue.split(':');
                var cssConfigProperty = cssConfigPropValueSplit[0];

                //if first time iterating set up the property we track
                if (typeof(duplicates[cssConfigProperty]) === 'undefined') {
                    //first value is duplicates count, second number is index number for deleting this key-value pair
                    duplicates[cssConfigProperty] = [0, index];
                }

                duplicates[cssConfigProperty][0]++;

                //we want to delete all cssConfigProperty where field value exists change it
                if ((cssConfigProperty === cssFieldProperty && (typeof(cssFieldValue) !== "undefined" || cssFieldValue.trim() !== ''))) {
                    delete cssConfigPropValues[index];
                }

                //at the end, we assigning to property with the same name as deleted value of our field
                if (index === cssConfigPropValues.length - 1) {
                    if (typeof(cssFieldValue) !== "undefined" && cssFieldValue.trim() !== '') {
                        cssConfigPropValues[index + 1] = cssFieldProperty + ":" + cssFieldValue;
                    } else {
                        //when we removing duplicates there is possibility that we do not have value to alter it. In that case, we remove every but last duplicate;
                        duplicates.forEach(function (duplicate, index) {
                            if (duplicate[0] > 1 && duplicate[0] !== index - 1) {
                                delete cssConfigPropValues[1];
                            }
                        });
                    }
                }
            });

            ///reset keys for updating the config element
            var updatedConfigPropertiesValues = [];
            for (var i = 0; i < cssConfigPropValues.length; i++) {
                if (typeof(cssConfigPropValues[i]) !== "undefined" && cssConfigPropValues[i].trim() !== '') {
                    updatedConfigPropertiesValues.push(cssConfigPropValues[i].trim());
                }
            }
            //update config object
            n2gConfig[fieldTag].style = updatedConfigPropertiesValues.join(";");
            n2gConfig[fieldTag].style += ';';


        });
        //update the config form widget
        var configStylesTag = document.getElementById('widgetStyleConfig');
        if (configStylesTag !== null) {
            var parent = configStylesTag.parentElement;
            parent.removeChild(configStylesTag);
        }

        var widgetStyleConfig = document.createElement('textarea');
        widgetStyleConfig.id = 'widgetStyleConfig';
        widgetStyleConfig.name = 'widgetStyleConfig';

        widgetStyleConfig.style.display = 'none';
        widgetStyleConfig.style.overflowY = 'auto';
        widgetStyleConfig.innerHTML = JSON.stringify(n2gConfig, null, 4);
        widgetStyleConfig.value = JSON.stringify(n2gConfig, null, 4);
        document.getElementById('preview-form-panel').appendChild(widgetStyleConfig);

        renderHTML();
    }

    // events for toggling between html form and config json object
    document.getElementById('btnShowSource').onclick = function () {
        var view = document.getElementById('widgetStyleConfig');
        view.style.display = 'block';
        document.getElementById('widgetPreview').style.display = 'none';
        document.getElementById('btnShowPreview').className = 'button';
    };

    document.getElementById('btnShowPreview').addEventListener('click', renderHTML);

    // events for triggering changing the color

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

    buildWidgetForm();
});