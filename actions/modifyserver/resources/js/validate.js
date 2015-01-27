/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

X.actionLoaded(function (event, fn, params) {

    var form = fn('form');
    var fm = form.get(0).getFormMgr();
    var valid = true;

    // Creates an alias for convenience
    var empty = Object.isEmpty;

    clearErrors();
    fn('#protocol input[type="radio"]').each(function () {
        if (fn(this).attr("checked")) {
            show_local_fields(fn(this).val());
        }
    });

    fn('#protocol input[type="radio"]').click(function () {
        show_local_fields(fn(this).val());
    });

    //On click, reload the action with the selected server id. This will return the action with the input's value
    fn('div#serverid .row_item_selectable').click(function () {
        var urler = fn('#nodeURL').val() + '&serverid=' + fn(this).attr("value");
        fn('#mdfsv_form').attr('action', urler);
        fm.sendForm();
        return false;
    });

    fn('#delete_server').click(function (event) {
        if (fn('#serverid').val() != "none") {
            fn('input[name=borrar]').val(1);
            confirm_dialog(event, _('Are you sure you want to remove this server?'), form, fm);
            return true;
        }
    });

    //On click, reload the action without server id, so the inputs will be empty.
    fn("div.create-server").click(function (e) {
        var urler = fn("#nodeURL").val();
        fn("#mdfsv_form").attr("action", urler);
        fm.sendForm();
        return false;
    });
    fn('#create_server').get(0).beforeSubmit.add(function (event) {
        console.log("Firing beforesubmit", event);
        clearErrors();
        var protocolSelected = fn('#protocol').val();

        if (protocolSelected == 'LOCAL') {
            if (empty(fn('#url').val())) {
                addError(_('It is necessary to specify a local url for this server.'));
            } else if (empty(fn('#initialdirectory').val())) {
                addError(_('It is necessary to specify a local directory.'));
            }

        } else if ((protocolSelected == 'FTP') || (protocolSelected == 'SSH')) {
            var pw1 = fn('#password').val();
            if (empty(pw1)) {
                addError(_('A password ir required.'));
            } else if (empty(fn('#initialdirectory').val())) {
                addError(_('It is necessary to specify a remote directory.'));
            } else if (empty(fn('#url').val())) {
                addError(_('It is necessary to specify a remote url for this server.'));
            } else if (empty(fn('#port').val())) {
                addError(_('It is necessary to specify a connection port for this server.'));
            } else if (empty(fn('#host').val())) {
                addError(_('It is necessary to specify a remote address for this server.'));
            } else if (empty(fn('#login').val())) {
                addError(_('It is necesary specify a login.'));
            }
        }

        if (empty(fn('#description').val())) {
            addError(_('It is necessary to specify a description for this server.'));
        }

        if (!valid) {
            $(form).closest('.action_container').scrollTop(0);
            return true;
        }

        var msg = _('Are you sure you want to modify these properties?');
        if (fn('#enabled').attr('checked') == false) {
            msg = _('Server is not enabled. Documents will not be published on this server.') + msg;
        }
        confirm_dialog(event, msg, form, fm);

        return true;
    });

    function confirm_dialog(event, msg, form, fm) {

        var div_dialog = $("<div/>").attr('id', 'dialog').appendTo(form);

        var dialogCallback = function (send) {
            $(div_dialog).dialog("destroy");
            if (send) {
                fm.sendForm({
                    button: event.currentTarget,
                    confirm: false,
                    jsonResponse: true
                });
            }
        }.bind(this);

        div_dialog.html(msg);
        var dialogButtons = {};
        dialogButtons[_('Accept')] = function () {
            dialogCallback(true);
        };
        dialogButtons[_('Cancel')] = function () {
            dialogCallback(false);
        };
        div_dialog.dialog({
            buttons: {
                accept: function () {
                    dialogCallback(true);
                },
                cancel: function () {
                    dialogCallback(false);
                }
            }
        });
    }

    function show_local_fields(label) {
        var data = '';
        if (typeof label !== 'undefined') {
            data = label;
        }

        if (label === 'LOCAL') {
            var url = fn("input[name=url]");

            if (null == url.val() || "" == url.val()) {
                url.val(url_root + "/data/previos");
            }
            
            var directory = fn("input[name=initialdirectory]");
            if (null == directory.val() || "" == directory.val()) {
                directory.val(ximdex_root + "/data/previos/");
            }
            fn('#labelDirectorio').text(_('Directory'));
            fn('#labeldirRemota').text(_('Address'));
            fn('.not_local').hide();
        } else {
            if (label === 'SOLR') {
                fn('#labelDirectorio').text(_('Core'));
            }
            else {
                fn('#labelUrl').text(_('Remote URL'));
                fn('#labelDirectorio').text(_('Remote directory'));
            }
            
            fn('#labeldirRemota').text(_('Remote address'));
            fn('.password').show();
            fn('.port').show();
            fn('.login').show();
            fn('.host').show();
        }
    }

    function clearErrors() {
        fn('fieldset.mdfsv_errors').hide();
        fn('fieldset.mdfsv_errors div.errors-container').empty();
        valid = true;
    }

    function addError(message) {
        var container = $('<div></div>').addClass('ui-state-error ui-corner-all msg-error');
        var icon = $('<span></span>').addClass('ui-icon ui-icon-alert').appendTo(container);
        var msg = $('<span></span>').html(message).appendTo(container);
        fn('fieldset.mdfsv_errors div.errors-container').append(container);
        fn('fieldset.mdfsv_errors').show();
        valid = false;
    }

});
