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

DROP TABLE IF EXISTS `XimTAGSTags`;
DROP TABLE IF EXISTS `RelTagsNodes`;
DROP TABLE IF EXISTS `RelTagsDescriptions`;

-- -- Nodetype: 5012 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5012";
DELETE FROM `Actions` WHERE `IdAction` = "6911";
DELETE FROM `Nodes` WHERE `IdNode` = "6911";

-- -- Nodetype: 5013 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5013";
DELETE FROM `Actions` WHERE `IdAction` = "6912";
DELETE FROM `Nodes` WHERE `IdNode` = "6912";

-- -- Nodetype: 5014 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5014";
DELETE FROM `Actions` WHERE `IdAction` = "6913";
DELETE FROM `Nodes` WHERE `IdNode` = "6913";

-- -- Nodetype: 5015 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5015";
DELETE FROM `Actions` WHERE `IdAction` = "6914";
DELETE FROM `Nodes` WHERE `IdNode` = "6914";

-- -- Nodetype: 5016 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5016";
DELETE FROM `Actions` WHERE `IdAction` = "6915";
DELETE FROM `Nodes` WHERE `IdNode` = "6915";

-- -- Nodetype: 5017 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5017";
DELETE FROM `Actions` WHERE `IdAction` = "6916";
DELETE FROM `Nodes` WHERE `IdNode` = "6916";

-- -- Nodetype: 5018 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5018";
DELETE FROM `Actions` WHERE `IdAction` = "6917";
DELETE FROM `Nodes` WHERE `IdNode` = "6917";

-- -- Nodetype: 5020 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5020";
DELETE FROM `Actions` WHERE `IdAction` = "6918";
DELETE FROM `Nodes` WHERE `IdNode` = "6918";

-- -- Nodetype: 5021 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5021";
DELETE FROM `Actions` WHERE `IdAction` = "6919";
DELETE FROM `Nodes` WHERE `IdNode` = "6919";

-- -- Nodetype: 5022 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5022";
DELETE FROM `Actions` WHERE `IdAction` = "6920";
DELETE FROM `Nodes` WHERE `IdNode` = "6920";

-- -- Nodetype: 5023 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5023";
DELETE FROM `Actions` WHERE `IdAction` = "6921";
DELETE FROM `Nodes` WHERE `IdNode` = "6921";

-- -- Nodetype: 5024 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5024";
DELETE FROM `Actions` WHERE `IdAction` = "6922";
DELETE FROM `Nodes` WHERE `IdNode` = "6922";

-- -- Nodetype: 5025 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5025";
DELETE FROM `Actions` WHERE `IdAction` = "6923";
DELETE FROM `Nodes` WHERE `IdNode` = "6923";

-- -- Nodetype: 5026 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5026";
DELETE FROM `Actions` WHERE `IdAction` = "6924";
DELETE FROM `Nodes` WHERE `IdNode` = "6924";

-- -- Nodetype: 5028 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5028";
DELETE FROM `Actions` WHERE `IdAction` = "6925";
DELETE FROM `Nodes` WHERE `IdNode` = "6925";

-- -- Nodetype: 5031 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5031";
DELETE FROM `Actions` WHERE `IdAction` = "6928";
DELETE FROM `Nodes` WHERE `IdNode` = "6928";

-- -- Nodetype: 5032 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5032";
DELETE FROM `Actions` WHERE `IdAction` = "6929";
DELETE FROM `Nodes` WHERE `IdNode` = "6929";

-- -- Nodetype: 5036 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5036";
DELETE FROM `Actions` WHERE `IdAction` = "6933";
DELETE FROM `Nodes` WHERE `IdNode` = "6933";

-- -- Nodetype: 5039 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5039";
DELETE FROM `Actions` WHERE `IdAction` = "6936";
DELETE FROM `Nodes` WHERE `IdNode` = "6936";

-- -- Nodetype: 5040 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5040";
DELETE FROM `Actions` WHERE `IdAction` = "6937";
DELETE FROM `Nodes` WHERE `IdNode` = "6937";

-- -- Nodetype: 5041 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5041";
DELETE FROM `Actions` WHERE `IdAction` = "6938";
DELETE FROM `Nodes` WHERE `IdNode` = "6938";

-- -- Nodetype: 5043 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5043";
DELETE FROM `Actions` WHERE `IdAction` = "6939";
DELETE FROM `Nodes` WHERE `IdNode` = "6939";

-- -- Nodetype: 5044 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5044";
DELETE FROM `Actions` WHERE `IdAction` = "6940";
DELETE FROM `Nodes` WHERE `IdNode` = "6940";

-- -- Nodetype: 5045 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5045";
DELETE FROM `Actions` WHERE `IdAction` = "6941";
DELETE FROM `Nodes` WHERE `IdNode` = "6941";

-- -- Nodetype: 5048 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5048";
DELETE FROM `Actions` WHERE `IdAction` = "6942";
DELETE FROM `Nodes` WHERE `IdNode` = "6942";

-- -- Nodetype: 5049 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5049";
DELETE FROM `Actions` WHERE `IdAction` = "6943";
DELETE FROM `Nodes` WHERE `IdNode` = "6943";

-- -- Nodetype: 5050 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5050";
DELETE FROM `Actions` WHERE `IdAction` = "6944";
DELETE FROM `Nodes` WHERE `IdNode` = "6944";

-- -- Nodetype: 5053 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5053";
DELETE FROM `Actions` WHERE `IdAction` = "6945";
DELETE FROM `Nodes` WHERE `IdNode` = "6945";

-- -- Nodetype: 5054 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5054";
DELETE FROM `Actions` WHERE `IdAction` = "6946";
DELETE FROM `Nodes` WHERE `IdNode` = "6946";

-- -- Nodetype: 5055 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5055";
DELETE FROM `Actions` WHERE `IdAction` = "6947";
DELETE FROM `Nodes` WHERE `IdNode` = "6947";

-- -- Nodetype: 5056 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5056";
DELETE FROM `Actions` WHERE `IdAction` = "6948";
DELETE FROM `Nodes` WHERE `IdNode` = "6948";

-- -- Nodetype: 5057 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5057";
DELETE FROM `Actions` WHERE `IdAction` = "6949";
DELETE FROM `Nodes` WHERE `IdNode` = "6949";

-- -- Nodetype: 5059 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5059";
DELETE FROM `Actions` WHERE `IdAction` = "6951";
DELETE FROM `Nodes` WHERE `IdNode` = "6951";

-- -- Nodetype: 5061 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5061";
DELETE FROM `Actions` WHERE `IdAction` = "6953";
DELETE FROM `Nodes` WHERE `IdNode` = "6953";

-- -- Nodetype: 5063 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5063";
DELETE FROM `Actions` WHERE `IdAction` = "6954";
DELETE FROM `Nodes` WHERE `IdNode` = "6954";

-- -- Nodetype: 5064 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5064";
DELETE FROM `Actions` WHERE `IdAction` = "6955";
DELETE FROM `Nodes` WHERE `IdNode` = "6955";

-- -- Nodetype: 5065 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5065";
DELETE FROM `Actions` WHERE `IdAction` = "6956";
DELETE FROM `Nodes` WHERE `IdNode` = "6956";

-- -- Nodetype: 5066 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5066";
DELETE FROM `Actions` WHERE `IdAction` = "6957";
DELETE FROM `Nodes` WHERE `IdNode` = "6957";

-- -- Nodetype: 5067 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5067";
DELETE FROM `Actions` WHERE `IdAction` = "6958";
DELETE FROM `Nodes` WHERE `IdNode` = "6958";

-- -- Nodetype: 5068 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5068";
DELETE FROM `Actions` WHERE `IdAction` = "6959";
DELETE FROM `Nodes` WHERE `IdNode` = "6959";

-- -- Nodetype: 5076 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5076";
DELETE FROM `Actions` WHERE `IdAction` = "6960";
DELETE FROM `Nodes` WHERE `IdNode` = "6960";

-- -- Nodetype: 5077 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5077";
DELETE FROM `Actions` WHERE `IdAction` = "6961";
DELETE FROM `Nodes` WHERE `IdNode` = "6961";

-- -- Nodetype: 5078 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5078";
DELETE FROM `Actions` WHERE `IdAction` = "6962";
DELETE FROM `Nodes` WHERE `IdNode` = "6962";

-- -- Nodetype: 5081 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5081";
DELETE FROM `Actions` WHERE `IdAction` = "6965";
DELETE FROM `Nodes` WHERE `IdNode` = "6965";

-- -- Nodetype: 5300 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5300";
DELETE FROM `Actions` WHERE `IdAction` = "6967";
DELETE FROM `Nodes` WHERE `IdNode` = "6967";

-- -- Nodetype: 5301 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5301";
DELETE FROM `Actions` WHERE `IdAction` = "6968";
DELETE FROM `Nodes` WHERE `IdNode` = "6968";

-- -- Nodetype: 5302 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5302";
DELETE FROM `Actions` WHERE `IdAction` = "6969";
DELETE FROM `Nodes` WHERE `IdNode` = "6969";

-- -- Nodetype: 5303 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5303";
DELETE FROM `Actions` WHERE `IdAction` = "6970";
DELETE FROM `Nodes` WHERE `IdNode` = "6970";

-- -- Nodetype: 5304 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5304";
DELETE FROM `Actions` WHERE `IdAction` = "6971";
DELETE FROM `Nodes` WHERE `IdNode` = "6971";

-- -- Nodetype: 5305 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5305";
DELETE FROM `Actions` WHERE `IdAction` = "6972";
DELETE FROM `Nodes` WHERE `IdNode` = "6972";

-- -- Nodetype: 5306 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5306";
DELETE FROM `Actions` WHERE `IdAction` = "6973";
DELETE FROM `Nodes` WHERE `IdNode` = "6973";

-- -- Nodetype: 5307 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5307";
DELETE FROM `Actions` WHERE `IdAction` = "6974";
DELETE FROM `Nodes` WHERE `IdNode` = "6974";

-- -- Nodetype: 5308 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5308";
DELETE FROM `Actions` WHERE `IdAction` = "6975";
DELETE FROM `Nodes` WHERE `IdNode` = "6975";

-- -- Nodetype: 5309 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5309";
DELETE FROM `Actions` WHERE `IdAction` = "6976";
DELETE FROM `Nodes` WHERE `IdNode` = "6976";

-- -- Nodetype: 5310 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5310";
DELETE FROM `Actions` WHERE `IdAction` = "6977";
DELETE FROM `Nodes` WHERE `IdNode` = "6977";

-- -- Nodetype: 5311 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5311";
DELETE FROM `Actions` WHERE `IdAction` = "6978";
DELETE FROM `Nodes` WHERE `IdNode` = "6978";

-- -- Nodetype: 5312 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5312";
DELETE FROM `Actions` WHERE `IdAction` = "6979";
DELETE FROM `Nodes` WHERE `IdNode` = "6979";

-- -- Nodetype: 5313 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5313";
DELETE FROM `Actions` WHERE `IdAction` = "6980";
DELETE FROM `Nodes` WHERE `IdNode` = "6980";

-- -- Nodetype: 5320 ----
DELETE FROM `RelRolesActions` WHERE `IdAction` = "5320";
DELETE FROM `Actions` WHERE `IdAction` = "6981";
DELETE FROM `Nodes` WHERE `IdNode` = "6981";

DELETE FROM `Namespaces` WHERE service='Ximdex' AND type='OntologyBrowser';

