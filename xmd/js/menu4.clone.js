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


//<script>

Menu.prototype.clone = function () {
	var nm = new Menu();
	if ( this.cssFile !== Menu.prototype.cssFile )
		nm.cssFile = this.cssFile;
	if ( this.cssText !== Menu.prototype.cssText )
		nm.cssText = this.cssText;
	if ( this.mouseHoverDisabled !== Menu.prototype.mouseHoverDisabled )
		nm.mouseHoverDisabled = this.mouseHoverDisabled;
	if ( this.showTimeout !== Menu.prototype.showTimeout )
		nm.showTimeout = this.showTimeout;
	if ( this.closeTimeout !== Menu.prototype.closeTimeout )
		nm.closeTimeout = this.closeTimeout;
	
	for ( var i = 0; i < this.items.length; i++ )
		nm.add( this.items[i].clone() );
	
	return nm;
};

MenuItem.prototype.clone = function () {
	var ni = new MenuItem( this.text, this.action, this.icon,
		this.subMenu ? this.subMenu.clone() : null );
	
	if ( this.subMenuDirection !== MenuItem.prototype.subMenuDirection )
		ni.subMenuDirection = this.subMenuDirection;
	if ( this.disabled !== MenuItem.prototype.disabled )
		ni.disabled = this.disabled;
	if ( this.mnemonic !== MenuItem.prototype.mnemonic )
		ni.mnemonic = this.mnemonic;
	if ( this.shortcut !== MenuItem.prototype.shortcut )
		ni.shortcut = this.shortcut;
	if ( this.toolTip !== MenuItem.prototype.toolTip )
		ni.toolTip = this.toolTip;
	if ( this.target !== MenuItem.prototype.target )
		ni.target = this.target;
	if ( this.visible !== MenuItem.prototype.visible )
		ni.visible = this.visible;
		
	return ni;
};

CheckBoxMenuItem.prototype.clone = function () {
	var ni = new CheckBoxMenuItem( this.text, this.checked, this.action,
		this.subMenu ? this.subMenu.clone() : null );
	
	if ( this.subMenuDirection !== CheckBoxMenuItem.prototype.subMenuDirection )
		ni.subMenuDirection = this.subMenuDirection;
	if ( this.disabled !== CheckBoxMenuItem.prototype.disabled )
		ni.disabled = this.disabled;
	if ( this.mnemonic !== CheckBoxMenuItem.prototype.mnemonic )
		ni.mnemonic = this.mnemonic;
	if ( this.shortcut !== CheckBoxMenuItem.prototype.shortcut )
		ni.shortcut = this.shortcut;
	if ( this.toolTip !== CheckBoxMenuItem.prototype.toolTip )
		ni.toolTip = this.toolTip;
	if ( this.target !== CheckBoxMenuItem.prototype.target )
		ni.target = this.target;
	if ( this.visible !== CheckBoxMenuItem.prototype.visible )
		ni.visible = this.visible;
	
	return ni;
};

RadioButtonMenuItem.prototype.clone = function () {
	var ni = new RadioButtonMenuItem( this.text, this.checked, this.radioGroupName,
		this.action, this.subMenu ? this.subMenu.clone() : null );
	
	if ( this.subMenuDirection !== RadioButtonMenuItem.prototype.subMenuDirection )
		ni.subMenuDirection = this.subMenuDirection;
	if ( this.disabled !== RadioButtonMenuItem.prototype.disabled )
		ni.disabled = this.disabled;
	if ( this.mnemonic !== RadioButtonMenuItem.prototype.mnemonic )
		ni.mnemonic = this.mnemonic;
	if ( this.shortcut !== RadioButtonMenuItem.prototype.shortcut )
		ni.shortcut = this.shortcut;
	if ( this.toolTip !== RadioButtonMenuItem.prototype.toolTip )
		ni.toolTip = this.toolTip;
	if ( this.target !== RadioButtonMenuItem.prototype.target )
		ni.target = this.target;
	if ( this.visible !== RadioButtonMenuItem.prototype.visible )
		ni.visible = this.visible;
	
	return ni;
};

MenuSeparator.prototype.clone = function () {
	var ni = new MenuSeparator();
	
	if ( this.subMenuDirection !== MenuSeparator.prototype.subMenuDirection )
		ni.subMenuDirection = this.subMenuDirection;
	if ( this.disabled !== MenuSeparator.prototype.disabled )
		ni.disabled = this.disabled;
	if ( this.mnemonic !== MenuSeparator.prototype.mnemonic )
		ni.mnemonic = this.mnemonic;
	if ( this.shortcut !== MenuSeparator.prototype.shortcut )
		ni.shortcut = this.shortcut;
	if ( this.toolTip !== MenuSeparator.prototype.toolTip )
		ni.toolTip = this.toolTip;
	if ( this.target !== MenuSeparator.prototype.target )
		ni.target = this.target;
	if ( this.visible !== MenuSeparator.prototype.visible )
		ni.visible = this.visible;
		
	return ni;
};

MenuBar.prototype.clone = function () {
	var nm = new MenuBar();
	if ( this.cssFile !== MenuBar.prototype.cssFile )
		nm.cssFile = this.cssFile;
	if ( this.cssText !== MenuBar.prototype.cssText )
		nm.cssText = this.cssText;
	if ( this.mouseHoverDisabled !== MenuBar.prototype.mouseHoverDisabled )
		nm.mouseHoverDisabled = this.mouseHoverDisabled;
	if ( this.showTimeout !== MenuBar.prototype.showTimeout )
		nm.showTimeout = this.showTimeout;
	if ( this.closeTimeout !== MenuBar.prototype.closeTimeout )
		nm.closeTimeout = this.closeTimeout;
	
	for ( var i = 0; i < this.items.length; i++ )
		nm.add( this.items[i].clone() );
	
	return nm;
};

MenuButton.prototype.clone = function () {
	var ni = new MenuButton( this.text, this.subMenu.clone() );

	
	if ( this.subMenuDirection !== MenuButton.prototype.subMenuDirection )
		ni.subMenuDirection = this.subMenuDirection;
	if ( this.disabled !== MenuButton.prototype.disabled )
		ni.disabled = this.disabled;
	if ( this.mnemonic !== MenuButton.prototype.mnemonic )
		ni.mnemonic = this.mnemonic;
	if ( this.shortcut !== MenuButton.prototype.shortcut )
		ni.shortcut = this.shortcut;
	if ( this.toolTip !== MenuButton.prototype.toolTip )
		ni.toolTip = this.toolTip;
	if ( this.target !== MenuButton.prototype.target )
		ni.target = this.target;
	if ( this.visible !== MenuButton.prototype.visible )
		ni.visible = this.visible;
	
	return ni;
};
