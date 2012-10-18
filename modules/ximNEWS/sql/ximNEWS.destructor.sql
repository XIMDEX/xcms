#/**
# *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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

-- Deleting module actions

DELETE FROM Actions where Module = 'ximNEWS';
DELETE FROM Actions where IdNodeType >= 5300 AND IdNodeType <= 5320;
DELETE FROM Actions where IdAction = 7241;
DELETE FROM Actions where IdAction >= 7313 AND IDAction <= 7316;

-- Deleting nodes corresponding with actions

DELETE FROM `Nodes` WHERE IdNode = 6700;
DELETE FROM `Nodes` WHERE IdNode = 6701;
DELETE FROM `Nodes` WHERE IdNode = 6702;
DELETE FROM `Nodes` WHERE IdNode = 6703;
DELETE FROM `Nodes` WHERE IdNode = 6704;
DELETE FROM `Nodes` WHERE IdNode = 6706;
DELETE FROM `Nodes` WHERE IdNode = 6707;
DELETE FROM `Nodes` WHERE IdNode = 6708;
DELETE FROM `Nodes` WHERE IdNode = 6709;
DELETE FROM `Nodes` WHERE IdNode = 6710;
DELETE FROM `Nodes` WHERE IdNode = 6713;
DELETE FROM `Nodes` WHERE IdNode = 6714;
DELETE FROM `Nodes` WHERE IdNode = 6715;
DELETE FROM `Nodes` WHERE IdNode = 6716;
DELETE FROM `Nodes` WHERE IdNode = 6717;
DELETE FROM `Nodes` WHERE IdNode = 6720;
DELETE FROM `Nodes` WHERE IdNode = 6721;
DELETE FROM `Nodes` WHERE IdNode = 6723;
DELETE FROM `Nodes` WHERE IdNode = 6724;
DELETE FROM `Nodes` WHERE IdNode = 6725;
DELETE FROM `Nodes` WHERE IdNode = 6726;
DELETE FROM `Nodes` WHERE IdNode = 6727;
DELETE FROM `Nodes` WHERE IdNode = 6728;
DELETE FROM `Nodes` WHERE IdNode = 6729;
DELETE FROM `Nodes` WHERE IdNode = 6730;
DELETE FROM `Nodes` WHERE IdNode = 6731;
DELETE FROM `Nodes` WHERE IdNode = 6740;
DELETE FROM `Nodes` WHERE IdNode = 6741;
DELETE FROM `Nodes` WHERE IdNode = 6742;
DELETE FROM `Nodes` WHERE IdNode = 6745;
DELETE FROM `Nodes` WHERE IdNode = 6747;
DELETE FROM `Nodes` WHERE IdNode = 6750;
DELETE FROM `Nodes` WHERE IdNode = 6751;
DELETE FROM `Nodes` WHERE IdNode = 6752;
DELETE FROM `Nodes` WHERE IdNode = 6755;
DELETE FROM `Nodes` WHERE IdNode = 6757;
DELETE FROM `Nodes` WHERE IdNode = 6759;
DELETE FROM `Nodes` WHERE IdNode = 6770;
DELETE FROM `Nodes` WHERE IdNode = 6771;
DELETE FROM `Nodes` WHERE IdNode = 5320;
DELETE FROM `Nodes` WHERE IdNode = 5300;
DELETE FROM `Nodes` WHERE IdNode = 5301;

DELETE FROM Nodes where IdParent >= 5300 AND IdParent <= 5320;

-- Dleeting actions from RelRolesActions
DELETE FROM `RelRolesActions` WHERE IdAction = 6700;
DELETE FROM `RelRolesActions` WHERE IdAction = 6701;
DELETE FROM `RelRolesActions` WHERE IdAction = 6702;
DELETE FROM `RelRolesActions` WHERE IdAction = 6703;
DELETE FROM `RelRolesActions` WHERE IdAction = 6704;
DELETE FROM `RelRolesActions` WHERE IdAction = 6706;
DELETE FROM `RelRolesActions` WHERE IdAction = 6707;
DELETE FROM `RelRolesActions` WHERE IdAction = 6708;
DELETE FROM `RelRolesActions` WHERE IdAction = 6709;
DELETE FROM `RelRolesActions` WHERE IdAction = 6710;
DELETE FROM `RelRolesActions` WHERE IdAction = 6713;
DELETE FROM `RelRolesActions` WHERE IdAction = 6714;
DELETE FROM `RelRolesActions` WHERE IdAction = 6715;
DELETE FROM `RelRolesActions` WHERE IdAction = 6716;
DELETE FROM `RelRolesActions` WHERE IdAction = 6717;
DELETE FROM `RelRolesActions` WHERE IdAction = 6720;
DELETE FROM `RelRolesActions` WHERE IdAction = 6721;
DELETE FROM `RelRolesActions` WHERE IdAction = 6723;
DELETE FROM `RelRolesActions` WHERE IdAction = 6724;
DELETE FROM `RelRolesActions` WHERE IdAction = 6725;
DELETE FROM `RelRolesActions` WHERE IdAction = 6726;
DELETE FROM `RelRolesActions` WHERE IdAction = 6727;
DELETE FROM `RelRolesActions` WHERE IdAction = 6728;
DELETE FROM `RelRolesActions` WHERE IdAction = 6729;
DELETE FROM `RelRolesActions` WHERE IdAction = 6730;
DELETE FROM `RelRolesActions` WHERE IdAction = 6731;
DELETE FROM `RelRolesActions` WHERE IdAction = 6740;
DELETE FROM `RelRolesActions` WHERE IdAction = 6741;
DELETE FROM `RelRolesActions` WHERE IdAction = 6742;
DELETE FROM `RelRolesActions` WHERE IdAction = 6745;
DELETE FROM `RelRolesActions` WHERE IdAction = 6747;
DELETE FROM `RelRolesActions` WHERE IdAction = 6750;
DELETE FROM `RelRolesActions` WHERE IdAction = 6751;
DELETE FROM `RelRolesActions` WHERE IdAction = 6752;
DELETE FROM `RelRolesActions` WHERE IdAction = 6755;
DELETE FROM `RelRolesActions` WHERE IdAction = 6757;
DELETE FROM `RelRolesActions` WHERE IdAction = 6759;
DELETE FROM `RelRolesActions` WHERE IdAction = 6770;
DELETE FROM `RelRolesActions` WHERE IdAction = 6771;
DELETE FROM `RelRolesActions` WHERE IdAction = 7241;


-- Deleting NodeTypes
-- This first select has not into account all the constructor
DELETE FROM `NodeTypes` WHERE Module = 'ximNEWS';
DELETE FROM `NodeTypes` WHERE IdNodeType >= 5300 AND IdNodeType <= 5320;

-- Deleting NodeDefaultContents

DELETE FROM `NodeDefaultContents` WHERE IdNodeType = '5300';

-- Deleting NodeAllowedContents

DELETE FROM `NodeAllowedContents` WHERE NodeType = '5300';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5300';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5301';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5302';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5303';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5304';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5305';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5306';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5310';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5320';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5312';
DELETE FROM `NodeAllowedContents` WHERE IdNodeType = '5313';

-- Deleting Config

DELETE FROM `Config` WHERE ConfigKey = 'StartCheckNoFuelle';
DELETE FROM `Config` WHERE ConfigKey = 'EndCheckNoFuelle';
DELETE FROM `Config` WHERE ConfigKey = 'ToleranciaFuelle';
DELETE FROM `Config` WHERE ConfigKey = 'RatioNewsFuelle';

-- Deleting the exclusive tables of the module

DROP TABLE IF EXISTS XimNewsBulletins;
DROP TABLE IF EXISTS XimNewsNews;
DROP TABLE IF EXISTS XimNewsAreas;
DROP TABLE IF EXISTS XimNewsList;
DROP TABLE IF EXISTS XimNewsColector;
DROP TABLE IF EXISTS XimNewsFrameBulletin;
DROP TABLE IF EXISTS XimNewsFrameVersion;
DROP TABLE IF EXISTS RelNewsColector;
DROP TABLE IF EXISTS RelNewsBulletins;
DROP TABLE IF EXISTS RelColectorList;
DROP TABLE IF EXISTS XimNewsCache;
DROP TABLE IF EXISTS RelNewsArea;

