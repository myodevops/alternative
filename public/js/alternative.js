myo = {
    UI: {
        alertType : {
            OperativeSuccess: 0,
            OperativeInfo: 1,
            OperativeWarning: 2,
            OperativeError: 3,
            RuntimeError: 4            
        },
        newRecordUUID: 'af24f1d4-2916-4c55-a526-9f6217466ec3',
        dttable: null,
        OnDeleteActionFormAlert: false,
        OnModifyActionFormAlert: false,

        initializeDataTable (tableRef) {
            table = document.getElementById(tableRef);
            if (typeof(table) == 'undefined' || table == null) {
                alert ('Undefined datatableform "' + tableRef + '" defined as datatable');
            }
            $('#'+tableRef).on('init.dt', function (e) {
                dttable = $('#'+tableRef).DataTable();

                for (i = dttable.buttons().length; i > 0; i--) {
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
                if ((typeof insertAllowed !== 'undefined') && (typeof dataTableOnModifyActionForm !== 'undefined')) {
                    if (insertAllowed) {
                        dttable.button().add(0, {
                            text: '<i class="fa-solid fa-plus"></i>',
                            action: function ( e, dt, node, config ) {
                                myo.UI.onClickInsertDataTable(dataTableOnModifyActionForm);
                            }
                        });    
                    }
                }
            });

            tbody = table.getElementsByTagName('tbody')[0];
            $(tbody).on('click', 'tr', function () {
                if (jQuery(this).hasClass('selected')) {
                    jQuery(this).removeClass('selected');
                } else {
                    dttable.$('tr.selected').removeClass('selected');
                    jQuery(this).addClass('selected');
                }
            });
        },
        OnCreatedRow (e, settings) {
            // Assignment of the action to the modify button
            let modifyButton = null;
            e.childNodes.forEach (element => {
                modifyButton = element.querySelector('#dtModifyButton');
            });
            if (modifyButton) {
                const dataTableOnModifyActionForm = this.api().context[0].oInit.onModifyActionForm;
                
                if (typeof dataTableOnModifyActionForm !== 'undefined') {
                    const onModifyActionForm = document.getElementById (dataTableOnModifyActionForm);
                    if (typeof(onModifyActionForm) != 'undefined' && onModifyActionForm != null) {
                        const modifyKey = myo.UI.getListKeys (dataTableOnModifyActionForm, 'actionread');   // Use the array of the key of the method as primary key
                        modifyButton.setAttribute ('onclick', 
                            'myo.UI.onClickModifyDataTable("' + dataTableOnModifyActionForm + '", ["' + Object.values(myo.UI.getObjKeys (modifyKey, settings)).join(', ') + '"])');
                    } else {
                        if (!this.OnModifyActionFormAlert) {
                            modifyButton.style.display = 'none';
                        }
                    }
                }
                
                // Assignment of the action to the cancel button
                const dataTableOnDeleteActionForm = this.api().context[0].oInit.onDeleteActionForm;
    
                let deleteButton = null;
                e.childNodes.forEach (element => {
                    deleteButton = element.querySelector('#dtDeleteButton');
                });
                if (deleteButton) {
                    const onDeleteActionForm = document.getElementById (dataTableOnDeleteActionForm);
                    if (typeof(onDeleteActionForm) != 'undefined' && onDeleteActionForm != null) {
                        const deleteKey = myo.UI.getListKeys (dataTableOnDeleteActionForm + '-button', 'action');   // Use the array of the key of the method as primary key
                        deleteButton.setAttribute ('onclick', 
                            'myo.UI.onClickDeleteDataTable("' + dataTableOnDeleteActionForm + '", ["' + Object.values (myo.UI.getObjKeys (deleteKey, settings)).join(', ') + '"])');
                    } else {
                        if (!this.OnDeleteActionFormAlert) {
                            deleteButton.style.display = 'none';
                        }
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
            var modalnamedeletebutton = '#' + modalname + '-button';
            var i = 0;
            keys.forEach (function (key) {
                jQuery(modalnamedeletebutton)[0].dataset.actionset = jQuery(modalnamedeletebutton)[0].dataset.action.replace ('{' + key + '}', arraykey[i]);
                i++;
            });

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

            if (keys.count == 0) {
                return false;
            }

            // Read the record
            if ("actionread" in form.dataset) {
                var i = 0;
                keys.forEach (function(key) {
                    response = myo.WS.callReadRequest (form.dataset.actionread.replace ('{' + key + '}', arraykey[i]), 
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    i++;
                });
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
            inputfields = form.getElementsByTagName('input');
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
        getObjKeys(arrayKey, objData) {
            objKey = { };
            arrayKey.forEach (function (key) {
                if (key in objData) {
                    objKey[key] = objData[key];
                }
            });

            return objKey;
        },
        clearFields (form, keys) {
            inputfields = form.getElementsByTagName('input');
            Array.from(inputfields).forEach (function(field) {
                if (keys.includes (field.name)) {  
                    field.value = this.newRecordUUID;
                } else if ("fieldname" in field.dataset) {
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
            inputfields = form.getElementsByTagName('input');
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
                        if (field.hasAttribute('data-ajax--url')) {
                            field.innerHTML = '';
                            url = field.getAttribute('data-ajax--url');
                            if (data[field.dataset.fieldname].hasOwnProperty('split')) {
                                selected = data[field.dataset.fieldname].split('|');
                            } else {
                                selected = [data[field.dataset.fieldname]];
                            }
                            selected.forEach (function(singlesel) {
                                response = myo.WS.callReadRequest  (url + "/" + singlesel, 
                                                                    document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                                optionElement = document.createElement('option');
                                optionElement.value = singlesel;
                                optionElement.textContent = response.data.text;    
                                optionElement.setAttribute('selected', null);
                                field.appendChild(optionElement);
                            });
                        } else {
                            var selection = '|' + data[field.dataset.fieldname] + '|';
                            Array.from(field.childNodes).forEach(function(child) {
                                child.selected = selection.includes('|' + child.value + '|');
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
            var response = [success = false, data=null];
            jQuery.ajax({
                url: url,
                method: 'GET',
                async: false,
                data: {
                    "_token": token
                },
                success: function (result) {
                    conclusion = myo.UI.processAlert (result);
                    response.success = conclusion;
                    response.data = result;
                },
                error: function (result) {
                    myo.UI.processAlert({
                        "_runtimeError": {
                            "message": result.responseText
                        }
                    });
                    conclusion = false; 
                    response.success = conclusion;
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