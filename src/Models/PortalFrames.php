<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Models\ORM\PortalFramesOrm;
use Ximdex\Runtime\Session;

class PortalFrames extends PortalFramesOrm
{
    const TYPE_UP = 'Up';
    const TYPE_DOWN = 'Down';
    const STATUS_CREATED = 'Created';
    const STATUS_ACTIVE = 'Active';
    const STATUS_ENDED = 'Ended';
    
    public function upPortalFrameVersion(int $portalId, string $type = self::TYPE_UP) : int
    {
        $portalFrameVersion = $this->getLastVersion($portalId);
        $portalFrameVersion++;
        $this->set('IdPortal', $portalId);
        $this->set('Version', $portalFrameVersion);
        $this->set('CreationTime', time());
        $this->set('PublishingType', $type);
        $this->set('CreatedBy', Session::get('userID'));
        $this->set('Status', self::STATUS_CREATED);
        $this->set('StatusTime', time());
        $idPortalFrameVersion = parent::add();
        return ($idPortalFrameVersion > 0) ? (int) $idPortalFrameVersion : 0;
    }

    public function getLastVersion(int $portalId) : int
    {
        $result = parent::find('MAX(Version)', 'IdPortal = %s', array('IdPortal' => $portalId), MONO);
        return (int) $result[0];
    }

    public function getId(int $portalId, int $version) : int
    {
        $result = parent::find('id', 'IdPortal = %s AND Version = %s',
            array('IdPortal' => $portalId, 'Version' => $version), MONO);
        return (int) $result[0];
    }

    public function getAllVersions(int $portalId) : array
    {
        $result = parent::find('id, Version', 'IdPortal = %s', array('IdPortal' => $portalId), MULTI);
        $portalFrameVersions = [];
        foreach ($result as $resultData) {
            $portalFrameVersions[] = array('id' => $resultData['id'], 'version' => $resultData['Version']);
        }
        return $portalFrameVersions;
    }
}
