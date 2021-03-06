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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace Ximdex\Properties;

use Ximdex\Models\Channel;

class ChannelProperty extends InheritableProperty
{
    private $channel;
    
	public function getPropertyName()
	{	    
	    return strtolower(self::CHANNEL);
	}
	
	/**
	 * Obtain the system properties for languages
	 * 
	 * {@inheritDoc}
	 * @see InheritableProperty::get_system_properties()
	 */
    protected function get_system_properties()
    {
        if (! $this->channel) {
            $this->channel = new Channel;
        }
        return $this->channel->find('IdChannel as Id, Name');
    }
    
    /**
     * Get the inherited channels
     * 
     * {@inheritDoc}
     * @see InheritableProperty::get_inherit_properties()
     */
    protected function get_inherit_properties(array $availableProperties)
    {
        if (! $this->channel) {
            $this->channel = new Channel;
        }
        return $this->channel->find('IdChannel as Id, Name, Description', 'IdChannel in (%s)', array(implode(', ', $availableProperties))
            , MULTI, false);
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\Properties\InheritableProperty::updateAffectedNodes()
     */
    protected function updateAffectedNodes(array $values)
    {
        return true;
    }
}
