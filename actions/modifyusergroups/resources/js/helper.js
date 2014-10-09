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

X.actionLoaded(function(event, fn, params) {


	var $form = fn('.form_group_user');
    var $group = $form.find('input[name=group]');
    var $role = $form.find('input[name=role]');
    var $roleOld = $form.find('input[name=roleOld]');
	var action = $form.attr('action');

    function getRowGroup(button) {
        return fn(button).parent().parent().parent().find('input[name=info-IdGroup]').val();
    }

    function getRowRole(button) {
        return fn(button).parent().parent().parent().find('select[name=idRole] option:selected').val();
    }

    function getRowRoleOld(button) {
        return fn(button).parent().parent().parent().find('input[name=info-IdRoleOld]').val();
    }

	var btn1 = fn('.addgroupuser').get(0);
	if(btn1) {
		btn1.beforeSubmit.add(function(event, button) {
			$form.attr('action', action + '&method=suscribegroupuser');
		});
	}

    var cbDelete = function(event, button) {
        $form.attr('action', action + '&method=deletegroupuser');
        $group.val(getRowGroup(button));
    };

    fn('.deletegroupuser').each(function(index, button) {
        button.beforeSubmit.add(cbDelete);
    });

    var cbUpdate = function(event, button) {
        $form.attr('action', action + '&method=updategroupuser');
        $group.val(getRowGroup(button));
        $role.val(getRowRole(button));
        $roleOld.val(getRowRoleOld(button));
    };

    fn('.updategroupuser').each(function(index, button) {
        button.beforeSubmit.add(cbUpdate);
    });

    var cbChange = function() {
        var newRole=fn(this).find('option:selected').val();
        var oldRole=fn(this).parent().parent().find('input[name=info-IdRoleOld]').val();
        if(newRole!=oldRole){
            fn(this).parent().parent().find('.updategroupuser').removeClass('hidden');
        }else{
            fn(this).parent().parent().find('.updategroupuser').addClass('hidden');
        }
    };

    fn('select[name=idRole]').each(function(index, select) {
        fn(this).change(cbChange);
    });
});

