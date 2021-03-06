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

use Ximdex\Models\Iterators\IteratorSearchFilters;
use Ximdex\Models\ORM\SearchFiltersOrm;

class SearchFilters extends SearchFiltersOrm
{
    /**
     * Returns the filter id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Returns the filter name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Returns the handler
     * 
     * @return string
     */
    public function getHandler()
    {
        return $this->Handler;
    }

    public function getFilter($format = 'JSON')
    {
        unset($format);
        return $this->Filter;
    }

    /**
     * Static method that creates a new SearchFilter and returns the related object
     * Filter must by an XML string
     * 
     * @param string $name
     * @param string $handler
     * @param string $filter
     * @return \Ximdex\Models\SearchFilters
     */
    static public function create(string $name, string $handler, string $filter)
    {
        // TODO: Create a unique key in SearchFilters table
        
        // Key length for Filter field must be specified....
        $checksum = md5(sprintf('%s:%s', $handler, serialize($filter)));
        $db = new \Ximdex\Runtime\Db();
        $sql = sprintf("select Name from SearchFilters where md5(concat(Handler, ':', Filter)) = '%s'", $checksum);
        $db->query($sql);
        $ns = new SearchFilters();
        if (! $db->EOF) {
            $ns->messages->add(sprintf('The filter exists with name %s', $db->getValue('Name')), MSG_TYPE_ERROR);
            return $ns;
        }
        $ns->set('Name', $name);
        $ns->set('Handler', $handler);
        $ns->set('Filter', json_encode($filter, JSON_UNESCAPED_UNICODE));
        $ns->add();
        return $ns;
    }

    /**
     * Deletes the current filter
     * 
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::delete()
     */
    public function delete()
    {
        $db = new \Ximdex\Runtime\Db();
        $ret = parent::delete();
        $sql = sprintf('alter table %s auto_increment = 0', $this->_table);
        $db->execute($sql);
        return $ret;
    }

    /**
     * Returns an iterator of all node filters
     * 
     * @return \Ximdex\Models\Iterators\IteratorSearchFilters
     */
    static public function getFilters()
    {
        return new IteratorSearchFilters('', array());
    }

    /**
     * Executes the filter and returns an array of nodes
     */
    public function getNodes()
    {
        // TODO: Use QueryProcessor here or parametrize an instance...
    }
}
