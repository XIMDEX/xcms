#/**
# *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
# *
# *  Ximdex a Semantic Content Management System (CMS)
# *
# *  This program is free software: you can redistribute it and/or modify
# *  it under the terms of the GNU Affero General Public License as published
# *  by the Free Software Foundation, either version 3 of the License, or
# *  (at your option) any later version.
# *
# *  This program is distributed in the hope that it will be useful,
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# *  GNU Affero General Public License for more details.
# *
# *  See the Affero GNU General Public License for more details.
# *  You should have received a copy of the Affero GNU General Public License
# *  version 3 along with Ximdex (see LICENSE file).
# *
# *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
# *
# *  @author Ximdex DevTeam <dev@ximdex.com>
# *  @version $Revision$
# */

-- Activation of the actions associated to ximIO

CREATE TABLE `XimIONodeTranslations` (
  `IdNodeTranslation` int(11) NOT NULL auto_increment,
  `IdXimioExportation` int(11) NOT NULL,
  `IdExportationNode` int(11) NOT NULL,
  `IdImportationNode` int(11) NOT NULL,
  `IdExportationParent` int(11) NOT NULL,
  `status` int(3) NOT NULL,
  `path` varchar(255) NULL,
  PRIMARY KEY  (`IdNodeTranslation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `XimIOExportations` (
  `idXimIOExportation` int(11) NOT NULL auto_increment,
  `idXimIO` VARCHAR(50) NOT NULL,
  `timeStamp` varchar(200) NOT NULL,
  PRIMARY KEY  (`idXimIOExportation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
