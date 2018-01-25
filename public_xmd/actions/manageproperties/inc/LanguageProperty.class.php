<?php
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

use Ximdex\Models\Language;

\Ximdex\Modules\Manager::file('/actions/manageproperties/inc/InheritableProperty.class.php');

class LanguageProperty extends InheritableProperty {

    private $language;
    
	protected function getPropertyName() {
	    
		return self::LANGUAGE;
	}
	
	/**
	 * Obtain system properties for channels
	 * {@inheritDoc}
	 * @see InheritableProperty::get_system_properties()
	 */
    protected function get_system_properties()
    {
        if (!$this->language)
            $this->language = New Language();
        return $language->find('Id as IdLanguage, Name', 'Enabled = 1', NULL);
    }
    
    /**
     * Get the inherited languages
     * {@inheritDoc}
     * @see InheritableProperty::get_inherit_properties()
     */
    protected function get_inherit_properties(array $availableProperties)
    {
        if (!$this->language)
            $this->language = New Language();
        return $language->find('Id as IdLanguage, Name', 'Enabled = 1 and IdLanguage in (%s)', array(implode(', ', $availableProperties)), MULTI, false);
    }
}