myo = {
    UI: {
        dttable: null,

        initializeDataTable (tableRef) {
            $('#'+tableRef).on('init.dt', function (e) {
                dttable = $('#'+tableRef).DataTable();
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
                modifyButton.setAttribute ('onclick', 'myo.UI.onClickModifyDataTable("' + dataTableOnModifyActionForm + '", "' + settings.id + '")');
            }
            
            // Assignment of the action to the cancel button
            var deleteButton = e.childNodes.item(3).childNodes.item(0).childNodes.item(0).children.namedItem('dtDeleteButton');
            var dataTableOnDeleteActionForm = this.api().context[0].oInit.onDeleteActionForm;

            if (typeof dataTableOnDeleteActionForm !== 'undefined') {
                deleteButton.setAttribute ('onclick', 'myo.UI.onClickDeleteDataTable("' + dataTableOnDeleteActionForm + '", "' + settings.id + '")');
            }
        },
        onClickModifyDataTable(modalname, id){
            jQuery.noConflict();

            // Set the parameter of the confirm button 
            var modalnamemodifybutton = '#' + modalname + '-button';
            jQuery(modalnamemodifybutton)[0].dataset.actionset = jQuery(modalnamemodifybutton)[0].dataset.actionread.replace ('{id}', id);
            jQuery(modalnamemodifybutton)[0].dataset.actionset = jQuery(modalnamemodifybutton)[0].dataset.actionwrite.replace ('{id}', id);
            
            // Show the confirm modal
            jQuery('#' + modalname).modal('show');            
        },
        onClickDeleteDataTable(modalname, id){
            jQuery.noConflict();

            // Set the parameter of the confirm button 
            var modalnamedeletebutton = '#' + modalname + '-button';
            jQuery(modalnamedeletebutton)[0].dataset.actionset = jQuery(modalnamedeletebutton)[0].dataset.action.replace ('{id}', id);
            
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
        }  
    },
    WS: {
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