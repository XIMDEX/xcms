<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\Iterators\IteratorLinkDescriptions;
use Ximdex\Models\ORM\LinksOrm;

class Link extends LinksOrm
{
    const LINK_FAIL = "fail";
    
    const LINK_OK = "ok";
    
    const LINK_WAITING = "waiting";
    
    const LINK_NOT_CHECKED = "not checked";
    
    public $actsAs = ['\Ximdex\Behaviours\Search' => ['field' => 'IdLink']];

    public function checkUrl(string $url, string $name = null)
    {
        $conditions = array('conditions' => array('Url' => $url));
        if (! empty($name)) {
            $conditions['conditions']['name'] = $name;
        }
        $result = $this->search($conditions);
        if (count($result) > 0) {
            return ($result[0]);
        }
        return null;
    }

    public function getName()
    {
        $name = null;
        $node = new Node($this->get('IdLink'));
        if (! $node->get('IdNode')) {
            Logger::warning('The name of the ximlink with ID ' . $this->get('IdLink') . ' could not be obtained');
        } else {
            $name = $node->get('Name');
        }
        return $name;
    }

    public function getDescriptions()
    {
        return new IteratorLinkDescriptions('IdLink = %s', array($this->get('IdLink')));
    }

    public function addDescription(string $description)
    {
        return RelLinkDescriptions::create($this->get('IdLink'), $description);
    }

    public function deleteDescription(string $description)
    {
        $rel = new RelLinkDescriptions();
        $rel = $rel->find(ALL, 'IdLink = %s and Description = %s', array($this->get('IdLink'), $description));
        if (count($rel) > 0 && $rel[0]['IdLink'] > 0) {
            $rel = new RelLinkDescriptions($rel[0]['IdLink']);
            $rel->delete();
        }
        return $rel;
    }
}
