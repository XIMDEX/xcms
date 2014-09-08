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



/**
 * XIMDEX_ROOT_PATH
 */

if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

define ('TMP_FOLDER', XIMDEX_ROOT_PATH . '/data/tmp/');        
        
require_once (XIMDEX_ROOT_PATH . '/inc/patterns/xObject.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/helper/Messages.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/cli/Shell.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/mail/EmailContainer.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/mail/iterators/I_EmailContainer.class.php');

/**
 * Class to connect and query an email server
 *
 */

//ssl/notls/service=imap/novalidate-cert

class MailReceiver extends xObject {
	private $ssl = true;
	private $tls = false;
	private $serviceType = 'IMAP';
	private $validateCert = false;
	
	private $server = 'mail.ximdex.com';
	private $port = 993;
	
	private $user = '';
	private $password = '';
	
	private $mailConnection = false;
	private $messages;

	public function __construct() {
		$this->messages = new Messages();
	}
	
	/**
	 * @return emailConnection
	 */
	private function getMailConnection() {
		return $this->mailConnection;
	}
	
	/**
	 * @param emailConnection $mailConnection
	 */
	private function setMailConnection($mailConnection) {
		$this->mailConnection = $mailConnection;
	}
	/**
	 * @param integer $port
	 */
	public function setPort($port) {
		$this->port = $port;
	}
	
	/**
	 * @param string $server
	 */
	public function setServer($server) {
		$this->server = $server;
	}
	
	/**
	 * @param string $serviceType
	 */
	public function setServiceType($serviceType) {
		$validServiceTypes = array('IMAP', 'POP3', 'NNTP');
		if (in_array($serviceType, $validServiceTypes)) {
			$this->serviceType = $serviceType;
			return true;
		}
		return false;
	}
	
	/**
	 * @param boolean $tls
	 */
	public function setTls($tls) {
		$this->tls = (bool)$tls;
	}

	/**
	 * @param boolean $validateCert
	 */
	public function setValidateCert($validateCert) {
		$this->validateCert = $validateCert;
	}

	/**
	 * @param boolean $ssl
	 */
	public function setSsl($ssl) {
		$this->ssl = (bool) $ssl;
	}
	
	public function connect() {
		$connection = imap_open($this->_getConnectionString(), 
			$this->getUser(), $this->getPassword());
		if ($connection === false) {
			$errorList = imap_errors();
			foreach ($errorList as $error) {
				$this->messages->add($error, MSG_TYPE_ERROR);
			}
			return false;
		}
		$this->setMailConnection($connection);
		return true;
	}
	
	private function _getConnectionString() {
		$paramsString = "";
		if ($this->getSsl()) {
			$paramsString .= "/ssl";
		}
		
		$paramsString .= ($this->getTls()) ? '/tls' : '/notls';
		
		$serviceStrings = array(
			'IMAP' => '/service=imap',
			'POP3' => '/service=pop3',
			'NNTP' => '/service=nntp'
		);
		
		if (array_key_exists($this->getServiceType(), $serviceStrings)) {
			$paramsString .= sprintf('/service=%s', $serviceStrings[$this->getServiceType()]);
		}
		
		$paramsString .= ($this->validateCert) ? '/validate-cert' : '/novalidate-cert';
		
		return sprintf("{%s:%d%s}INBOX", 
			$this->getServer(), $this->getPort(), $paramsString);
	}
	
	public function searchMail($clause) {
		
		if (!$this->getMailConnection()) {
			$this->messages->add(_('No hay conexión establecida con el servidor'), MSG_TYPE_ERROR);
			return false;
		}
		
		$emailList = imap_search($this->getMailConnection(), $clause);
		if (empty($emailList)) {
			$this->messages->add(_('No se han encontrado mensajes que cumplan con el criterio seleccionado'), 
				MSG_TYPE_ERROR);
			return false;
		}
		
		$emails = array();
		foreach ($emailList as $idEmail) {
			$emails[] = $this->getMail($idEmail);
		}
		$emailIterator = new I_EmailContainer(NULL, NULL);
		$emailIterator->_objects = $emails;
		return $emailIterator;
		
	}
	
	public function getMail($idEmail) {
		$chead = imap_headerinfo($this->getMailConnection(), $idEmail);
		$subject = $chead->subject;
		/**
		 * @var $tsDate timestamp
		 */
		$tsDate = $chead->udate;
		$structure = imap_fetchstructure($this->getMailConnection(), $idEmail);
		
		$body = '';
		switch ($structure->type) {
			case 0:  // plain text
				$body = imap_body($this->getMailConnection(), $idEmail); 
				break;
			case 1:  // multipart message
				$body = $this->_getBody($structure->parts, $idEmail);
				// scan parts looking for plain text parts
				break;
		}

		
				
		$email = new EmailContainer();
		$email->setBody($body);
		$email->setSubject($subject);
		$email->setTsDate($tsDate);
		$email->setFiles($this->_getAttachments($idEmail));
		return $email;

	}
	
	private function _getBody($structureParts, $idEmail, $section = '') {
		foreach ($structureParts as $idPart => $partInfo) {
			$partNumber = $idPart + 1;
			$part = empty($section) ? $partNumber : sprintf('%s.%d', $section, $partNumber);
			if ($partInfo->type === 0) {
				$body = imap_fetchbody($this->getMailConnection(), $idEmail, $part);
			}
			if (!empty($partInfo->parts)) {
				$body .= $this->_getBody($partInfo->parts, $idEmail, $part);
			}
		}
		return $body;
	}
	
	/**
	 * @return integer
	 */
	private function getPort() {
		return $this->port;
	}
	
	/**
	 * @return string
	 */
	private function getServer() {
		return $this->server;
	}
	
	/**
	 * @return string
	 */
	private function getServiceType() {
		return $this->serviceType;
	}
	
	/**
	 * @return boolean
	 */
	private function getSsl() {
		return $this->ssl;
	}
	
	/**
	 * @return boolean
	 */
	private function getTls() {
		return $this->tls;
	}
	
	/**
	 * @return boolean
	 */
	private function getValidateCert() {
		return $this->validateCert;
	}
	/**
	 * @return string
	 */
	private function getPassword() {
		return $this->password;
	}
	
	/**
	 * @return string
	 */
	private function getUser() {
		return $this->user;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
	
	/**
	 * @param string $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}
	
	private function _getAttachments($idEmail) {
		$files = array();
		
		$filename = FsUtils::getUniqueFile(TMP_FOLDER);
		
		$filename = TMP_FOLDER . $filename;
		imap_savebody($this->getMailConnection(), $filename, $idEmail);
		
  		$command = "munpack -C " . TMP_FOLDER . " -fq $filename";
		$shell = new Shell($this->messages);
  		$return = $shell->exec($command);
  		
  		foreach ($return as $fileInfo) {
  			if (empty($fileInfo)) {
  				continue;
  			}
  			
  			$fileParts = explode(" ", $fileInfo);
  			$filePath = TMP_FOLDER . $fileParts[0];
  			if (is_file($filePath)) {
  				$files[] = $filePath;
  			}
  			
  			FsUtils::delete($filename);
  		}
  		
  		return $files;
	}
}
?>