// myo and dttable are global on purpose: they are used by the pages and by the scripts of the components
window.myo = {
    UI: {
        alertType : {
            OperativeSuccess: 0,
            OperativeInfo: 1,
            OperativeWarning: 2,
            OperativeError: 3,
            RuntimeError: 4            
        },
        dttable: null,
        OnDeleteActionFormAlert: false,
        OnModifyActionFormAlert: false,
        OnInsertActionFormAlert: false,
        OnInfoActionFormAlert: false,

        initializeDataTable (tableRef) {
            const table = document.getElementById(tableRef);
            if (typeof(table) == 'undefined' || table == null) {
                alert ('Undefined datatableform "' + tableRef + '" defined as datatable');
            }
            jQuery('#'+tableRef).on('preInit.dt', function (e) {
                window.dttable = jQuery('#'+tableRef).DataTable();

                for (let i = dttable.buttons().length; i > 0; i--) {
                    dttable.buttons(i-1).remove();
                }
                dttable.button().add(0, {
                    text: '<i class="fa-solid fa-rotate-right"></i>',
                    action: function ( e, dt, node, config ) {
                        dt.ajax.reload();
                    }
                } );

                const insertAllowed = dttable.context[0].oInit.insertAllowed;
                const dataTableOnModifyActionForm = dttable.context[0].oInit.onModifyActionForm;
                const dataTableOnInsertActionForm = dttable.context[0].oInit.onInsertActionForm;
                if (typeof insertAllowed !== 'undefined') {
                    if (insertAllowed) {
                        if (typeof dataTableOnInsertActionForm !== 'undefined') {
                            dttable.button().add(0, {
                                text: '<i class="fa-solid fa-plus"></i>',
                                action: function ( e, dt, node, config ) {
                                    myo.UI.onClickInsertDataTable(dataTableOnInsertActionForm);
                                }
                            });        
                        } else if (typeof dataTableOnModifyActionForm !== 'undefined') {
                            dttable.button().add(0, {
                                text: '<i class="fa-solid fa-plus"></i>',
                                action: function ( e, dt, node, config ) {
                                    myo.UI.onClickInsertDataTable(dataTableOnModifyActionForm);
                                }
                            });        
                        }
                    }
                }
            });

            const tbody = table.getElementsByTagName('tbody')[0];
            jQuery(tbody).on('click', 'tr', function () {
                if (jQuery(this).hasClass('selected')) {
                    jQuery(this).removeClass('selected');
                } else {
                    dttable.$('tr.selected').removeClass('selected');
                    jQuery(this).addClass('selected');
                }
            });
        },
        OnCreatedRow (e, settings) {
            // Assignment of the action to the delete button
            const dataTableOnModifyActionForm = this.api().context[0].oInit.onModifyActionForm;
            const dataTableOnDeleteActionForm = this.api().context[0].oInit.onDeleteActionForm;
            
            // Assignment of the action to the modify button
            // The buttons are searched in the whole row, so the actions column can be in any position
            const modifyButton = e.querySelector('[data-dtaction="modify"]');
            if (modifyButton) {
                const onModifyActionForm = document.getElementById (dataTableOnModifyActionForm);
                if (typeof(onModifyActionForm) != 'undefined' && onModifyActionForm != null) {
                    const modifyKey = myo.UI.getListKeys (dataTableOnModifyActionForm, 'actionread');   // Use the array of the key of the method as primary key
                    // The key values are passed as data with a closure, never concatenated in a code string
                    const modifyValues = modifyKey.map (key => settings[key]);
                    modifyButton.addEventListener ('click', function () {
                        myo.UI.onClickModifyDataTable (dataTableOnModifyActionForm, modifyValues);
                    });
                } else {
                    if (!this.OnModifyActionFormAlert) {
                        modifyButton.style.display = 'none';
                    }
                }
            }
                
            const deleteButton = e.querySelector('[data-dtaction="delete"]');
            if (deleteButton) {
                const onDeleteActionForm = document.getElementById (dataTableOnDeleteActionForm);
                if (typeof(onDeleteActionForm) != 'undefined' && onDeleteActionForm != null) {
                    const deleteKey = myo.UI.getListKeys (dataTableOnDeleteActionForm + '-button', 'action');   // Use the array of the key of the method as primary key
                    const deleteValues = deleteKey.map (key => settings[key]);
                    deleteButton.addEventListener ('click', function () {
                        myo.UI.onClickDeleteDataTable (dataTableOnDeleteActionForm, deleteValues);
                    });
                } else {
                    if (!this.OnDeleteActionFormAlert) {
                        deleteButton.style.display = 'none';
                    }
                }
            }

            // Assignment of the action to the info button
            const dataTableOnInfoActionForm = this.api().context[0].oInit.onInfoActionForm;
            const dataTableonClickInfoActionCustom = this.api().context[0].oInit.onClickInfoActionCustom;

            const infoButton = e.querySelector('[data-dtaction="info"]');
            if (infoButton) {
                const onInfoActionForm = document.getElementById (dataTableOnInfoActionForm);
                if (typeof(onInfoActionForm) != 'undefined' && onInfoActionForm != null) {
                    const infoKey = myo.UI.getListKeys (dataTableOnInfoActionForm, 'actionread');   // Use the array of the key of the method as primary key
                    const infoValues = infoKey.map (key => settings[key]);
                    if (dataTableonClickInfoActionCustom != null) {
                        // Find the custom function by name, also with a dotted path (e.g. "myApp.showInfo")
                        const customFunction = dataTableonClickInfoActionCustom.split('.').reduce ((obj, name) => obj == null ? undefined : obj[name], window);
                        if (typeof customFunction !== 'function') {
                            window.alert ("The onClickInfoActionCustom function " + dataTableonClickInfoActionCustom + " doesn't exist.");
                        } else {
                            infoButton.addEventListener ('click', function () {
                                customFunction (dataTableOnInfoActionForm, infoValues);
                            });
                        }
                    } else {
                        infoButton.addEventListener ('click', function () {
                            myo.UI.onClickInfoDataTable (dataTableOnInfoActionForm, infoValues);
                        });
                    }
                } else {
                    if (!this.OnInfoActionFormAlert) {
                        infoButton.style.display = 'none';
                    }
                }
            }
        },
        onClickModifyDataTable(modalname, arraykey){
            jQuery.noConflict();

            // Populate the fields
            this.populateFormFields (modalname, arraykey);

            // Show the confirm modal
            jQuery('#' + modalname).modal('show');            
        },
        onClickInsertDataTable(modalname){
            jQuery.noConflict();

            // Initialize the fields
            this.populateFormFields (modalname, []);

            // Show the confirm modal
            jQuery('#' + modalname).modal('show');
        },
        onClickDeleteDataTable(modalname, arraykey){
            jQuery.noConflict();

            var keys = this.getListKeys (modalname + '-button', 'action');

            // Set the parameter of the confirm button
            var deletebutton = jQuery('#' + modalname + '-button')[0];
            deletebutton.dataset.actionset = this.replaceKeys (deletebutton.dataset.action, keys, arraykey);

            // Show the confirm modal
            jQuery('#' + modalname).modal('show');

        },
        onClickInfoDataTable(modalname, arraykey){
            jQuery.noConflict();

            // Populate the fields
            this.populateFormFields (modalname, arraykey);

            // Show the confirm modal
            jQuery('#' + modalname).modal('show');
        },
        validateEmailField(emailField, event) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (!re.test(String(emailField.value).toLowerCase())) {
                myo.UI.raiseAlert (myo.UI.alertType.OperativeError, "Invalid email!");
                event.preventDefault();
                emailField.focus();
                return false;
            }
            return true;
        },
        populateFormFields (formname, arraykey) {
            // Find the form
            var form = jQuery('#' + formname)[0];
            var keys = this.getListKeys (formname, 'actionread');
            
            // New record
            if (arraykey.length == 0) {
                myo.UI.clearFields (form, keys);
                return true;
            }

            if (keys.length == 0) {
                return true;
            }

            if (keys.count == 0) {
                return false;
            }

            // Read the record, with all the placeholders of the URL replaced in a single call
            var response;
            if ("actionread" in form.dataset) {
                response = myo.WS.callReadRequest (this.replaceKeys (form.dataset.actionread, keys, arraykey),
                    document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            } else {
                window.alert ("The actionread parameter in the form is missing.");
                return false;
            } 
            if (response.success == false) {
                window.alert ("Server comunication error: " + response.data + ".");
                return false;
            }

            myo.UI.populateFields (form, response.data);
        },
        generateDataPost(formname) {
            // Find the form
            var form = jQuery('#' + formname)[0];
            var data = { };

            // find all the input fields in the form and populate
            let inputfields = form.getElementsByTagName('input');
            Array.from(inputfields).forEach (function(field) {
                if ("fieldname" in field.dataset) {
                    switch(field.type) {
                        case 'checkbox':
                            data[field.dataset.fieldname] = field.checked ? "1" : "0";
                            break;
                        default:
                            data[field.dataset.fieldname] = field.value;
                    }
                }        
            });
            // find all the select fields in the form and populate
            inputfields = form.getElementsByTagName('select');
            Array.from(inputfields).forEach (function(field) {
                if ("fieldname" in field.dataset) {
                    Array.from(field.children).forEach(function(child) {
                        if (child.selected) {
                            if (data[field.dataset.fieldname] === undefined) {
                                data[field.dataset.fieldname] = child.value;
                            } else {
                                data[field.dataset.fieldname] =  data[field.dataset.fieldname] + '|' + child.value;
                            }
                        }
                    })
                }        
            });

            return data;
        },
        getListKeys(objName, actionName) {
            const regex = /\{(.*?)\}/g;
            var obj = jQuery('#' + objName)[0];
            var keys = [];

            if (typeof obj.dataset[actionName] !== 'undefined') {
                let key;
                while (key = regex.exec(obj.dataset[actionName])) {
                  keys.push(key[1]);
                }
            }

            return keys;
        },
        replaceKeys(url, keys, arraykey) {
            // Replace every {key} placeholder of the URL with the value in the same position of arraykey.
            // The values are encoded, so characters like / ? # & don't change the URL.
            // split/join is used instead of replace() because replace() interprets $ in the replacement string
            keys.forEach (function (key, i) {
                const value = (arraykey[i] === undefined || arraykey[i] === null) ? '' : arraykey[i];
                url = url.split('{' + key + '}').join(encodeURIComponent(value));
            });

            return url;
        },
        getObjKeys(arrayKey, objData) {
            const objKey = { };
            arrayKey.forEach (function (key) {
                if (key in objData) {
                    objKey[key] = objData[key];
                }
            });

            return objKey;
        },
        clearFields (form, keys) {
            let inputfields = form.getElementsByTagName('input');
            Array.from(inputfields).forEach (function(field) {
                // The key fields are emptied too: an empty key identifies a new record
                if ("fieldname" in field.dataset) {
                    switch(field.type) {
                        case 'checkbox':
                            field.checked = false;
                            break;
                        default:
                            field.value = '';
                    }
                }        
            });
            // find all the select fields in the form and initialize
            inputfields = form.getElementsByTagName('select');
            Array.from(inputfields).forEach (function(field) {
                if ("fieldname" in field.dataset) {
                    if (field.hasAttribute('data-ajax--url')) {
                        Array.from(field.children).forEach(function(child) {
                            field.remove(child);
                        });
                    } else {
                        Array.from(field.children).forEach(function(child) {
                            child.selected = false;
                        });    
                    }
                }        
            });
        },
        populateFields (form, data) {
            // find all the input fields in the form and populate
            let inputfields = form.getElementsByTagName('input');
            Array.from(inputfields).forEach (function(field) {
                if ("fieldname" in field.dataset) {
                    field.value = '';
                    if (field.dataset.fieldname in data) {
                        if ("fieldname" in field.dataset) {
                            switch(field.type) {
                                case 'checkbox':
                                    field.checked = data[field.dataset.fieldname] === "1";
                                    myo.UI.changeField(field);
                                    break;
                                default:
                                    field.value = data[field.dataset.fieldname];
                            }
                        }
                    }
                }        
            });
            // find all the select fields in the form and populate
            inputfields = form.getElementsByTagName('select');
            Array.from(inputfields).forEach (function(field) {
                if ("fieldname" in field.dataset) {
                    if (field.dataset.fieldname in data) {
                        // The selected values can be a JSON array, a string with the values separated by "|" or a single value
                        const value = data[field.dataset.fieldname];
                        let selected;
                        if (Array.isArray(value)) {
                            selected = value;
                        } else if (typeof value === 'string') {
                            selected = value === '' ? [] : value.split('|');
                        } else {
                            selected = value == null ? [] : [value];
                        }
                        // The option values are strings: compare them as strings
                        selected = selected.map (singlesel => String(singlesel));

                        if (field.hasAttribute('data-ajax--url')) {
                            field.innerHTML = '';
                            const url = field.getAttribute('data-ajax--url');
                            selected.forEach (function(singlesel) {
                                const response = myo.WS.callReadRequest  (url + "/" + encodeURIComponent(singlesel),
                                                                    document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                                const optionElement = document.createElement('option');
                                optionElement.value = singlesel;
                                optionElement.textContent = response.data.text;
                                optionElement.setAttribute('selected', null);
                                field.appendChild(optionElement);
                            });
                        } else {
                            Array.from(field.options).forEach(function(child) {
                                child.selected = selected.includes(child.value);
                            });
                        }
                        myo.UI.changeField(field);
                    }
                }        
            });
            // find all the textarea fields in the form and populate
            inputfields = form.getElementsByTagName('textarea');
            Array.from(inputfields).forEach (function(field) {
                if ("fieldname" in field.dataset) {
                    field.value = '';
                    if (field.dataset.fieldname in data) {
                        if ("fieldname" in field.dataset) {
                            switch(field.type) {
                                case 'checkbox':
                                    field.checked = data[field.dataset.fieldname] === "1";
                                    myo.UI.changeField(field);
                                    break;
                                default:
                                    field.value = data[field.dataset.fieldname];
                            }
                        }
                    }
                }        
            });
        },
        changeField(field) {
            var event = new Event('change');
            field.dispatchEvent(event);
        },
        processAlert(obj) {
            /**
             * View the corresponding message in a response, if exists, based on his tipology
             * Params:
             *   response: array of the response
             * Return:
             *   false if the message is blocker, true if not
             */
            switch (true) {
                case obj.hasOwnProperty('_operativeSuccess'):
                    myo.UI.raiseAlert (myo.UI.alertType.OperativeSuccess, obj['_operativeSuccess']['message']);
                    return true;
                case obj.hasOwnProperty('_operativeInfo'):
                    myo.UI.raiseAlert (myo.UI.alertType.OperativeInfo, obj['_operativeInfo']['message']);
                    return true;    
                case obj.hasOwnProperty('_operativeWarning'):
                    myo.UI.raiseAlert (myo.UI.alertType.OperativeWarning, obj['_operativeWarning']['message']);
                    return true;
                case obj.hasOwnProperty('_operativeError'):
                    myo.UI.raiseAlert (myo.UI.alertType.OperativeError, obj['_operativeError']['message']);
                    return false;
                case obj.hasOwnProperty('_runtimeError'):
                    myo.UI.raiseAlert (myo.UI.alertType.RuntimeError, obj['_runtimeError']['message']);
                    return false;
            }
            return true;
        },
        raiseAlert(type, message) {
            switch (type) {
                case myo.UI.alertType.OperativeSuccess:
                    Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        icon: 'success',
                        text: message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    break;
                case myo.UI.alertType.OperativeInfo:
                    Swal.fire({ 
                        text: message,
                        type: 'info',
                        icon: 'info'
                    });
                    break;
                case myo.UI.alertType.OperativeWarning:
                    Swal.fire({ 
                        text: message,
                        type: 'warning',
                        icon: 'warning'
                    });
                    break;
                case myo.UI.alertType.OperativeError:
                    Swal.fire({ 
                        text: message,
                        type: 'error',
                        icon: 'error'
                    });                    
                    break;
                case myo.UI.alertType.RuntimeError:
                    Swal.fire({ 
                        text: message,
                        type: 'error',
                        icon: 'error',
                        iconHtml: '!'
                    });                    
                    break;
            }
        }
    },
    WS: {
        callReadRequest  (url, token) {
            var response = { success: false, data: null };
            jQuery.ajax({
                url: url,
                method: 'GET',
                async: false,
                data: {
                    "_token": token
                },
                success: function (result) {
                    response.success = myo.UI.processAlert (result);
                    response.data = result;
                },
                error: function (result) {
                    myo.UI.processAlert({
                        "_runtimeError": {
                            "message": result.responseText
                        }
                    });
                    response.success = false;
                },
            });

            return response;
        },
        callUpdateRequest (formname, url, token) {
            var conclusion = false;
            var data = myo.UI.generateDataPost (formname);
            data["_token"] = token;
                        
            jQuery.ajax({
                url: url,
                type: 'POST',
                async: false,
                data: data,
                success: function (result) {
                    conclusion = myo.UI.processAlert(result);
                },
                error: function (result) {
                    try {
                        conclusion = myo.UI.processAlert(JSON.parse(result.responseText));
                    } catch (e) {
                        myo.UI.processAlert({
                            "_runtimeError": {
                                "message": result.responseText
                            }
                        });    
                    }
                    
                    conclusion = false; 
                },
            });

            return conclusion;
        },
        callDeleteRequest (url, token) {
            var conclusion = false;
            jQuery.ajax({
                url: url,
                type: 'DELETE',
                async: false,
                data: {
                    "_token": token
                },
                success: function (result) {
                    conclusion = myo.UI.processAlert (result);
                },
                error: function (result) {
                    myo.UI.processAlert({
                        "_runtimeError": {
                            "message": result.responseText
                        }
                    });
                    conclusion = false; 
                },
            });

            return conclusion;
        }
    }
}