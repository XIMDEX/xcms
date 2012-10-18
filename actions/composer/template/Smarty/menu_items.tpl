{**
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
 *}


/*
  --- Menu items --- 
  Note that this structure has changed its format since previous version.
  additional third parameter is added for item scope settings.
  Now this structure is compatible with Tigra Menu GOLD.
  Format description can be found in product documentation.
*/
var MENU_ITEMS = [
	['{t}File{/t}', null, null,
		['<img src="images/icons/envelope.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Messages{/t}', 'javascript:MailModule();', null],
		['<img src="images/icons/key.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Modify user data{/t}', "javascript:addworkspace('{t}Change password{/t}','./chkpass.php');", null],
		['<img src="images/icons/logout.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Close session{/t}', 'javascript:LogOut();',null],
	],
	['{t}View{/t}', null, null,
		['<img src="images/icons/tree_ximdex.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Ximdex tree{/t}', 'javascript:ToggleTree(true);', null],
		['<img src="images/icons/hand.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Status bar{/t}', 'javascript:ToggleStatus(true);', null],
	],
	['{t}Help{/t}', null, null,
		['<img src="images/icons/help.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Ximdex manual{/t}', 'javascript: ayuda();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}About Ximdex{/t}',"javascript:alert('{$versionname} (c) Open Ximdex Evolution SL 2.006.');", null]
	]

	{if $can_close_ximdex}
	, ['{t}Close{/t}', null, null,
		['<img src="images/icons/help.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Stop scripts{/t}', "javascript: addworkspace('{t}Stop scripts{/t}','../xmd/loadaction.php?nodeid=NULL&amp;action=ximdexclose&amp;method=index');", null]
	]
	{/if}

	, ['{t}Reports{/t}', null, null,
		['{t}Actions{/t}', null, null,
			['<img src="images/icons/help.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Action totals{/t}', "javascript: addworkspace('{t}Action statistics{/t}','../xmd/loadaction.php?nodeid=NULL&amp;action=actionsstats&amp;method=index');", null],
			['<img src="images/icons/help.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Time average{/t}', "javascript: addworkspace('{t}Action statistics{/t}','../xmd/loadaction.php?nodeid=NULL&amp;action=actionsstats&amp;method=average');", null]
		] ,

		{if ($ximSYNC)}
			['{t}Synchronizer{/t}', null, null,
				['<img src="images/icons/help.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Vista I{/t}', "javascript: addworkspace('{t}Sync. Manager{t/}','..{$smarty.const.MODULE_XIMSYNC_PATH}/actions/managebatchs/init.php?mode=action');", null],
				['<img src="images/icons/help.gif" border="0" style="float:left;" />&nbsp;&nbsp;{t}Vista II{/t}', "javascript: addworkspace('{t}Sync. Manager{/t}','..{$smarty.const.MODULE_XIMSYNC_PATH}/actions/managebatchs/init.php');", null],
			]
		{/if}

	]

	{if ($usuario_admin)}
	, ['{t}Debug{/t}', null, null,
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}View logs{/t}', 'javascript: openDebugConsole();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}Actions on windows{/t}', 'javascript: actions_open_in_window();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}Smarty console{/t}', 'javascript:open_debug_smarty();', null],
	  ], 
		['{t}Tools{/t}', null, null,
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}Filter nodes{/t}', 'javascript: debug_filter();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}Grep search{/t}', 'javascript:find_grep();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}Close tabs{/t}', 'javascript:cerrar_tabs();', null],
	  ], 
	 ['{t}Info{/t}', null, null,
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}Active modules{/t}', 'javascript:modulos();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;Node Info', 'javascript:nodeinfo();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;PhpInfo', 'javascript:phpinfo();', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;{t}View id nodes{/t}', 'javascript:ver_idnodos();', null],
	  ],
	 ['{t}Renders{/t}', null, null,
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;Smarty', 'javascript:debug_renders(1);', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;JSON', 'javascript:debug_renders(2);', null],
		['<img src="images/icons/about.png" border="0" style="float:left;" />&nbsp;&nbsp;Debug', 'javascript:debug_renders(3);', null],
	  ],
	 ['{t}Languages{/t} ', null, null,
		['<img src="images/menu/spain.gif" border="0" style="float:left; height: 15px;" />&nbsp;&nbsp;{t}Spanish{/t}', 'javascript:change_lang(\'es\');', null],
		['<img src="images/menu/greatbr.gif" border="0" style="float:left; height: 15px;" />&nbsp;&nbsp;{t}English{/t}', 'javascript:change_lang(\'en\');', null],
		['<img src="images/menu/germany.gif" border="0" style="float:left; height: 15px;" />&nbsp;&nbsp;{t}German{/t}', 'javascript:change_lang(\'de\');', null],
	  ]
	{/if}
];
