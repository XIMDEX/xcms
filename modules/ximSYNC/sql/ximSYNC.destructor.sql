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

DROP TABLE IF EXISTS `Batchs`;
DROP TABLE IF EXISTS `ChannelFrames`;
DROP TABLE IF EXISTS `NodeFrames`;
DROP TABLE IF EXISTS `Pumpers`;
DROP TABLE IF EXISTS `ServerErrorByPumper`;
DROP TABLE IF EXISTS `ServerFrames`;
DROP TABLE IF EXISTS `SynchronizerStats`;
DROP TABLE IF EXISTS `PublishingReport`; 
DROP TABLE IF EXISTS `NodesToPublish`;

-- Updating publicaion actions and we make ximdex to manage them

UPDATE Actions SET Module = NULL WHERE IdAction IN (6127, 6129, 6131, 6133, 6204, 7016, 6773, 8010, 8011, 7228);

-- Deletin action "Publish a server massively"
DELETE FROM Actions where IdAction = 7228;

-- Drop field ActiveForPumping on Servers table
ALTER TABLE `Servers` DROP `ActiveForPumping`;
