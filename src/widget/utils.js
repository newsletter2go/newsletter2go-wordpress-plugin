window[window['Newsletter2GoTrackingObject']] = (function (code, host, namespace) {

    var code,
        companyId,
        elementId,
        transactions = {},
        state;

    var form;

    var subscribeHelpers = {
        config: {
            "form": {
                "type": "form",
                "class": "",
                "id": "",
                "style": ""
            },
            "container": {
                "type": "table",
                "class": "",
                "id": "",
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
                "style": "padding: 5px 10px; border-radius: 2px; border: 1px solid #d8dee4;"
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
                "style": "padding-left: 5px"
            },
            "loader": {
                "type": "img",
                "src": "//www.newsletter2go.com/images/loader.svg",
                "id": "",
                "class": "",
                "style": "margin: auto; display:block; width: auto;"
            },
            "message": {
                "type": "h2",
                "class": "",
                "id": "",
                "style": "text-align: center;"
            },
            "overlay": {
                "style": "display: block; width: 100%; height: 100%; position: fixed; z-index: 2147483647; background: rgba(0,0,0,0.2); top: 0; left: 0; "
            },
            "popup": {
                "style": "background-color: #ffffff; padding: 50px; display: block; margin: 100px auto auto auto; max-width: 600px; border-radius: 5px; "
            },
            "image": {
                "type": "img",
                "class": "",
                "style": "max-width: 100%;"
            },
            "h1": {
                "class": "",
                "style": "",
                "type": "h1"
            },
            "h2": {
                "class": "",
                "style": "",
                "type": "h2"
            },
            "h3": {
                "class": "",
                "style": "",
                "type": "h3"
            },
            "h4": {
                "class": "",
                "style": "",
                "type": "h4"
            },
            "h5": {
                "class": "",
                "style": "",
                "type": "h5"
            },
            "p": {
                "class": "",
                "style": "",
                "type": "p"
            }
        },
        createElement: function (type, form) {
            if (form.config[type].type) {
                var element = document.createElement(form.config[type].type);
            }
            else {
                var element = document.createElement(type);
            }
            element.className = form.config[type].class;
            if (form.config[type].id) {
                element.id = form.config[type].id;
            }
            if (form.config[type].style) {
                var styles = form.config[type].style.split(";");
                for (var i = 0; i < styles.length; i++) {
                    var parts = styles[i].split(":");
                    if (parts.length == 2) {
                        var key = parts[0].replace(/^\s+|\s+$/g, '');
                        var value = parts[1].replace(/^\s+|\s+$/g, '');
                        element.style[key] = value;
                    }
                }
            }
            if (form.config[type].src) {
                element.src = form.config[type].src;
            }
            return element;
        },
        selectById: function (id, callback) {
            var element = document.getElementById(id);
            if (element) {
                callback.apply(element, []);
            }
        },
        selectByClass: function (className, callback) {
            var elements = document.getElementsByClassName(className);
            for (var i = 0; i < elements.length; i++) {
                callback.apply(elements[i], []);
            }
        },
        selectByTag: function (tagName, callback) {
            var elements = document.getElementsByTagName(tagName);
            for (var i = 0; i < elements.length; i++) {
                callback.apply(elements[i], []);
            }
        },
        hasClass: function (element, className) {
            return (" " + element.className + " ").replace(/[\t\r\n\f]/g, " ").indexOf(" " + className + " ") > -1;
        },
        extend: function () {
            var out = arguments[0] || {};

            for (var i = 1; i < arguments.length; i++) {
                var obj = arguments[i];

                if (!obj)
                    continue;

                for (var key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        if (typeof obj[key] === 'object') {
                            out[key] = this.extend(out[key], obj[key]);
                        }
                        else {
                            out[key] = obj[key];
                        }
                    }
                }
            }

            return out;
        },
        getHtmlType: function (item, form) {
            var element;
            if (item.values) { // is enum
                if (item.is_multiselect || item.type == 'list') { // is multiselect >> checkboxes with labels
                    element = subscribeHelpers.getCheckboxes(item, form);
                } else { // is only one value >> dropdown
                    element = subscribeHelpers.getDropdown(item, form);
                }
            } else { // no enum (custom values possible)
                switch (item.attribute_type) {
                    case "boolean": // create one checkbox w/o label
                        item.values = [
                            {
                                "value": 1,
                                "label": ""
                            }
                        ];
                        element = subscribeHelpers.getCheckboxes(item, form);
                        break;
                    default: // create input
                        element = subscribeHelpers.getInput(item, form);
                        break;
                }
            }

            if (item.is_required) {
                element.className += ' required';
                element.required = true;
            }

            element.value = item.recipient_value;

            return element;
        },
        getCheckboxes: function (item, form) {
            var container = document.createElement('span');
            for (var i = 0; i < item.values.length; i++) {
                var obj = item.values[i];

                var checkboxId = item.uid + '_' + i;

                var checkbox = this.createElement('checkbox', form);
                checkbox.type = 'checkbox';
                checkbox.value = obj.value;
                checkbox.id = checkboxId;

                if (item.is_required) {
                    checkbox.className += ' required';
                    checkbox.required = true;
                }

                if (obj.is_preselected && item.apply_preselected) {
                    checkbox.checked = true;
                    item.recipient_value = item.recipient_value || [];
                    if (item.recipient_value.indexOf(obj.value) === -1) {
                        item.recipient_value.push(obj.value);
                    }
                }

                if (!item.apply_preselected) {
                    if (typeof item.recipient_value === 'object') { // we have a recipient, check right boxes, override preselected

                        var arr = Object.keys(item.recipient_value).map(function (key) {
                            return item.recipient_value[key];
                        });

                        checkbox.checked = -1 < arr.indexOf(obj.value);

                    }
                    else if (item.recipient_value == obj.value) {
                        checkbox.checked = true;
                    }
                }


                container.appendChild(checkbox);
                checkbox.onchange = function () {
                    if (item.attribute_type && item.attribute_type == 'boolean') {
                        item.recipient_value = this.checked ? this.value : 0;
                    }
                    else { // list
                        item.recipient_value = item.recipient_value || [];
                        if (this.checked) {
                            item.recipient_value.push(this.value);
                        }
                        else {
                            item.recipient_value.splice(item.recipient_value.indexOf(this.value), 1);
                        }
                    }
                }

                var label = this.createElement('label', form);
                label.htmlFor = checkboxId;
                label.innerHTML = typeof obj.label == 'undefined' ? obj.value : obj.label;
                container.appendChild(label);

                if (item.values[parseInt(i) + 1]) {
                    var separator = this.createElement('separator', form);
                    container.appendChild(separator);
                }
            }
            return container;
        },
        getDropdown: function (item, form) {
            var dropdown = this.createElement('dropdown', form);
            var hasPreselected = false;
            var options = [];
            for (var i = 0; i < item.values.length; i++) {
                var obj = item.values[i];

                var option = document.createElement('option');
                option.value = obj.value;
                option.innerHTML = obj.value;

                if (obj.is_preselected && item.apply_preselected) {
                    hasPreselected = true;
                    option.selected = true;
                    item.recipient_value = obj.value;
                }

                options.push(option);
                dropdown.appendChild(option);
            }

            if (!hasPreselected) {
                dropdown.appendChild(document.createElement('option'));
            }

            for (i = 0; i < options.length; i++) {
                dropdown.appendChild(options[i]);
            }
            dropdown.onchange = function () {
                item.recipient_value = this.value;
            }
            return dropdown;
        },
        getInput: function (item, form) {
            var input = this.createElement('input', form);
            try {
                switch (item.attribute_sub_type) {
                    case 'integer':
                        input.type = 'number';
                        input.step = 1;
                        break;
                    case 'float':
                        input.type = 'number';
                        input.step = 'any';
                        break;
                    default:
                        input.type = item.attribute_sub_type;
                        break;
                }
            }
            catch (e) {
                input.type = 'text';
            }
            input.placeholder = item.placeholder;
            input.onchange = function () {
                item.recipient_value = this.value;
            };
            return input;
        },
        outputMessage: function (message, form) {
            subscribeHelpers.loader('hide', form);
            if (!form.message) {
                form.message = subscribeHelpers.createElement('message', form);
                form.insertBefore(form.message, form.firstChild);
            }
            form.message.innerHTML = message;

        },
        loader: function (display, form) {
            if (display == 'show') {
                if (form.message) {
                    form.message.innerHTML = '';
                }
                if (!form.loader) {
                    form.loader = subscribeHelpers.createElement('loader', form);

                }
                form.insertBefore(form.loader, form.firstChild);
                form.loader.style.display = 'block';
            } else {
                form.loader.style.display = 'none';
            }
        }
    };
    var fns = {
        create: function () {
            if (!arguments[1]) {
                throw "A user id is required";
            }

            // We only pass a company id in the create call and the code is read from the GET parameter or the cookie
            if (arguments[1].indexOf("-") == -1) {
                companyId = arguments[1];
                code = decodeURIComponent((new RegExp('[?|&]n2g=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || code || null;
            }
            // We pass the whole code in the create call and extract the company id
            else {
                companyId = arguments[1].split("-")[0];
                code = arguments[1];
            }
            state = decodeURIComponent((new RegExp('[?|&]n2g_state=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || "default";
        },
        send: function () {
            fns.registerEvent.apply(this, arguments);
        },
        ecommerce_send: function () {
            for (var key in transactions) {
                if (transactions.hasOwnProperty(key)) {
                    var params = {
                        "ecommerce": transactions[key]
                    };
                    var method = "POST";
                    var path = "/newsletters/conversion/" + code;
                    fns.registerEvent(method, path, params);
                }
            }
        },
        ecommerce_addItem: function () {
            var item = arguments[1];
            if (typeof item.id == "undefined") {
                throw "A transaction id is required";
            }
            if (typeof item.name == "undefined") {
                throw "An item name is required";
            }
            if (typeof transactions[item.id] == "undefined") {
                throw "No transaction with id " + item.id + " found. Please use ecommerce:addTransaction to add it first."
            }
            var id = item.id;
            delete item.id;
            transactions[id]["items"] = transactions[id]["items"] || [];
            transactions[id]["items"].push(item);
        },
        ecommerce_addTransaction: function () {
            var transaction = arguments[1];
            if (typeof transaction.id == "undefined") {
                throw "A transaction id is required";
            }
            transactions[transaction.id] = transaction;
        },
        ecommerce_clear: function () {

        },
        webversion: function () {
            var successCallback = arguments[1] || function () {
                };
            var errorCallback = arguments[2] || function () {
                };
            var method = "GET";
            var path = "/newsletters/webversion/" + code;
            fns.registerEvent(method, path, {}, successCallback, errorCallback);
        },
        unsubscribe_addReason: function () {
            var reason = arguments[1] || "";
            var successCallback = arguments[2] || function () {
                };
            var errorCallback = arguments[3] || function () {
                };
            var method = "POST";
            var path = "/newsletters/unsubscribe/reason/" + code;
            var params = {
                "reason": reason
            };
            fns.registerEvent(method, path, params, successCallback, errorCallback);
        },
        /*subscribe_getFormMock: function () {

         var fnc = arguments[1];
         var response = {
         "status": 200,
         "info": {
         "count": 1
         },
         "value": [
         {
         "items": [
         {
         "type": "image",
         "src": "http:\/\/localhost\/php\/Impressum_files\/logo.png"
         },
         {
         "name": "email",
         "description": null,
         "is_multiselect": false,
         "default_value": null,
         "attribute_type": "text",
         "attribute_sub_type": "email",
         "id": "email",
         "label": "Email",
         "placeholder": "",
         "is_required": false,
         "type": "attribute"
         },
         {
         "name": "first_name",
         "description": null,
         "is_multiselect": false,
         "default_value": "Always set",
         "attribute_type": "text",
         "attribute_sub_type": "text",
         "id": "first_name",
         "label": "First Name",
         "placeholder": "",
         "is_required": false,
         "type": "attribute",
         "is_hidden": true
         },
         {
         "name": "Boolean",
         "description": null,
         "is_multiselect": false,
         "default_value": "0",
         "attribute_type": "boolean",
         "attribute_sub_type": "integer",
         "id": "Boolean",
         "type": "attribute",
         "label": "Boolean",
         "placeholder": "",
         "is_required": false
         },
         {
         "name": "multi",
         "type": "attribute",
         "description": null,
         "is_multiselect": true,
         "default_value": ["one"],
         "values": [
         {
         "is_preselected": true,
         "value": "one"
         },
         {
         "is_preselected": false,
         "value": "two"
         },
         {
         "is_preselected": false,
         "value": "three"
         }
         ],
         "attribute_type": "text",
         "attribute_sub_type": "text",
         "id": "multi",
         "label": "multi",
         "placeholder": "",
         "is_required": false
         },
         {
         "name": "gender",
         "description": null,
         "is_multiselect": false,
         "default_value": null,
         "values": [
         {
         "is_preselected": false,
         "value": "m"
         },
         {
         "is_preselected": false,
         "value": "f"
         }
         ],
         "attribute_type": "text",
         "attribute_sub_type": "text",
         "id": "gender",
         "label": "Gender",
         "placeholder": "",
         "is_required": false,
         "type": "attribute"
         },
         {
         "type": "list",
         "is_required": true,
         "label": "Welche Themen interessieren Sie?",
         "values": [
         {
         "is_preselected": true,
         "value": "johtoqqb",
         "label": "Thema 1",
         "groups": [
         "he39eacm",
         "93kdei29"
         ]
         },
         {
         "is_preselected": false,
         "value": "xlmkcb0z",
         "label": "Thema 2"
         }
         ]
         },
         {
         "label": "Submit",
         "placeholder": "",
         "is_button": true,
         "is_required": false,
         "type": "submit"
         }
         ],
         "messages": {
         "message_duplicate": "You're already signed in.",
         "message_error": "Something went wrong. Please try again.",
         "message_success": "Thanks you for subscribing. We've sent you an email with a confirmation link.",
         "message_mandatory_missing": "Please complete all required fields.",
         "message_confirmation": "Thank you! You've confirmed your registration.",
         "message_change": "Your data was successfully updated."
         },
         "recipientNOT": {
         "email": "test",
         "first_name": "first",
         "gender": "f",
         "Boolean": 1,
         "multi": ["one", "three"]
         }
         }
         ]
         };
         fnc(response);
         },*/
        subscribe_getForm: function () {
            var successCallback = arguments[1] || function () {
                };
            var errorCallback = arguments[2] || function () {
                };
            var method = "GET";
            var path = "/forms/generate/" + code;
            fns.registerEvent(method, path, {}, successCallback, errorCallback);
        },
        subscribe_createForm: function () {
            var config = arguments[1] || {};
            var timeout = arguments[6] || 0;
            var formConfig = subscribeHelpers.extend(subscribeHelpers.config, config);
            var form = subscribeHelpers.createElement('form', {"config": formConfig});
            form.config = formConfig;
            form.successCallback = arguments[3] || null;
            form.errorCallback = arguments[4] || null;
            form.confirmedCallback = arguments[5] || null;
            var target = arguments[2] || "n2g_script";
            var parent = document.getElementById(target);
            form.container = subscribeHelpers.createElement('container', form);
            if (timeout > 0) {
                form.style.display = 'none';
            }
            form.appendChild(form.container);
            if (parent.nodeName && parent.nodeName.toLowerCase() === "script") {
                parent.parentElement.insertBefore(form, parent);
            }
            else {
                parent.appendChild(form);
            }
            subscribeHelpers.loader('show', form);

            form.onclick = function (e) {
                if (this == e.target) {
                    this.parentElement.removeChild(this);
                }
            };

            form.onsubmit = function (e) {
                var recipient = {};
                var lists = [];
                for (var i in form.items) {
                    var item = form.items[i];
                    if (item.type == 'attribute') {
                        recipient[item.name] = item.recipient_value;
                    }
                    if (item.type == 'list') {
                        lists = item.recipient_value;
                    }
                }
                var params = {'recipient': recipient, 'lists': lists};
                subscribeHelpers.loader('show', this);
                fns.subscribe_send({}, params,
                    function (responseText) {
                        if (typeof form.successCallback === 'function') {
                            subscribeHelpers.loader('hide', form);
                            form.successCallback(responseText, form.messages);
                        }
                        else {
                            if (responseText.status == 201) {
                                subscribeHelpers.outputMessage(form.messages.message_success, form);
                                form.container.style.display = 'none';
                            } else if (responseText.status == 200) {
                                subscribeHelpers.outputMessage(form.messages.message_duplicate, form);
                            } else {
                                subscribeHelpers.outputMessage(form.messages.message_error, form);
                            }
                        }
                    },
                    function (responseText) {
                        if (typeof form.errorCallback === 'function') {
                            subscribeHelpers.loader('hide', form);
                            form.errorCallback(responseText, form.messages);
                        }
                        else {
                            subscribeHelpers.outputMessage(form.messages.message_error, form);
                        }
                    }
                );

                e.preventDefault();
                return false;
            }

            window.setTimeout(
                fns.subscribe_getForm(
                    {},
                    function (responseText) {
                        form.messages = responseText.value[0].messages;
                        if (state == 'default') {

                            var items = responseText.value[0].items;
                            var recipient = responseText.value[0].recipient || {};
                            var lists = responseText.value[0].lists || [];
                            form.items = items;
                            form.recipient = recipient;
                            form.lists = lists;
                            var applyPreselected = responseText.value[0].recipient ? false : true;
                            for (var i = 0; i < items.length; i++) {
                                var item = items[i];

                                switch (item.type) {
                                    case 'submit':
                                        var button = subscribeHelpers.createElement('button', form);
                                        button.type = 'submit';
                                        button.innerHTML = item.label;
                                        var row = subscribeHelpers.createElement('row', form);
                                        var colLeft = subscribeHelpers.createElement('columnLeft', form);
                                        var colRight = subscribeHelpers.createElement('columnRight', form);
                                        colRight.appendChild(button);
                                        row.appendChild(colLeft);
                                        row.appendChild(colRight);
                                        form.container.appendChild(row);
                                        break;
                                    case 'image':
                                        var image = subscribeHelpers.createElement('image', form);
                                        image.src = item.src;
                                        var row = subscribeHelpers.createElement('row', form);
                                        row.appendChild(image);
                                        form.container.appendChild(row);
                                        break;
                                    case 'text':
                                        var text = subscribeHelpers.createElement(item.sub_type, form);
                                        text.innerHTML = item.content;
                                        var row = subscribeHelpers.createElement('row', form);
                                        row.appendChild(text);
                                        form.container.appendChild(row);
                                        break;
                                    case 'html':
                                        var row = subscribeHelpers.createElement('row', form);
                                        row.innerHTML = item.content;
                                        form.container.appendChild(row);
                                        break;
                                    case 'attribute':
                                    case 'list':
                                        if (item.is_hidden) {
                                            item.recipient_value = recipient[item.id] || item.value;
                                            break;
                                        }
                                        item.uid = Math.random().toString(36).substring(8);
                                        if (item.type == 'attribute'){
                                            item.recipient_value = recipient[item.id] || item.default_value;    
                                        }
                                        else{ // list
                                            item.recipient_value = lists;
                                        }
                                        
                                        item.apply_preselected = applyPreselected;

                                        var label = subscribeHelpers.createElement('label', form);
                                        label.innerHTML = item.label;

                                        var input = subscribeHelpers.getHtmlType(item, form);

                                        var row = subscribeHelpers.createElement('row', form);
                                        var colLeft = subscribeHelpers.createElement('columnLeft', form);
                                        var colRight = subscribeHelpers.createElement('columnRight', form);

                                        colLeft.appendChild(label);
                                        colRight.appendChild(input);

                                        row.appendChild(colLeft);
                                        row.appendChild(colRight);
                                        form.style.display = 'block';
                                        subscribeHelpers.loader('hide', form);

                                        form.container.appendChild(row);
                                }

                            }
                        }
                        else {
                            var message = "";

                            switch (state) {
                                case "doi_success":
                                    message = form.messages.message_confirmation;
                                    break;
                                case "doi_duplicate":
                                    message = form.messages.message_duplicate;
                                    break;
                                case "doi_error":
                                    message = form.messages.message_error;
                                    break;
                            }
                            if (typeof form.confirmedCallback === 'function') {
                                subscribeHelpers.loader('hide', form);
                                form.confirmedCallback(state, form.messages);
                            }
                            else {
                                subscribeHelpers.outputMessage(message, form);
                                form.style.display = 'block';
                            }
                        }

                    },
                    function (responseText) {
                    }
                )
                , timeout);
        },
        subscribe_createPopup: function () {
            var timeout = arguments[2] * 1000 || 0;
            var config = arguments[1] || {};
            var successCallback = arguments[3] || null;
            var errorCallback = arguments[4] || null;
            var confirmedCallback = arguments[5] || null;
            var customConfigForm = config.form ? config.form.style || "" : "";
            var customConfigContainer = config.form ? config.container.style || "" : "";
            config.form = config.form || {};
            config.container = config.container || {};
            config.form.style = subscribeHelpers.config.overlay.style + customConfigForm;
            config.container.style = subscribeHelpers.config.popup.style + customConfigContainer;
            fns.subscribe_createForm({}, config, null, successCallback, errorCallback, confirmedCallback, timeout);
        },
        subscribe_send: function () {
            var params = arguments[1] || {};
            var successCallback = arguments[2] || function () {
                };
            var errorCallback = arguments[3] || function () {
                };
            var method = "POST";
            var path = "/forms/submit/" + code;
            fns.registerEvent(method, path, params, successCallback, errorCallback);
        },
        outputResponse: function (response) {
            var ele = document.getElementById(elementId);
            if (ele) {
                if (ele.nodeName == "IFRAME") {
                    var iframe = (ele.contentWindow) ? ele.contentWindow : (ele.contentDocument.document) ? ele.contentDocument.document : ele.contentDocument;
                    iframe.document.open();
                    iframe.document.write(response);
                    iframe.document.close();
                }
                else {
                    ele.innerHTML = response;
                }
            }
            else {
                document.write(response);
                document.close();
            }
        },
        login: function () {

            if (!arguments[1] || !arguments[1].username || !arguments[1].password) {
                throw "Username and password are required";
            }

            if (!arguments[2] || typeof arguments[2] !== "function") {
                var successCallback = function () {
                };
            }
            else {
                var successCallback = arguments[2];
            }

            if (!arguments[3] || typeof arguments[3] !== "function") {
                var errorCallback = function () {
                };
            }
            else {
                var errorCallback = arguments[3];
            }

            var target = arguments[1].target || "_blank";
            var base = arguments[1].base ? arguments[1].base.replace(/\/+$/, "") : "https://ui.newsletter2go.com";
            var username = arguments[1].username;
            var password = arguments[1].password;
            var path = "/oauth/v2/token";
            var auth = "Basic " + btoa("xhq4n6xf_Stguwv_jzr5c3_LTrhjkn5_9dtdsfahMvp3:Rqf1Hr#Wwaxl");
            var params = {
                "username": username,
                "grant_type": "https://nl2go.com/jwt",
                "password": arguments[1].password
            };
            fns.registerEvent(
                "POST",
                path,
                params,
                function (data) {

                    var loginForm = document.createElement('form');
                    loginForm.setAttribute('method', 'POST');
                    loginForm.setAttribute('target', target);
                    loginForm.setAttribute('action', base + '/index.php');

                    var accountIdInput = document.createElement("input");
                    accountIdInput.setAttribute('name', 'account_id');
                    accountIdInput.setAttribute('value', data.account_id);
                    accountIdInput.setAttribute('type', 'hidden');

                    var usernameInput = document.createElement("input");
                    usernameInput.setAttribute('name', 'username');
                    usernameInput.setAttribute('value', username);
                    usernameInput.setAttribute('type', 'hidden');

                    var tokenInput = document.createElement("input");
                    tokenInput.setAttribute('name', 'token');
                    tokenInput.setAttribute('value', data.access_token);
                    tokenInput.setAttribute('type', 'hidden');

                    loginForm.appendChild(accountIdInput);
                    loginForm.appendChild(usernameInput);
                    loginForm.appendChild(tokenInput);

                    loginForm.submit();
                    successCallback();
                },
                function () {
                    errorCallback();
                },
                auth
            );
        },
        registerEvent: function (method, path, params, successCallback, errorCallback, auth) {
            errorCallback = errorCallback || function () {
                };
            successCallback = successCallback || function () {
                };

            var companyIdFromCode = code ? code.split("-")[0] : companyId;
            if (companyId != companyIdFromCode) {
                return;
            }

            var xhr = new XMLHttpRequest();
            var url = host + path;
            xhr.open(method, url, true);
            if (auth) {
                xhr.setRequestHeader("Authorization", auth);
            }
            xhr.crossDomain = true;
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            xhr.onload = function (e) {
                if (xhr.readyState === 4) {
                    if (xhr.status.toString().substr(0, 1) === "2") {
                        successCallback(JSON.parse(xhr.responseText));
                    } else {
                        errorCallback(JSON.parse(xhr.responseText));
                    }
                }
            };
            xhr.onerror = function (e) {
                errorCallback(JSON.parse(xhr.responseText));
            };

            if (method == "GET") {
                xhr.send(null);
            }
            else {
                xhr.send(JSON.stringify(params));
            }
        },
        getURLParameter: function (name) {
            return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || null;
        }
    };

    var executeAction = function () {

        var functionName = arguments[0].replace(":", "_"); // for ecommerce:send, etc.

        if (typeof fns[functionName] === 'function') {
            fns[functionName].apply(this, arguments);
        }

    };

    executeAction("getcookie");
    var queue = window[namespace].q;
    for (var i = 0; i < queue.length; i++) {
        executeAction.apply(this, queue[i]);
    }

    return executeAction;

})("", "https://api.newsletter2go.com", window['Newsletter2GoTrackingObject']);