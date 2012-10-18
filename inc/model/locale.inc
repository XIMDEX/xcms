<?php

/*********************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    			    	 *
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	    	 *
 *                                                                            					    	 *
 *  This program is free software: you can redistribute it and/or modify        	 *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      	 *
 *  (at your option) any later version.                                       			 *
 *                                                                            						 *
 *  This program is distributed in the hope that it will be useful,           		 *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the * 
 *  GNU Affero General Public License for more details.                       		 *
 *                                                                            						 *
 * See the Affero GNU General Public License for more details.                	 *
 * You should have received a copy of the Affero GNU General Public License  *
 * version 3 along with Ximdex (see LICENSE).                                 		 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       		 *
 *                                                                            						 *
 * @version $Revision: $                                                      				 * 	 
 *                                                                            						 *
 *                                                                            						 *
 *********************************************************************************/



if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Locales_ORM.class.php';

class XimLocale extends Locales_ORM
{
	var $dbObj;
	var $numErr;				// Error code
    var $msgErr;				// Error message
	var $errorList= array(	// Class error list
		1 => 'Locale does not exist',
		2 => 'A locale with this name already exists',
		3 => 'Arguments missing',
		4 => 'Error de conexion con la base de datos',
	);


	//Constructor
	function XimLocale($params = null) {
		$this->errorList[1]=_('Locale does not exist');
		$this->errorList[2]=_('A locale with this name already exists');
		$this->errorList[3]=_('Arguments missing');
		$this->errorList[3]=_('Database connection error');

		parent::__construct($params);
	}
	// Devuelve el ID (atributo de la clase)
	function GetID() {
		return $this->get('ID');
	}

	// Permite cambiar el ID sin tener que destruir y volver a crear un objeto
	function SetID($id) {
		parent::GenericData($id);
		return $this->get('ID');
	}

	// Devuelve una lista con todos los idLocales existentes
	function GetList($order = NULL)
	{
		$validDirs = array('ASC', 'DESC');
		$this->ClearError();
		$dbObj = new DB();
    	$sql = "SELECT ID FROM Locales";
    	if (!empty($order) && is_array($order) && isset($order['FIELD'])) {
    		$sql .= sprintf(" ORDER BY %s %s", $order['FIELD'],
    			isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
    	}
		$dbObj->Query($sql);
		if(!$dbObj->numErr) {
			while(!$dbObj->EOF) {
				$salida[] = $dbObj->GetValue("ID");
				$dbObj->Next();
			}
    		return !empty($salida) ? $salida : NULL;
		}
		else
			$this->SetError(4);
	}


	function GetCode()
	{
		return $this->get('Code');
	}


	// Devuelve el nombre del locale correspondiente
	function GetName()
	{
		return $this->get('Name');
	}


	function GetAllLocales($order = NULL) {
		return $this->GetList($order);
	}

	function GetEnabledLocales() {

			$_locales = $this->find('ID', 'Enabled = 1', null);
			if(!empty($_locales) ) {
				$locales = array();
				foreach ($_locales as $locale) {
					$class = new XimLocale($locale['ID']);
					list($lang, $country) = explode("_",  $class->GetCode() );
					$locales[] = array( "ID" =>  $locale['ID'],
											'Code' => $class->GetCode(),
											'Lang' => $lang,
											'Country' => $country,
											"Name" => $class->GetName()
					);
				}

				return $locales;
			}else {
				return null;
			}
	}


	function GetLocaleByCode($_code = NULL) {
			if(empty($_code) ) {
				$_code = DEFAULT_LOCALE;
			}

			$_locales = $this->find('ID', "Code = '{$_code}'", null);

			if(!empty($_locales) ) {
				$locales = array();
				foreach ($_locales as $locale) {
					$class = new XimLocale($locale['ID']);
					list($lang, $country) = explode("_",  $class->GetCode() );
					$locales[] = array( "ID" =>  $locale['ID'],
											'Code' => $class->GetCode(),
											'Lang' => $lang,
											'Country' => $country,
											"Name" => $class->GetName()
					);

				}


				return $locales[0];
			}else {
				return null;
			}
	}

	// Nos permite cambiar el nombre a una locale
	function SetName($name)
	{
		if (!($this->get('ID') > 0)) {
			$this->SetError(2, 'No Existe locale');
			return false;
		}
		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}


	function SetCode($code)
	{
		if (!($this->get('ID') > 0)) {
			$this->SetError(2, 'No Existe locale');
			return false;
		}
		$result = $this->set('Code', $code);
		if ($result) {
			return $this->update();
		}
		return false;
	}


    // Nos busca locale por su nombre
	function SetByName($name)
	{
		$this->ClearError();
		$dbObj = new DB();
		$query = sprintf("SELECT ID FROM Locales WHERE Name = %s", $dbObj->sqlEscapeString($name));
		$dbObj->Query($query);
		if ($dbObj->numRows)
			parent::GenericData($dbObj->GetValue("ID"));
		else
			$this->SetError(4);
	}


    // Crear un nuevo idioma y cargar su ID en el objeto
	function CreateLocale($code, $name, $enabled=0, $ID=null)
	{

		if ($ID > 0) {
			$this->set('ID', $ID);
		}


		$this->set('Code', $code);
		$this->set('Name', $name);
		$this->set('Enabled', (int)!empty($enabled));
		$this->add();

		if ($this->get('ID') <= 0) {
			$this->SetError(4) ;
		}

		return $ID;
	}

    // Elimina el idioma actual
	function DeleteLanguage()
		{
		$this->ClearError();
		$dbObj = new DB();
		if(!is_null($this->get('ID')))
			{
				// Lo borramos de la base de datos
				$dbObj->Execute(sprintf("DELETE FROM Locales WHERE ID= %d", $this->get('ID')));
				if($dbObj->numErr)
					$this->SetError(4);
			}
		else
			$this->SetError(1);
		}

	function LocaleEnabled($ID) {

		$dbObj = new DB();
		$query = sprintf("SELECT Enabled FROM Locales WHERE ID = %d", $ID);
		$dbObj->Query($query);
		if ($dbObj->numErr != 0) {
	 	$this->SetError(1);
	 	return null;
		}
		return 	$dbObj->row["Enabled"];


	}


	function SetEnabled($enabled) {

		if (!($this->get('ID') > 0)) {
			$this->SetError(2, 'No Existe ');
			return false;
		}

		$result = $this->set('Enabled', (int)$enabled);
		if ($result) {
			return $this->update();
		}
		return false;
	}


	/// Limpia los errores de la clase
	function ClearError()
		{
		$this->numErr = null;
		$this->msgErr = null;
		}

	/// Carga un error en la clase
	function SetError($code)
		{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
		}

    // devuelve true si en la clase se ha producido un error
    function HasError()
    	{
        return ($this->numErr != null);
        }
}
?>
