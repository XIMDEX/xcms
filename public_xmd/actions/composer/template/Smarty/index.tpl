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


<HTML>
<HEAD>
    <HTA:APPLICATION ID="ximdex"
     APPLICATIONNAME="myApp"
     BORDER="normal"
     BORDERSTYLE="normal"
     CAPTION="yes"
     CONTEXTMENU="yes"
     ICON="images/xmd.ico"
     INNERBORDER="yes"
     MAXIMIZEBUTTON="yes"
     MINIMIZEBUTTON="yes"
     NAVIGABLE="yes"
     SCROLL="yes"
     SCROLLFLAT="yes"
     SELECTION="no"
     SHOWINTASKBAR="yes"
     SINGLEINSTANCE="yes"
     SYSMENU="yes">
 
<TITLE> {$versionname}</TITLE>

<link rel="stylesheet" type="text/css" href="{$_URL_ROOT}{$base_action}resources/css/index.css">

<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/index.js"></script> 

</HEAD>
<frameset rows="88,*,22" id="workwindow" name="workwindow" style="border: 0;" border="1" framespacing="0" onBeforeUnload="return '  ';" >
	<frame application="yes" name="toolbar" src="{$composer_index}?method=driver" marginwidth="0" marginheight="0" scrolling="no" frameborder="no" noresize />
	<frameset cols="225,*" id="workspaces" name="workspaces" framespacing="0" border="1" scrolling="no">
			<frame application="yes" id="tree" name="tree" src="{$composer_index}?method=treecontainer" scrolling="no" frameborder="no" style="border: 0px;" />
			<frame application="yes" id="content" name="content" src="{$composer_index}?method=content" scrolling="no" frameborder="no" style="border: 0px;" >
	</frameset>
	<frame application="yes" id="statsubar" name="status" src="{$composer_index}?method=status" marginwidth="0" marginheight="0" scrolling="no" frameborder="no" noresize>
</frameset>

</HTML>
