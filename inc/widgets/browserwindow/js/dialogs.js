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
 *  @version $Revision: 7842 $
 */


X.browser.BrowserInputDialog = Object.xo_create(X.dialogs.InputDialog, {

	_init: function(options) {
		X.browser.BrowserInputDialog._construct(this, options);
	},
	
	showMessages: function(messages) {
		$('.dialog-input ~ div.dialog-message', this.dialog).remove();
		$.each(messages, function(index, item) {
			$(this.input).after(
				$('<div/>')
					.addClass('dialog-message')
					.html(item.message)
					.appendTo(this.dialog)
			);
		}.bind(this));
	}
});

X.browser.NewSetDialog = Object.xo_create(X.browser.BrowserInputDialog, {

	_init: function(options) {
		X.browser.NewSetDialog._construct(this, Object.extend({
			id: 'newsetdialog',
			title: 'Creating new set',
			message: 'New set name:',
			height: 400,
			buttons: [
				{text: 'Create set', value: 301, onPress: this.createSet.bind(this)},
				X.buttons.BTN_CANCEL
			],
			keypress: this.createSet.bind(this)
		}, options));
		
		$(this.dialog).append(
			$('<div/>')
				.addClass('dialog-content')
				.html('Other readers:')
		);
		
		var container = $('<div/>').addClass('dialog-users-container');
		$(this.dialog).append(container);

		this.userslist = new X.browser.UsersList({
			element: container,
			rest: {
				action: 'browser3',
				method: 'getUsers'
			}
		});
		this.userslist.show();
			
	},
	
	open: function(nodes) {
		if (!Object.isEmpty(nodes)) this.options.nodes = nodes;
		X.browser.NewSetDialog._super(this, 'open');
	},
	
	clear: function() {
		$(this.input).val('');
		this.options.nodes = null;
	},
	
	createSet: function() {
	
		var name = this.getInput() || '';
		name = name.trim();
		if (name.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The set name cannot be empty.'
			}]);
			return;
		}
		
		var users = $(this.userslist.getSelected()).map(function(index, item) {
			return item.id;
		});
		users = $.makeArray(users);
		
		var nodes = Object.isArray(this.options.nodes) ?this.options.nodes : [];
	
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'addSet',
				name: name,
				'users[]': users,
				'nodes[]': nodes
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
//					this.showMessages(data);
					var dialog = new X.dialogs.MessageDialog({
						owner: this,
						message: data
					});
					dialog.open();
				}
				
				this.trigger('onOk');
				this.close();
				//this.options.owner.updateSets();
			}.bind(this)
		);
	}
	
});

X.browser.ManageSetReadersDialog = Object.xo_create(X.dialogs.MessageDialog, {

	_init: function(options) {
		X.browser.ManageSetReadersDialog._construct(this, Object.extend({
			id: 'managereadersdialog',
			title: 'Managing set readers',
			message: 'Set readers',
			height: 400,
			buttons: [
				{text: 'Update', value: 301, onPress: this.updateReaders.bind(this)},
				X.buttons.BTN_CANCEL
			],
			keypress: this.updateReaders.bind(this)
		}, options));
				
		var container = $('<div/>').addClass('dialog-users-container');
		$(this.dialog).append(container);

		this.userslist = new X.browser.UsersList({
			element: container,
			setid: options.setid,
			rest: {
				action: 'browser3',
				method: 'getUsers'
			}
		});
		this.userslist.show();
	},
	
	updateReaders: function() {

		var users = $(this.userslist.getSelected()).map(function(index, item) {
			return item.id;
		});
		users = $.makeArray(users);
	
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'updateSetUsers',
				setid: this.options.setid,
				'users[]': users
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					var dialog = new X.dialogs.MessageDialog({
						owner: this,
						message: data
					});
					dialog.open();
				}
				this.close();
				this.options.owner.updateSets();
			}.bind(this)
		);
	}
});

X.browser.RenameSetDialog = Object.xo_create(X.browser.BrowserInputDialog, {

	_init: function(options) {
		X.browser.RenameSetDialog._construct(this, Object.extend({
			id: 'renamesetdialog',
			title: 'Renaming set',
			message: 'New set name:',
			buttons: [
				{text: 'Rename set', value: 301, onPress: this.renameSet.bind(this)},
				X.buttons.BTN_CANCEL
			],
			keypress: this.renameSet.bind(this)
		}, options));
	},
	
	renameSet: function() {
	
		var name = this.getInput() || '';
		name = name.trim();
		if (name.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The set name cannot be empty.'
			}]);
			return;
		}
		
		if (this.options.setid < 1) {
			this.showMessages([{
				type: 0,
				message: 'The set id cannot be empty.'
			}]);
			return;
		}
	
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'renameSet',
				setid: this.options.setid,
				name: name
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					this.showMessages(data);
				} else {
					this.options.owner.updateSets();
					this.close();
				}
			}.bind(this)
		);
	}
	
});

X.browser.DeleteSetDialog = Object.xo_create(X.dialogs.MessageDialog, {

	_init: function(options) {
		X.browser.DeleteSetDialog._construct(this, Object.extend({
			id: 'deletesetdialog',
			title: 'Deleting set',
			message: 'Are you sure you want to delete the selected set?',
			buttons: [
				{text: 'Delete set', value: 301, onPress: this.deleteSet.bind(this)},
				X.buttons.BTN_NO
			],
			keypress: this.deleteSet.bind(this)
		}, options));
	},
	
	deleteSet: function() {
	
		if (this.options.setid < 1) {
			var dialog = new X.dialogs.MessageDialog({
				owner: this,
				message: 'The set id cannot be empty.'
			});
			dialog.open();
			return;
		}
		
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'deleteSet',
				setid: this.options.setid
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					var dialog = new X.dialogs.MessageDialog({
						owner: this,
						message: data
					});
					dialog.open();
				}
				this.options.owner.updateSets();
				this.close();
			}.bind(this)
		);	
	}
});

X.browser.DeleteNodesFromSetDialog = Object.xo_create(X.dialogs.MessageDialog, {

	_init: function(options) {
		X.browser.DeleteNodesFromSetDialog._construct(this, Object.extend({
			id: 'deletenodesfromsetdialog',
			title: 'Deleting nodes from set',
			message: 'Are you sure you want to delete the selected nodes from the set?',
			buttons: [
				{text: 'Delete nodes', value: 301, onPress: this.deleteNodes.bind(this)},
				X.buttons.BTN_NO
			],
			keypress: this.deleteNodes.bind(this)
		}, options));
	},
	
	deleteNodes: function() {
	
		if (this.options.setid < 1) {
			var dialog = new X.dialogs.MessageDialog({
				owner: this,
				message: 'The set id cannot be empty.'
			});
			dialog.open();
			return;
		}
		
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'deleteNodeFromSet',
				setid: this.options.setid,
				'nodes[]': this.options.nodes
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					var dialog = new X.dialogs.MessageDialog({
						owner: this,
						message: data
					});
					dialog.open();
				}
				this.options.owner.updateSets();
				this.options.owner.onSetbuttonClick(null, {id: this.options.setid});
				this.close();
			}.bind(this)
		);
	}
});

X.browser.NewFilterDialog = Object.xo_create(X.browser.BrowserInputDialog, {

	_init: function(options) {
		X.browser.NewFilterDialog._construct(this, Object.extend({
			id: 'newfilterdialog',
			title: 'Creating new filter',
			message: 'New filter name:',
			handler: 'SQL',
			query: '',
			buttons: [
				{text: 'Create filter', value: 301, onPress: this.createFilter.bind(this)},
				X.buttons.BTN_CANCEL
			],
			keypress: this.createFilter.bind(this)
		}, options));
	},
	
	open: function(query) {
		this.showMessages([]);
		if (!Object.isEmpty(query)) this.options.query = query;
		X.browser.NewFilterDialog._super(this, 'open');
	},
	
	clear: function() {
		$(this.input).val('');
	},
	
	createFilter: function() {
	
		var name = this.getInput() || '';
		name = name.trim();
		if (name.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The filter name cannot be empty.'
			}]);
			return;
		}
		
		var handler = this.options.handler.trim();
		if (handler.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The filter handler cannot be empty.'
			}]);
			return;
		}
		
		var filter = this.options.query.getQuery('XML').trim();
		if (filter.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The filter cannot be empty.'
			}]);
			return;
		}
	
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'addFilter',
				name: name,
				handler: handler,
				filter: filter
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					this.showMessages(data);
					this.trigger('onError');
				} else {
					this.trigger('onOk');
					/*if (this.options.owner && Object.isFunction(this.options.owner.updateFilters)) {
						this.options.owner.updateFilters();
					}*/
					this.close();
				}
			}.bind(this)
		);
	}
	
});

X.browser.RenameFilterDialog = Object.xo_create(X.browser.BrowserInputDialog, {

	_init: function(options) {
		X.browser.RenameFilterDialog._construct(this, Object.extend({
			id: 'renamefilterdialog',
			title: 'Renaming filter',
			message: 'New filter name:',
			buttons: [
				{text: 'Rename filter', value: 301, onPress: this.renameFilter.bind(this)},
				X.buttons.BTN_CANCEL
			],
			keypress: this.renameFilter.bind(this)
		}, options));
	},
	
	renameFilter: function() {
	
		var name = this.getInput() || '';
		name = name.trim();
		if (name.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The filter name cannot be empty.'
			}]);
			return;
		}
		
		if (this.options.filterid < 1) {
			this.showMessages([{
				type: 0,
				message: 'The filter id cannot be empty.'
			}]);
			return;
		}
	
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'renameFilter',
				filterid: this.options.filterid,
				name: name
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					this.showMessages(data);
				} else {
					this.options.owner.updateFilters();
					this.close();
				}
			}.bind(this)
		);
	}
	
});

X.browser.DeleteFilterDialog = Object.xo_create(X.dialogs.MessageDialog, {

	_init: function(options) {
		X.browser.DeleteFilterDialog._construct(this, Object.extend({
			id: 'deletefilterdialog',
			title: 'Deleting filter',
			message: 'Are you sure you want to delete the selected filter?',
			buttons: [
				{text: 'Delete filter', value: 301, onPress: this.deleteFilter.bind(this)},
				X.buttons.BTN_NO
			],
			keypress: this.deleteFilter.bind(this)
		}, options));
	},
	
	deleteFilter: function() {
	
		if (this.options.filterid < 1) {
			var dialog = new X.dialogs.MessageDialog({
				owner: this,
				message: 'The filter id cannot be empty.'
			});
			dialog.open();
			return;
		}
		
		$.post(
			'%s/xmd/loadaction.php'.printf(window.url_root),
			{
				action: 'browser3',
				method: 'deleteFilter',
				filterid: this.options.filterid
			},
			function(data, textStatus) {
				data = eval(data);
				if (data.length > 0) {
					var dialog = new X.dialogs.MessageDialog({
						owner: this,
						message: data
					});
					dialog.open();
				}
				this.options.owner.updateFilters();
				this.close();
			}.bind(this)
		);	
	}
});

X.browser.SaveFilterAsSetDialog = Object.xo_create(X.browser.BrowserInputDialog, {

	_init: function(options) {
		X.browser.SaveFilterAsSetDialog._construct(this, Object.extend({
			id: 'savefilterassetdialog',
			title: 'Save filter as set',
			message: 'New set name:',
			buttons: [
				{text: 'Save', value: 301, onPress: this.saveFilter.bind(this)},
				X.buttons.BTN_CANCEL
			],
			keypress: this.saveFilter.bind(this)
		}, options));
	},
	
	saveFilter: function() {
	
		var name = this.getInput() || '';
		name = name.trim();
		if (name.length == 0) {
			this.showMessages([{
				type: 0,
				message: 'The set name cannot be empty.'
			}]);
			return;
		}
			
		var ds = $('.bwAccordion .filters-container', this.options.owner.element).data('datastore');
		ds.load_data({params: {filterid: this.options.filterid}}, function(ds) {
			
			var nodes = [];
			$.each(ds.data, function(index, item) {
				nodes.push(item.nodeid.value);
			});
	
			$.post(
				'%s/xmd/loadaction.php'.printf(window.url_root),
				{
					action: 'browser3',
					method: 'addSet',
					name: name,
					'nodes[]': nodes
				},
				function(data, textStatus) {
					data = eval(data);
					if (data.length > 0) {
						var dialog = new X.dialogs.MessageDialog({
							owner: this,
							title: 'Save filter as set',
							message: data
						});
						dialog.open();
					}
					this.options.owner.updateSets();
					this.close();
				}.bind(this)
			);
			
		}.bind(this));
	}

});

