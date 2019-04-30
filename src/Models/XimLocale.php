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

use Ximdex\Models\ORM\LocalesOrm;

class XimLocale extends LocalesOrm
{
	public $dbObj;
	
	/**
	 * Error code
	 * 
	 * @var int
	 */
	public $numErr;
	
	/**
	 * Error message
	 * 
	 * @var string
	 */
	public $msgErr;
	
	/**
	 * Class error list
	 * 
	 * @var array
	 */
	public $errorList;

	function __construct(int $params = null)
	{
		$this->errorList[1] = _('Locale does not exist');
		$this->errorList[2] = _('A locale with this name already exists');
		$this->errorList[3] = _('Arguments missing');
		$this->errorList[3] = _('Database connection error');
		parent::__construct($params);
	}

	/**
	 * Devuelve el ID (atributo de la clase)
	 * 
	 * @return boolean|string
	 */
	public function getID()
	{
		return $this->get('ID');
	}

	/**
	 * Permite cambiar el ID sin tener que destruir y volver a crear un objeto
	 * 
	 * @param int $id
	 * @return boolean|string
	 */
	public function setID(int $id)
	{
		parent::__construct($id);
		return $this->get('ID');
	}

	/**
	 * Devuelve una lista con todos los idLocales existentes
	 * 
	 * @param array $order
	 * @return NULL|array
	 */
	public function getList(array $order = NULL)
	{
		$validDirs = array('ASC', 'DESC');
		$this->ClearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = "SELECT ID FROM Locales";
		if (! empty($order) && is_array($order) && isset($order['FIELD'])) {
			$sql .= sprintf(" ORDER BY %s %s", $order['FIELD'],
				isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
		}
		$dbObj->query($sql);
		if (! $dbObj->numErr) {
		    $salida = [];
			while (! $dbObj->EOF) {
				$salida[] = $dbObj->getValue("ID");
				$dbObj->next();
			}
			return ! empty($salida) ? $salida : NULL;
		} else {
			$this->setError(4);
		}
	}

	public function getCode()
	{
		return $this->get('Code');
	}

	/**
	 * Devuelve el nombre del locale correspondiente
	 * 
	 * @return boolean|string
	 */
	public function getName()
	{
		return $this->get('Name');
	}

	public function getAllLocales(array $order = NULL)
	{
		return $this->getList($order);
	}

	public function getEnabledLocales()
	{
		$_locales = $this->find('ID', 'Enabled = 1', null);
		if (! empty($_locales)) {
			$locales = array();
			foreach ($_locales as $locale) {
				$class = new static($locale['ID']);
				list($lang, $country) = explode("_", $class->getCode());
				$locales[] = array
				(
				    "ID" => $locale['ID'],
					'Code' => $class->getCode(),
					'Lang' => $lang,
					'Country' => $country,
					"Name" => $class->getName()
				);
			}
			return $locales;
		}
        return null;
	}

	public function getLocaleByCode(string $_code = null)
	{
		if (empty($_code)) {
			$_code = DEFAULT_LOCALE;
		}
		$_locales = $this->find('ID', "Code = '{$_code}'", null);
		if (! empty($_locales)) {
			$locales = array();
			foreach ($_locales as $locale) {
				$class = new static($locale['ID']);
				list($lang, $country) = explode("_", $class->getCode());
				$locales[] = array
				(
				    "ID" => $locale['ID'],
					'Code' => $class->getCode(),
					'Lang' => $lang,
					'Country' => $country,
					"Name" => $class->getName()
				);
			}
			return $locales[0];
		}
		return null;
	}

	/**
	 * Nos permite cambiar el nombre a una locale
	 * 
	 * @param string $name
	 * @return boolean|array
	 */
	public function setName(string $name)
	{
		if (! $this->get('ID')) {
			$this->setError(2, 'No Existe locale');
			return false;
		}
		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	public function setCode(string $code)
	{
		if (! $this->get('ID')) {
			$this->setError(2, 'No Existe locale');
			return false;
		}
		$result = $this->set('Code', $code);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Nos busca locale por su nombre
	 * 
	 * @param string $name
	 */
	public function SetByName(string $name)
	{
		$this->ClearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$query = sprintf("SELECT ID FROM Locales WHERE Name = %s", $dbObj->sqlEscapeString($name));
		$dbObj->query($query);
		if ($dbObj->numRows) {
			parent::__construct($dbObj->getValue("ID"));
		} else {
			$this->setError(4);
		}
	}

	/**
	 * Create a new language and update its ID in the object
	 * 
	 * @param string $code
	 * @param string $name
	 * @param int $enabled
	 * @param int $ID
	 * @return int
	 */
	public function createLocale(string $code, string $name, int $enabled = 0, int $ID = null)
	{
		if ($ID > 0) {
			$this->set('ID', $ID);
		}
		$this->set('Code', $code);
		$this->set('Name', $name);
		$this->set('Enabled', (int) ! empty($enabled));
		$this->add();
		if ($this->get('ID') <= 0) {
			$this->setError(4);
		}
		return $ID;
	}

	/**
	 * Delete the current language
	 */
	public function deleteLanguage()
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		if (! is_null($this->get('ID'))) {
		    
			// Lo borramos de la base de datos
			$dbObj->execute(sprintf("DELETE FROM Locales WHERE ID = %d", $this->get('ID')));
			if ($dbObj->numErr) {
				$this->setError(4);
			}
		} else {
			$this->setError(1);
		}
	}

	public function LocaleEnabled(int $ID)
	{
		$dbObj = new \Ximdex\Runtime\Db();
		$query = sprintf("SELECT Enabled FROM Locales WHERE ID = %d", $ID);
		$dbObj->query($query);
		if ($dbObj->numErr != 0) {
			$this->setError(1);
			return null;
		}
		return $dbObj->getValue("Enabled");
	}

	public function setEnabled(int $enabled)
	{
		if (! $this->get('ID')) {
			$this->setError(2, 'No Existe');
			return false;
		}
		$result = $this->set('Enabled', (int) $enabled);
		if ($result) {
			return $this->update();
		}
		return false;
	}
	
	/**
	 * Devuelve true si en la clase se ha producido un error
	 *
	 * @return boolean
	 */
	public function hasError()
	{
	    return ($this->numErr != null);
	}

	/**
	 * Limpia los errores de la clase
	 */
	private function clearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}

	/**
	 * Carga un error en la clase
	 * @param int $code
	 */
	private function setError(int $code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}
}
