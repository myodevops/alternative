myo = {
    UI: {
        newRecordUUID: 'af24f1d4-2916-4c55-a526-9f6217466ec3',
        dttable: null,

        initializeDataTable (tableRef) {
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

                var insertAllowed = dttable.context[0].oInit.insertAllowed;
                var dataTableOnModifyActionForm = dttable.context[0].oInit.onModifyActionForm;    
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

            table = document.getElementById(tableRef);
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
            // Assignment of the action to the cancel button
            var modifyButton = e.childNodes.item(3).childNodes.item(0).childNodes.item(0).children.namedItem('dtModifyButton');
            var dataTableOnModifyActionForm = this.api().context[0].oInit.onModifyActionForm;
            
            if (typeof dataTableOnModifyActionForm !== 'undefined') {
                var modifyKey = myo.UI.getListKeys (dataTableOnModifyActionForm, 'actionread');   // Use the array of the key of the method as primary key

                modifyButton.setAttribute ('onclick', 
                   'myo.UI.onClickModifyDataTable("' + dataTableOnModifyActionForm + '", [' + Object.values(myo.UI.getObjKeys (modifyKey, settings)).join(', ') + '])');
            }
            
            // Assignment of the action to the cancel button
            var deleteButton = e.childNodes.item(3).childNodes.item(0).childNodes.item(0).children.namedItem('dtDeleteButton');
            var dataTableOnDeleteActionForm = this.api().context[0].oInit.onDeleteActionForm;

            if (typeof dataTableOnDeleteActionForm !== 'undefined') {
                var deleteKey = myo.UI.getListKeys (dataTableOnDeleteActionForm + '-button', 'action');   // Use the array of the key of the method as primary key
                pippo = 'myo.UI.onClickDeleteDataTable("' + dataTableOnDeleteActionForm + '", [' + Object.values (myo.UI.getObjKeys (deleteKey, settings)).join(', ') + '])';
                deleteButton.setAttribute ('onclick', 
                  'myo.UI.onClickDeleteDataTable("' + dataTableOnDeleteActionForm + '", [' + Object.values (myo.UI.getObjKeys (deleteKey, settings)).join(', ') + '])');
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
                alert ("Invalid email!");
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
                    data[field.dataset.fieldname] = '|';
                    Array.from(field.children).forEach(function(child) {
                        if (child.selected) {
                            data[field.dataset.fieldname] = data[field.dataset.fieldname] + child.value + '|';
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
                    Array.from(field.children).forEach(function(child) {
                        child.selected = false;
                    });
                }        
            });
        },
        populateFields (form, data) {
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
                        Array.from(field.childNodes).forEach(function(child) {
                            child.selected = data[field.dataset.fieldname].includes('|' + child.value + '|');
                        });
                        myo.UI.changeField(field);
                    }
                }        
            });
        },
        changeField(field) {
            var event = new Event('change');
            field.dispatchEvent(event);
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
                    response.success = true;
                    response.data = result;
                },
                error: function (result) {
                    response.success = false;
                    response.data = result.responseJSON.text;
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
                    conclusion = true;
                },
                error: function (result) {
                    conclusion = result.statusText;
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
                    conclusion = true;
                },
                error: function (result) {
                    conclusion = result.responseJSON.text;
                },
            });

            return conclusion;
        }
    }
}