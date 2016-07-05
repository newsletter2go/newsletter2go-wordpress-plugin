window.addEventListener('load', function () {
    var formUniqueCode = document.getElementById('formUniqueCode').value.trim(),
        widgetPreview = document.getElementById('widgetPreview');

    if (formUniqueCode) {
        var picker = jQuery.farbtastic('#colorPicker'),
            widgetStyleConfig = document.getElementById('widgetStyleConfig'),
            input,
            timer = 0,
            n2gSetUp = function  () {
                if (widgetStyleConfig.textContent === null || widgetStyleConfig.textContent.trim() === "") {
                    widgetStyleConfig.textContent = JSON.stringify(n2gConfig, null, 2);
                } else {
                    n2gConfig = JSON.parse(widgetStyleConfig.textContent);
                }

                [].forEach.call(document.getElementsByClassName('nl2g-fields'), function (element) {
                    var field = element.name.split('.');
                    var style = getStyle(field[1], n2gConfig[field[0]]['style']);

                    element.value = element.style.backgroundColor = style;
                    if (element.value !== '') {
                        element.style.color = picker.RGBToHSL(picker.unpack(element.value))[2] > 0.5 ? '#000' : '#fff';
                    }
                });
            };

        function getStyle (field, str) {
            var styleArray = str.split(';');

            for (var i=0; i < styleArray.length; i++){
                var styleField = styleArray[i].split(':');
                if (styleField[0].trim() == field) {
                    return styleField[1].trim();
                }
            }
            return '';
        }

        function updateConfig (element) {
            widgetStyleConfig.textContent = '';
            var formPropertyArray = element.name.split('.'),
                property = formPropertyArray[0],
                attribute = 'style',
                cssProperty = formPropertyArray[1],
                cssValue = element.value;

            var styleProperties;
            if (n2gConfig[property][attribute] == '') {
                styleProperties = cssProperty + ':' + cssValue;
            } else {
                styleProperties = updateString(n2gConfig[property][attribute], cssProperty, cssValue);
            }

            n2gConfig[property][attribute] = styleProperties;
            widgetStyleConfig.textContent = JSON.stringify(n2gConfig, null, 2);
        }

        function updateForm () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                document.getElementById('n2g_form').remove();
                n2g('subscribe:createForm', n2gConfig);
            }, 100);
        }

        function updateString (string, cssProperty, cssValue) {
            var stylePropertiesArray = string.split(';'),
                found = false,
                updatedString;
            // todo
            for (var i = 0; i < stylePropertiesArray.length-1; i++) {
                var trimmedAttr = stylePropertiesArray[i].trim();
                var styleProperty = trimmedAttr.split(':');
                if (styleProperty[0] == cssProperty) {
                    styleProperty[1] = cssValue;
                    stylePropertiesArray[i] = styleProperty[0] + ':' + styleProperty[1];
                    found = true;
                    break;
                }
            }
            if (!found) {
                stylePropertiesArray[i] = cssProperty + ':' + cssValue;
            }

            updatedString = stylePropertiesArray.join(';');

            if(updatedString.slice(-1) !== ';'){
                updatedString+=';';
            }

            return updatedString;
        }

        function show () {
            switch(this.id) {
                case 'btnShowConfig':
                    widgetStyleConfig.style.display = 'block';
                    widgetPreview.style.display = 'none';
                    break;
                default:
                    widgetPreview.style.display = 'block';
                    widgetStyleConfig.style.display = 'none';
            }
            this.className = 'button btn-nl2go';
            [].forEach.call(jQuery('#'+this.id).siblings(), function(button) {
                button.className = 'button';
            });
        }

        jQuery('.color-picker').focus(function () {
            input = this;
            picker.linkTo(function () {}).setColor('#000');
            picker.linkTo(function (color) {
                input.style.backgroundColor = color;
                input.style.color = picker.RGBToHSL(picker.unpack(color))[2] > 0.5 ? '#000' : '#fff';
                input.value = color;

                updateConfig(input);
                updateForm();

            }).setColor(input.value);
        }).blur(function () {
            picker.linkTo(function () {}).setColor('#000');
            if (!input.value) {
                input.style.backgroundColor = '';
                input.style.color = '';
            }
            updateConfig(input);
            updateForm();
        });

        n2gSetUp();

        n2g('create', formUniqueCode);
        n2g('subscribe:createForm', n2gConfig);

        [].forEach.call(document.getElementById('n2gButtons').children, function (button) {
            button.addEventListener('click', show);
        });

        document.getElementById('colorPicker').addEventListener('click', function () {
            input && input.focus();
        });

    }
});