window.addEventListener('load', function () {
    var formUniqueCode = document.getElementById('formUniqueCode'),
        widgetPreview = document.getElementById('widgetPreview'),
        nl2gStylesConfig = document.getElementById('nl2gStylesConfig'),
        widgetStyleConfig = document.getElementById('widgetStyleConfig'),
        input,
        timer = 0;


    function n2gSetUp() {
        if (widgetStyleConfig.textContent === null || widgetStyleConfig.textContent.trim() === '') {
            widgetStyleConfig.textContent = JSON.stringify(n2gConfig, null, 2);
        } else {
            n2gConfig = JSON.parse(widgetStyleConfig.textContent);
        }

        [].forEach.call(document.getElementsByClassName('n2go-colorField'), function (element) {
            var field = element.name.split('.'),
                style = getStyle(field[1], n2gConfig[field[0]]['style']);

            if (style !== '') {
                element.value = style;
            }
        });
    }

    function getStyle(field, str) {
        var styleArray = str.split(';');

        for (var i = 0; i < styleArray.length; i++) {
            var styleField = styleArray[i].split(':');
            if (styleField[0].trim() == field) {
                return styleField[1].trim();
            }
        }
        return '';
    }

    function updateConfig(element) {
        var formPropertyArray = element.name.split('.'),
            property = formPropertyArray[0],
            attribute = 'style',
            cssProperty = formPropertyArray[1],
            cssValue = '#' + element.value,
            styleProperties;

        widgetStyleConfig.textContent = '';
        if (n2gConfig[property][attribute] == '') {
            styleProperties = cssProperty + ':' + cssValue;
        } else {
            styleProperties = updateString(n2gConfig[property][attribute], cssProperty, cssValue);
        }

        n2gConfig[property][attribute] = styleProperties;
        widgetStyleConfig.textContent = JSON.stringify(n2gConfig, null, 2);
    }

    function updateForm() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            jQuery('#widgetPreview').find('form').remove();
            n2g('subscribe:createForm', n2gConfig);
        }, 100);
    }

    function updateString(string, cssProperty, cssValue) {
        var stylePropertiesArray = string.split(';'),
            found = false,
            updatedString;

        for (var i = 0; i < stylePropertiesArray.length - 1; i++) {
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

        if (updatedString.slice(-1) !== ';') {
            updatedString += ';';
        }

        return updatedString;
    }

    function show() {
        var btnConfig = jQuery('#btnShowConfig'),
            btnPreview = jQuery('#btnShowPreview');

        switch (this.id) {
            case 'btnShowConfig':
                nl2gStylesConfig.style.display = 'block';
                widgetPreview.style.display = 'none';
                btnConfig.addClass('active');
                btnPreview.removeClass('active');
                break;
            default:
                widgetPreview.style.display = 'block';
                nl2gStylesConfig.style.display = 'none';
                btnConfig.removeClass('active');
                btnPreview.addClass('active');
        }
    }

    if (formUniqueCode) {
        jQuery('.n2go-colorField').on('change', function () {
            input = this;

            updateConfig(input);
            updateForm();

        });

        n2gSetUp();

        n2g('create', formUniqueCode.value.trim());
        n2g('subscribe:createForm', n2gConfig);

        show();

        [].forEach.call(document.getElementById('n2gButtons').children, function (button) {
            button.addEventListener('click', show);
        });
    }
});