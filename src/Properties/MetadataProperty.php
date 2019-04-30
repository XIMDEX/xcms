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

use Ximdex\Models\MetadataScheme;

class MetadataProperty extends InheritableProperty
{
    private $metadata;
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\Properties\InheritableProperty::getPropertyName()
     */
	public function getPropertyName()
	{	    
	    return strtolower(self::METADATA_SCHEME);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::get_system_properties()
	 */
    protected function get_system_properties()
    {
        if (! $this->metadata) {
            $this->metadata = new MetadataScheme();
        }
        return $this->metadata->find('idMetadataScheme as Id, name as Name');
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\Properties\InheritableProperty::get_inherit_properties()
     */
    protected function get_inherit_properties(array $availableProperties)
    {
        if (! $this->metadata) {
            $this->metadata = new MetadataScheme();
        }
        return $this->metadata->find('idMetadataScheme as Id, name as Name', 'idMetadataScheme in (%s)'
            , array(implode(', ', $availableProperties)), MULTI, false);
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
