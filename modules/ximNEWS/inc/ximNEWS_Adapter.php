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



ModulesManager::file('/inc/io/BaseIO.class.php');
ModulesManager::file('/inc/model/XimNewsAreas.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsNews.inc', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsList.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsArea.php', 'ximNEWS');
ModulesManager::file('/inc/parsers/ParsingRng.class.php');

/**
*	@brief Adapter for make common ximNEWS operations by different media.
*/

class  ximNEWS_Adapter {

	/**
	 * 
	 * @return unknown_type
	 */
	function __construct() {
		$this->messages = new Messages();
	}

	/**
	 * 
	 * @param int idNode
	 * @param int idTemplate
	 * @param string name
	 * @param array languages
	 * @param array channels
	 * @param array data
	 * @param int master
	 * @param array alias
	 * @param array colectors
	 * @param array areas
	 * @param array images
	 * @param array links
	 * @param array files
	 * @param array videos
	 * @return unknown_type
	 */
	function createNews($idNode, $idTemplate, $name, $languages, $channels, $data, $master = NULL, $alias = NULL,
		$colectors = NULL, $areas = NULL, $images = NULL, $links = NULL, $files = NULL, $videos = NULL, $checkDates = true) {

		$newsFolder = new Node($idNode);
		$idSection = $newsFolder->GetSection();

		// newscontainer creation

		$results = self::createNewsContainer($idNode, $idSection, $idTemplate, $name);
		$idNewsContainer = $results['errno'];

		if (!($idNewsContainer > 0)) {
			XMD_Log::error("In newscontainer creation");
			$this->messages->add(sprintf(_('Error creando el contenedor %s'), $name), MSG_TYPE_ERROR);
			foreach ($results['errors'] as $error) {
				$this->messages->add(_($error['message']), $error['type']);
			}
			return false;
		}

		$targetLink = NULL;
		$languages = (array) $languages;

		// date

		if (array_key_exists('noticia_fecha', $data)) {

			$newsData['noticia_fecha'] = $data['noticia_fecha']; 
		}

		// insert images

		if (!is_null($images) && !is_null($images[0]) && array_key_exists('name', $images[0])) {

			$insertedImages = self::insertImages($images, $idSection, $name);
			$newsData['cuerpo_imagenes'] = $insertedImages;

			foreach ($insertedImages as $image) {
				if (array_key_exists('is_property', $image)) $newsData['a_enlaceid_noticia_imagen_asociada'] = $image['url'];
			}
		} else {
			$newsData['a_enlaceid_noticia_imagen_asociada'] = $data['a_enlaceid_noticia_imagen_asociada']; 
		}

		// insert files

		if (!is_null($files) && sizeof($files) > 0) {

			$newsData['a_enlaceid_noticia_archivo_asociado'] = self::insertFiles($files, $idSection, $name);
		}

		// insert videos

		if (!is_null($videos) && sizeof($videos) > 0) {

			$newsData['a_enlaceid_noticia_video_asociado'] = self::insertFiles($videos, $idSection, $name);
		}

		// insert links

		if (sizeof($links) > 0) {

			$newsData['cuerpo_informacion_relacionada'] = self::insertLinks($links, $idSection, $name);
			$newsData['a_enlaceid_noticia_enlace_asociado'] = 
				$newsData['cuerpo_informacion_relacionada'][0]['a_enlaceid_url'];

		} else {
			$newsData['a_enlaceid_noticia_enlace_asociado'] = $data['a_enlaceid_noticia_enlace_asociado']; 
		}

		// ximnewslanguages creation

		if (!is_null($master)) {

			// sort languages starting by master

			usort($languages, create_function('$v, $w', 'return ($v == ' . $master . ') ? -1 : +1;'));
		}

		foreach ($languages as $langId) {

			// gets news content

			foreach ($data as $var => $value) {
				if (!is_array($var) && preg_match("/(\w+?)_$langId$/i", $var, $matches))
					$newsData[$matches[1]] = $value;
			}

			$idNewsLanguage = self::createNewsLanguage($idNewsContainer, $idSection, $idTemplate, $name,
				$langId, $alias[$langId], $channels, $newsData, $targetLink);

			if (!($idNewsLanguage > 0)) {

				XMD_Log::error("In newslanguage $name creation");
				$this->messages->add(sprintf(_('Error creando la noticia %s'), $name), MSG_TYPE_ERROR);
			} else {

				// set workflow master for next news

				if ($langId == $master) $targetLink = $idNewsLanguage;

				XMD_Log::info("Newslanguage $name creation O.K.");
				$this->messages->add(sprintf(_('La noticia %s se ha creado correctamente'), $name), MSG_TYPE_NOTICE);


				// areas association

				if (!is_null($areas)) {

					self::addAreasToNew($idNewsLanguage, $areas);
				}

				// colectors association

				if (!is_null($colectors)) {

					self::addNewToColectors($idNewsLanguage, $colectors, $data["fechainicio"], $data["fechafin"]);
				}

			}

		}

		return true;
	}

	/**
	 * 
	 * @param int idParent
	 * @param int idSection
	 * @param int idTemplate
	 * @param string name
	 * @return unknown_type
	 */
	function createNewsContainer($idParent, $idSection, $idTemplate, $name) {

		$data = array (
			"NODETYPENAME" => "XIMNEWSNEW",
			"NAME" => $name,
			"PARENTID" => $idParent,
			'IDSECTION' => $idSection,

			"CHILDRENS" => array (
				array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
				)
		);

		$baseIO = new BaseIO();
		$return = $baseIO->build($data);
		
		$errors = array();
		if ($return <= 0) {
			$errors = $baseIO->messages->messages;
			//debug::log($errors);
		}
				
		return array('errno' => $return, 'errors' => $errors);
	}

	/**
	 * 
	 * @param int idNewscontainer
	 * @param int idSection
	 * @param int idTemplate
	 * @param string name
	 * @param int idLang
	 * @param string aliasLang
	 * @param array channelLst
	 * @param array newsData
	 * @param int targetLink
	 * @return unknown_type
	 */
	function createNewsLanguage($idNewscontainer, $idSection, $idTemplate, $name, $idLang, $aliasLang, $channelLst,
		$newsData, $targetLink = NULL) {

		$data = array (
			"NODETYPENAME" => "XIMNEWSNEWLANGUAGE",
			"NAME" => $name,
			"PARENTID" => $idNewscontainer,
			'IDSECTION' => $idSection,
			"STATE" => "Edición",
			'ALIASNAME' => $aliasLang,
			'NEWTARGETLINK' => $targetLink,
			'DATANEWS' => $newsData,

			"CHILDRENS" => array (
				array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
				array ("NODETYPENAME" => "LANGUAGE", "ID" => $idLang)
				)
		);

		if (!empty($channelLst)) {
			foreach ($channelLst as $idChannel) {
				$data["CHILDRENS"][] = array ("NODETYPENAME" => "CHANNEL", "ID" => $idChannel);
			}
		}

		$baseIO = new BaseIO();
		return $baseIO->build($data);
	}

	/**
	 * 
	 * @param int idNode
	 * @param int idParent
	 * @param string name
	 * @param array data
	 * @param array colectors
	 * @param array areas
	 * @param array images
	 * @param array links
	 * @param array files
	 * @param array videos
	 * @return unknown_type
	 */
	function updateNews($idNode, $idParent, $name, $langId, $data, $colectors = NULL, $areas = NULL, $images = NULL,
		$links = NULL, $files = NULL, $videos = NULL) {

		$node = new Node($idNode);
		$idSection = $node->GetSection();

		// gets news content

		$data['name'] = $name;

		foreach ($data as $var => $value) {
			if (!is_array($var) && preg_match("/(\w+?)_$langId$/i", $var, $matches))
				$data[$matches[1]] = $value;
		}

		// colectors

		if (!is_null($colectors)) {

			$this->addNewToColectors($idNode, $colectors, $data["fechainicio"], $data["fechafin"]);

			$oldColectors = RelNewsColector::getColectorsFromNew($idNode);

			if (!is_null($oldColectors)) {

				$this->removeNewFromColectors($idNode, array_diff($oldColectors, $colectors));
			}

		} else {

			$colectors = array();
		}

		// areas

		if (!is_null($areas)) {
			$this->addAreasToNew($idNode, $areas);
		} else {
			$areas = array();
		}

		$rel = new RelNewsArea();
		$oldAreas = $rel->GetAreasByNew(array($idNode));

		if (!is_null($oldAreas)) {
			$this->removeNewFromColectors($idNode, array_diff($oldAreas, $areas));
		}

		// insert images

		if (!is_null($images) && array_key_exists('name', $images[0])) {

			$insertedImages = self::insertImages($images, $idSection, $name);
			$data['cuerpo_imagenes'] = $insertedImages;

			foreach ($insertedImages as $image) {
				if (array_key_exists('is_property', $image)) $data['a_enlaceid_noticia_imagen_asociada'] = $image['url'];
			}
		} 

		// insert links

		if (sizeof($links) > 0) {

			$data['cuerpo_informacion_relacionada'] = self::insertLinks($links, $idSection, $name);
			$data['a_enlaceid_noticia_enlace_asociado'] = 
				$data['cuerpo_informacion_relacionada'][0]['a_enlaceid_url'];

		} 

		// insert files

		if (!is_null($files) && sizeof($files) > 0) {

			$data['a_enlaceid_noticia_archivo_asociado'] = $this->insertFiles($files, $idSection, $name);
		}

		// insert videos

		if (!is_null($videos) && sizeof($videos) > 0) {

			$data['a_enlaceid_noticia_video_asociado'] = $this->insertFiles($videos, $idSection, $name);
		}

		// baseio

		$data = array (
			"ID" => $idNode,
			"NODETYPENAME" => "XIMNEWSNEWLANGUAGE",
			"NAME" => $name,
			"PARENTID" => $idParent,
			'IDSECTION' => $idSection,
			"STATE" => "Edición",
			'NEWSDATA' => $data,
			"CHILDRENS" => array()
			);

		$baseIO = new BaseIO();
		$baseIO->update($data);
	}

	/**
	 * 
	 * @param array images
	 * @param int idSection
	 * @param string name
	 * @return unknown_type
	 */
	private function insertImages($images, $idSection, $name) {

		// create ximnewsimages folder and insert images

		$section = new Node($idSection);
		$imagesFolderId = $section->GetChildByName('images');

		$imagesFolderNode = new Node($imagesFolderId);
		$idLote = $imagesFolderNode->GetChildByName($name);

		if (!($idLote > 0)) {
			$idLote = $this->createLote($name, $imagesFolderId, 'normal', date('d/m/Y'));
		}

		$loteNode = new Node($idLote);

		foreach ($images as $image) {

			$idImage = $loteNode->GetChildByName($image['name']);
			$image['url'] = $idImage > 0 ? $idImage : $this->createImage($image['name'], $idLote, $image['tmp_name']);
			$insertedImages[] = $image;
		}

		//Make the first image as principal image of the news
                if (count($insertedImages))
                        $insertedImages[0][is_property]=true;


		return $insertedImages;
	}

	/**
	 * 
	 * @param array files
	 * @param int idSection
	 * @param string name
	 * @return unknown_type
	 */	
	 private function insertFiles($files, $idSection, $name) {

		//if only one file is, convert it into array
		if(array_key_exists("name", $files ) )
			$files = array("0" => $files);
		
		// create common folder for insert files and videos
		$idCommonFolder = $this->getCommonFolder($idSection, $name);
		if (!($idCommonFolder > 0)) {
			$idCommonFolder = $this->createCommonFolder($idSection, $name);
		}

		$commonFolderNode = new Node($idCommonFolder);

		// insert files
		$tmpPath = Config::GetValue('AppRoot') . Config::GetValue('TempRoot');
		$tmpFile = $tmpPath ."/".FsUtils::getUniqueFile($tmpPath);

		foreach ($files as $file) {

			$idFile = $commonFolderNode->GetChildByName($file['name']);

			if (!($idFile > 0)) {
				move_uploaded_file($file['tmp_name'], $tmpFile);

				$binaryFileData = array("NODETYPENAME" => "BINARYFILE",
						"NAME" => $file['name'],
						"PARENTID" => $idCommonFolder,
						'CHILDRENS' => array(
							array('NODETYPENAME' =>	'PATH', 'SRC' => $tmpFile)
							)
						);

				$baseIO = new baseIO();
				$idFile = $baseIO->build($binaryFileData);

			}
		}

		return $idFile;
	}

	/**
	 * 
	 * @param array links
	 * @param int idSection
	 * @param string name
	 * @return unknown_type
	 */
	function insertLinks($links, $idSection, $name) {
	
		foreach ($links as $link) {

			if (isset($link['idlink']) && $link['idlink'] > 0) {

				$result[] = array('a_enlaceid_url' => $link['idlink'], 'enlace_relacionado' => $link['name']);
				continue;
			}

			if (!isset($idLinkFolder)) {
				$idLinkFolder = $this->getXimLinkFolder($idSection, $name);
				$linkFolder = new Node($idLinkFolder);
			}

			$url_valida = strpos(" ".$link["url"], "http://") || strpos(" ".$link["url"], "https://") ||
				strpos(" ".$link["url"], "ftp://");

			if ($link["url"] != NULL && !$linkFolder->GetChildByName($link['name']) && $url_valida !== false) {
				$linkData = array(
					'NODETYPENAME' => 'LINK',
					'NAME' => $link['name'],
					'PARENTID' => $idLinkFolder,
					'CHILDRENS' => array (
						array ('URL' => isset($link['url']) ? $link['url'] : ''),
						array ('DESCRIPTION' => isset($link['description']) ? $link['description'] : '')
					)
				);

				$baseIO = new baseIO();
				$res = $baseIO->build($linkData);
				$result[] = array('a_enlaceid_url' => $res, 'enlace_relacionado' => $link['name']);
			}
		}

		return $result;
	}

	/**
	 * 
	 * @param int idTemplate
	 * @return unknown_type
	 */
	function getContentElements($idTemplate) {

		if (empty($idTemplate)) {
			XMD_Log::error("Void templateID");
			return NULL;
		}

		$rngParser = new ParsingRng();
		$elements =  $rngParser->getElementsForRender($idTemplate);

		foreach ($elements as $element) {
			
			if (!array_key_exists('label', $element)) {
				continue;
			}

			if (array_key_exists('name', $element) && sizeof(explode('fecha', $element['name'])) > 1) { 
				$element['type'] = 'fecha';
			}

			$formElements[] = $element;
		}

		// adding upload elements

		$formElements[] = array('name' => 'a_enlaceid_noticia_archivo_asociado', 'type' => 'file_upload', 'label' => 'Archivo');
		$formElements[] = array('name' => 'a_enlaceid_noticia_video_asociado', 'type' => 'file_upload', 'label' => 'Video');

		// adding other elements

		$formElements[] = array('name' => 'a_enlaceid_noticia_imagen_asociada', 'type' => 'attribute', 'label' => 'Imagen');

		return $formElements;
	}

	/**
	 * 
	 * @param array newsData 
	 * @param int idTemplate
	 * @return unknown_type
	 */
	function setNewsXmlContent($newsData, $idTemplate) {

		if (is_null($idTemplate)) {
			XMD_Log::error('Template is mandatory');
			return NULL;
		}

		$rngParser = new ParsingRng();
		$content = $rngParser->buildDefaultContent($idTemplate);

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;
		$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8));

		$xpath = new DOMXPath($domDoc);

		// Adds to newsData array the name and nodeid

		foreach ($newsData as $nameElement => $valueElement) {
			$nodeList = $xpath->query('//*[@name = "'.$nameElement.'"]');
			if ($nodeList->length > 0) {
				foreach ($nodeList as $entry) {
					// Sets elements value
					$type = $entry->attributes->getNamedItem('type')->nodeValue;
					if (in_array($type, array('checkbox', 'multiple'))) {
						// valueElement is an array of elements
						foreach ($valueElement as $childData) {
							// Rejects text nodes
							$i = 0;

							$newNode = $entry->childNodes->item($i)->cloneNode(true);
							while ($newNode instanceof DOMText) {
								$i++;
								$firstNode = $entry->childNodes->item($i);
								$newNode = $entry->childNodes->item($i)->cloneNode(true);
							}
							foreach ($childData as $dataName => $dataValue) {

								if ($newNode->nodeName == $dataName) {
									$newNode->nodeValue = $dataValue;
								} else {
									$aList = $newNode->getElementsByTagName($dataName);
									if ($aList->length > 0) {
										$aList->item(0)->nodeValue = $dataValue;
									} else {
										if ($newNode->hasAttribute($dataName)) {
											$newNode->setAttribute($dataName, $dataValue);
										}
									}
								}
							}

							// Inserts the new node
							$entry->insertBefore($newNode);
						}

						// remove the void node
			
						$firstNode = $entry->childNodes->item($i);
						$entry->removeChild($firstNode);
					} else {
						$entry->nodeValue = XmlBase::recodeSrc($valueElement, XML::UTF8);
					}
				}
			} else {
				// Sets attributes value
				$attList = $xpath->query('//@*[local-name(.) = "'.$nameElement.'"]');
				if ($attList->length > 0) {
					$attList->item(0)->nodeValue = XmlBase::recodeSrc($valueElement, XML::UTF8);
				}
			}
		}

		// Saving new XML content

		$result = $domDoc->saveXML();
		// Omit XML declaration and decoding
		$result = str_replace('<?xml version="1.0"?>', '', $result);

		return $result;
	}

	/**
	 * 
	 * @param int idSection
	 * @param string name
	 * @return unknown_type
	 */
	function getCommonFolder($idSection, $name) {
		$section = new Node($idSection);
		$commonFolder = $section->GetChildByName('common');

		$node = new Node($commonFolder);
		return $node->GetChildByName($name);
	}

	/**
	 * 
	 * @param int idSection
	 * @param string name
	 * @return unknown_type
	 */
	function createCommonFolder($idSection, $name) {

		$section = new Node($idSection);
		$idCommonFolder = $section->GetChildByName('common');

		$data = array("NODETYPENAME" => "COMMONFOLDER",
					"NAME" => $name,
					"PARENTID" => $idCommonFolder);

		$baseIO = new baseIO();
		return $baseIO->build($data);
	}

	/**
	 * 
	 * @param int idSection
	 * @param string name
	 * @return unknown_type
	 */
	function getXimLinkFolder($idSection, $name) {

		// create ximlink folder (if not exists) for insert links

		$node = new Node($idSection);
		$idProject = $node->getProject();

		$project = new Node($idProject);
		$idXimLink = $project->GetChildByName('ximlink');

		$ximLinkNode = new Node($idXimLink);
		$idLinkFolder = $ximLinkNode->GetChildByName($name);

		if (!($idLinkFolder > 0)) {

			$linkFolderData = array("NODETYPENAME" => "LINKFOLDER",
						"NAME" => $name,
						"PARENTID" => $idXimLink);

			$baseIO = new baseIO();
			return $baseIO->build($linkFolderData);
		}

		return $idLinkFolder;
	}

	/**
	 * 
	 * @param int idNew
	 * @param array colectors
	 * @param string fecha_ini
	 * @param string fecha_fin
	 * @return unknown_type
	 */
	function addNewToColectors($idNew, $colectors, $fecha_ini = NULL, $fecha_fin = NULL) {

		$node = new Node($idNew);
		$dataFactory = new datafactory($idNew);
		$version = $dataFactory->getLastVersion();
		$subversion = $dataFactory->getLastSubVersion($version);

		foreach($colectors as $idColector) {
	
			if (!$node->class->addToColector($idColector, $fecha_ini, $fecha_fin, "$version-$subversion"))
				XMD_Log::error('Error al insertar en RelNewsColector');
		}
	}

	/**
	 * 
	 * @param int idNew
	 * @param array colectors
	 * @return unknown_type
	 */
	function removeNewFromColectors($idNew, $colectors) {

		if (sizeof($colectors) > 0) {

			foreach($colectors as $idColector) {

				$relNewsColector = new RelNewsColector();
				$idRel = $relNewsColector->hasNews($idColector, $idNew);

				if ($idRel > 0) {
					$relNewsColector = new RelNewsColector($idRel);
					$relNewsColector->set('FechaOut', date('d-m-Y H:i:s'));
					$relNewsColector->update();
					$idUser = XSession::get('userID'); 
					$rel = new RelNewsColectorUsers(); 
					$rel->add($idRel, $idUser); 
				}
			}
		}
	}

	/**
	 * 
	 * @param int idNews
	 * @param array areas
	 * @return unknown_type
	 */

	function addAreasToNew($idNews, $areas) {

		$node = new Node($idNews);

		foreach ($areas as $idArea) {
			if ($node->get('IdNode') > 0) {
				if (!$node->class->addToArea($idArea)) {
					XMD_Log::error("In relation news $idNews with area $idArea");
				}
			}
		}
	}

	/**
	 * 
	 * @param string name
	 * @param int idParent
	 * @param string type
	 * @param string stringDate
	 * @return unknown_type
	 */
	function createLote($name, $idParent, $type, $stringDate) {

		if ((int) preg_match("/([0-9]{1,2})[-\/]([0-9]{1,2})[-\/]([0-9]{2,4})/", $stringDate, $date) == 0) {
			XMD_Log::error("Invalid date format $stringDate");
			return NULL;
		}

		switch ($type) {

			case "fechaCarpetas":

			setlocale(LC_TIME, "es_ES");
			$month = strftime("%B", mktime(0, 0, 0, $date[2], $date[1], $date[3]));

			$monthName = "$month-{$date[3]}";
			$day = $date[1];

			// create month folder

			$node = new Node($idParent);
			$monthFolderId = $node->GetChildByName($monthName);

			if (!$monthFolderId) {

				$data = array("NODETYPENAME" => "XIMNEWSDATESECTION",
						"NAME" => $monthName,
						"PARENTID" => $idParent
						);

				$baseIO = new baseIO();
				$monthFolderId = $baseIO->build($data);
			}

			// create day folder

			$monthNode = new Node($monthFolderId);
			$dayFolderId = $monthNode->GetChildByName($day);

			if (!$dayFolderId) {

				$data = array("NODETYPENAME" => "XIMNEWSDATESECTION",
						"NAME" => $day,
						"PARENTID" => $monthFolderId
						);

				$baseIO = new baseIO();
				$dayFolderId = $baseIO->build($data);
			}

			$idParent = $dayFolderId;

			break;

			case "fechaNombre":
				$name = $date[3] . $date[2] . $date[1] . '_' . $name;

			break;
		}


		$data = array("NODETYPENAME" => "XIMNEWSIMAGESFOLDER",
				"NAME" => $name,
				"PARENTID" => $idParent
				);

		$baseIO = new baseIO();

		return $baseIO->build($data);
	}

	/**
	 * 
	 * @param string name
	 * @param int idParent
	 * @param string file
	 * @return unknown_type
	 */
	function createImage($name, $idParent, $file) {

		$data = array("NODETYPENAME" => "XIMNEWSIMAGEFILE",
					"NAME" => $name,
					"PARENTID" => $idParent,
					'CHILDRENS' => array(array('NODETYPENAME' => 'PATH', 'SRC' => $file))
				);

		$baseIO = new baseIO();
		$result = $baseIO->build($data);
		return $result;
	}

	/**
	 * 
	 * @param int idColector
	 * @param array languages
	 * @param array headerData
	 * @param string set
	 * @param int bulletinNumber
	 * @param int lote
	 * @param int master
	 * @return unknown_type
	 */
	function createBulletins($idColector, $languages, $headerData, $set, $bulletinNumber, $lote = NULL, $master = NULL) {

		$newsFolder = new Node($idColector);
		$idSection = $newsFolder->GetSection();
		$channelLst = $newsFolder->class->getChannels();

		$ximNewsColector = new XimNewsColector($idColector);
		$idTemplate = $ximNewsColector->get('IdTemplate');
		$filter = $ximNewsColector->get('Filter');
		$colectorName = $ximNewsColector->get('Name');
		$idXimlet = $ximNewsColector->get('IdXimlet');

		// set the bulletin name

		if (preg_match("/^fecha/", $filter) > 0){

			preg_match("/([0-9]{4})([0-9]{2})([0-9]{2})/", $set, $regs);
			$name = $colectorName . "_" .$regs[3] . "_" .$regs[2] . "_" . $regs[1] . "_$bulletinNumber";
		} else{
			$name = $colectorName . "_" .date("d_m_Y") . "_$bulletinNumber";
		}

		// bulletincontainer creation

		$idBulletinContainer = self::createBulletinContainer($idColector, $idSection, $idTemplate, $name);

		if (!($idBulletinContainer > 0)) {
			XMD_Log::error("In bulletincontainer creation");
			return false;
		}

		// bulletinlanguages creation

		if (!is_null($master)) {

			// sort languages starting by master

			uksort($languages, create_function('$v, $w', 'return ($v == ' . $master . ') ? -1 : +1;'));
		}

		$ximletNode = new Node($idXimlet);
		$date = mktime();
		$targetLink = NULL;

		foreach ($languages as $langId) {

			$lang = new Language($langId);
			$langIso = $lang->get('IsoName');
			$bulletinName = $name . '-id' . $langIso;
			$alias =  $ximletNode->GetAliasForLang($langId);

			$idBulletinLanguage = self::createBulletinLanguage($idBulletinContainer, $idSection, $idTemplate,
				$bulletinName, $langId, $alias, $headerData, $idColector, $set, $date, $lote, $targetLink, $channelLst);

			if (!($idBulletinLanguage > 0)) {

				XMD_Log::error("In bulletinlanguage $bulletinName creation");
			} else {

				// set workflow master for next news

				if ($langId == $master) $targetLink = $idBulletinLanguage;

				XMD_Log::info("Bulletinlanguage $bulletinName creation O.K.");
			}

		}

		return true;
	}

	/**
	 * 
	 * @param int idParent
	 * @param int idSection
	 * @param int idTemplate
	 * @param string name
	 * @return unknown_type
	 */
	function createBulletinContainer($idParent, $idSection, $idTemplate, $name) {

		$data = array (
			"NODETYPENAME" => "XIMNEWSBULLETIN",
			"NAME" => $name,
			"PARENTID" => $idParent,
			'IDSECTION' => $idSection,

			"CHILDRENS" => array (
				array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
				)
		);

		$baseIO = new BaseIO();
		return $baseIO->build($data);
	}

	/**
	 * 
	 * @param int idNewscontainer
	 * @param int idSection
	 * @param int idTemplate
	 * @param string name
	 * @param int idLang
	 * @param string aliasLang
	 * @param string headerData
	 * @param int idColector
	 * @param string set
	 * @param int date
	 * @param int lote
	 * @param int targetLink
	 * @param array channelLst
	 * @return unknown_type
	 */
	function createBulletinLanguage($idNewscontainer, $idSection, $idTemplate, $name, $idLang, $aliasLang,
		$headerData, $idColector, $set, $date, $lote = NULL, $targetLink = NULL, $channelLst = NULL) {

		if (is_null($channelLst)) {

			$colectorNode = new Node($idColector);
			$channelLst = $colectorNode->class->getChannels();
		}

		$data = array (
			"NODETYPENAME" => "XIMNEWSBULLETINLANGUAGE",
			"NAME" => $name,
			"PARENTID" => $idNewscontainer,
			'IDSECTION' => $idSection,
			"STATE" => "Edición",
			'ALIASNAME' => $aliasLang,
			'NEWTARGETLINK' => $targetLink,
			'COLECTOR' => $idColector,
			'CONTENT' => self::getBulletinHeader($headerData, $idTemplate),
			'LOTE' => $lote,
			'DATE' => $date,
			'SET' => $set,

			"CHILDRENS" => array (
				array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
				array ("NODETYPENAME" => "LANGUAGE", "ID" => $idLang)
				)
		);

		if (!empty($channelLst)) {
			foreach ($channelLst as $idChannel) {
				$data["CHILDRENS"][] = array ("NODETYPENAME" => "CHANNEL", "ID" => $idChannel);
			}
		}

		$baseIO = new BaseIO();
		return $baseIO->build($data);
	}

	/**
	 * Returns the bulletin header enclosed by bulletin root tag
	 * @param array data 
	 * @param int  templateID
	 * @return string / NULL
	 */
	function getBulletinHeader($headerData, $idTemplate) {

		if (is_null($idTemplate)) {
			XMD_Log::error('Pvd not found');
			return NULL;
		}

		$rngParser = new ParsingRng();
		$content = $rngParser->buildDefaultContent($idTemplate);

		$doc = new DomDocument;
		$doc->validateOnParse = true;
		$doc->LoadXML($content);

		$xpath = new DOMXPath($doc);

		$rootTag = $xpath->query('/*[1]')->item(0)->nodeName;

		// Gets header nodes

		$nodeList = $xpath->query('//*[@id = "header"]');

		if (!($nodeList->length > 0)) {
			XMD_Log::info('Header not found');
			return NULL;
		}

		// From domNode to string
		$header = "<$rootTag>" . '<' . $nodeList->item(0)->nodeName . '>' ;

		foreach ($nodeList->item(0)->childNodes as $domNode) {
			$nodeName = $domNode->nodeName;

			if ($nodeName != '#text'){
				$header .= '<' . $nodeName;
			}

			if ($domNode->hasAttributes()) {
				foreach($domNode->attributes as $att) {
					$header .= ' ' . $att->nodeName . '="' . $att->nodeValue . '"';
				}
			}

			if ($nodeName != '#text'){
				$header .= '>';
			}

			if (in_array($nodeName, array_keys((array) $headerData))) {
				$header .= $headerData[$nodeName];
			} else {
				$header .= $domNode->nodeValue;
			}

			if ($nodeName != '#text'){
				$header .= '</' . $nodeName . '>';
			}
		}

		$header .= '</' . $nodeList->item(0)->nodeName . '>' . "</$rootTag>";
		return $header;
	}

	/**
	 * 
	 * @param int idNews
	 * @return unknown_type
	 */
	function getDataFromNews($idNews) {

		$strDoc = new StructuredDocument($idNews);
		$name = $strDoc->get('Name');
		$idTemplate = $strDoc->get('IdTemplate');
		$idLang = $strDoc->get('IdLanguage');

		$lang = new Language($idLang);
		$language = $lang->get('Name');

		$templateNode = new Node($idTemplate );

		$resultado["template_id"] = $idTemplate;
		$resultado["template_name"] = $templateNode->get('Name');
		$resultado["name"] = $strDoc->get('Name');
		$resultado["idioma_id"] = $idLang;
		$resultado["idioma"] = $language;

		return $resultado;
	}

	/**
	 * Return ximnewsimagesfolder belong to node section
	 * @param int  idNode
	 * @return array / NULL
	 */
	function getLotes($idNode) {
		$node = new Node($idNode);
		$idSection = $node->GetSection();
		
		//find child from this section that idNodeType = 5310 (ximnewsimagesfolder)
		$query = "select n.IdNode, n.Name from FastTraverse as f inner join Nodes as n on n.IdNode = f.IdChild ";
		$query .= "where f.IdNode = $idSection and n.IdNodeType = 5310;";
		$lotes = $node->query($query, MULTI);
		
		return $lotes;
	}

	/**
	 * 
	 * @param int idParent
	 * @param string name
	 * @param int idTemplate
	 * @param array languages
	 * @param array aliasLanguages
	 * @param array channels
	 * @param int global
	 * @param string order
	 * @param int canalCorreo
	 * @param int newstogenerate
	 * @param int timetogenerate
	 * @param int inactive
	 * @param int newsPerBulletin
	 * @param string filter
	 * @param int mailList
	 * @param int idArea
	 * @param int master
	 * @return unknown_type
	 */
	function createColector($idParent, $name, $idTemplate, $languages, $aliasLanguages = NULL, $channels, $global, $order, 
		$canalCorreo, $newstogenerate, $timetogenerate, $inactive, $newsPerBulletin, $filter, $mailList = NULL, 
		$idArea = NULL, $master = NULL) {
/*
		foreach ($aliasList as $idLanguage => $aliasName) {
			if (in_array($idLanguage, $languages)) {
				$aliasLanguages[$idLanguage] = $aliasName;
			}
		}
*/
		$parent = new Node($idParent);
		$idSection = $parent->GetSection();

		// Gets pvd version

		$dataFactory = new DataFactory($idTemplate);
		$ver = $dataFactory->GetLastVersion();
		$subver = $dataFactory->GetLastSubVersion($ver);
		$templateIdVersion = $dataFactory->getVersionId($ver, $subver);

		// Creating colector node

		$nodeType = new NodeType();
		$nodeType->SetByName('XimNewsColector');
		$idNodeType = ($nodeType->get('IdNodeType') > 0) ? $nodeType->get('IdNodeType') : NULL;

		$data = array(
			'NODETYPENAME' => 'XIMNEWSCOLECTOR',
			'NAME' => $name,
			'NODETYPE' => $idNodeType,
			'STATUS' => '', 
			'PARENTID' => $idParent,
			'FILTER' => $filter,
			'IDSECTION' => $idSection,
			'ORDERNEWSINBULLETINS' => $order,
			'NEWSPERBULLETIN' => $newsPerBulletin,
			'TIMETOGENERATE' => $timetogenerate,
			'NEWSTOGENERATE' => $newstogenerate,
			'MAILCHANNEL' => $canalCorreo,
			'PVDVERSION' => $templateIdVersion,
			'INACTIVE' => $inactive,
			'IDAREA' => $idArea,
			'GLOBAL' => $global,
			'CHILDRENS' => array(
				array(
					'NODETYPENAME' => 'VISUALTEMPLATE',
					'ID' => $idTemplate
				)
			)
		);

		$baseIo = new baseIO();
		$idColector = $baseIo->build($data);		

		if (!($idColector > 0)) {
			$this->messages->add(_("El colector NO se ha creado con exito."), MSG_TYPE_NOTICE);
			$this->messages->add(_("Error insertando en XimNewsColector."), MSG_TYPE_NOTICE);
			$this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');

			return false;
		}

		// Creating ximlet container

		$data = array (
				"NODETYPENAME" => "XIMNEWSBULLETINLANGUAGEXIMLETCONTAINER",
				"NAME" => $name,
				"PARENTID" => $idColector,
				"STATE" => "",
				"CHILDRENS" => array (
									array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate)
								)
			);

		foreach ($channels as $channelId) {
			$data["CHILDRENS"][] = array ("NODETYPENAME" => "CHANNEL", "ID" => $channelId);
		}

		$idXimletContainer = $baseIo->build($data);

		if(!($idXimletContainer > 0)){
			$this->messages->mergeMessages($baseIo->messages);
			
			$node = new Node($idColector);

			if ($node->nodeType->get('Name') == 'XimNewsColector') {
				$node->DeleteNode();
			}

			return false;
		}

		// Creating documents ximlet

		$targetLink = NULL;
		$languages = (array) $languages;

		if (!is_null($master)) {

			// sort languages starting by master

			uksort($languages, create_function('$v, $w', 'return ($v == ' . $master . ') ? -1 : +1;'));
		}

		foreach ($languages as $langId) {

			$id = self::createXimletLanguage($name, $idXimletContainer, $langId, $aliasLanguages[$langId], $idTemplate, 
				$channels, $targetLink);

			if (!($id > 0)) {

				$this->messages->add(_("El colector NO se ha creado con exito."), MSG_TYPE_NOTICE);
				$this->messages->add(_("Error creando ximlet de idioma $langId."), MSG_TYPE_NOTICE);
			} else {

				// set workflow master for next news

				if ($langId == $master) $targetLink = $id;
			}
		}

		// Updating colector data

		$ximNewsColector = new XimNewsColector($idColector);
		$ximNewsColector->set('IdXimlet', $idXimletContainer);
		$ximNewsColector->update();

		if(sizeof($mailList) > 0) {
			$ximNewsList = new XimNewsList();
			$ximNewsList->updateList($idColector, $mailList);
		}

		return $idColector;
	}

	/**
	 * 
	 * @param string colectorName
	 * @param int idXimletContainer
	 * @param int idLanguage
	 * @param string aliasName
	 * @param int idTemplate
	 * @param array channels
	 * @param int targetLink
	 * @return unknown_type
	 */
	public function createXimletLanguage($colectorName, $idXimletContainer, $idLanguage, $aliasName, $idTemplate, $channels, 
		$targetLink) {

		$lang = new Language($idLanguage);

		$data = array (
			"NODETYPENAME" => "XIMNEWSBULLETINLANGUAGEXIMLET",
			"NAME" => $colectorName,
			"PARENTID" => $idXimletContainer,
			"STATE" => "Edición",
			"NEWTARGETLINK" => $targetLink,
			"CONTENT" => "",
			"ALIASNAME" => $aliasName,
			"CHILDRENS" => array (
								array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
								array ("NODETYPENAME" => "LANGUAGE", "ID" => $idLanguage)
							)
			);

		foreach ($channels as $channelId) {
			$data["CHILDRENS"][] = array ("NODETYPENAME" => "CHANNEL", "ID" => $channelId);
		}
		
		$baseIo = new BaseIO();
		$id = $baseIo->build($data);
		

		if (!($id > 0)) {
			XMD_Log::error("Creating for colector $colectorName ximlet language $idLanguage");
			return NULL;
		}

		return $id;
	}

	/**
	 * 
	 * @param int idColector 
	 * @param string typeColector
	 * @return unknown_type
	 */
	public function setOtfProperty($idColector,$typeColector){
		$n = new Node($idColector);
		$isOTF = $n->getSimpleBooleanProperty('otf');

		if ($typeColector == 'hibrido'){
			$n->setProperty('hybridColector',"true");
			if (!$isOTF){
				$n->setProperty('otf', "true");
			}
			$isOTF = true;
		}else if ($typeColector == 'otf'){
			if (!$isOTF){
				$n->setProperty('otf', "true");
			}
			$isOTF = true;
		}else{
			$n->setProperty('otf',"false");
			$isOTF = false;
		}
		unset($n);
		return $isOTF;
	}

    /**
     * 
     * @param int idArea
     * @return unknown_type
     */
	public function deleteArea($idArea) {
		
		$objAreas = new XimNewsAreas($idArea);
		$relNewsAreas = new RelNewsArea();

		if ($relNewsAreas->hasAreas($idArea, '')) {
			$res = $objAreas->Deleterelnewsarea($idArea);
		}
		
		if (!$objAreas->DeleteArea())  return false;
		
		return true;
    }
    
    /**
     * 
     * @param string name
     * @param string description
     * @return unknown_type
     */
    public function createArea($name, $description) {

		$area = new XimNewsAreas();
		
		if (!$area->CreateArea(XmlBase::recodeSrc($name, XML::UTF8), XmlBase::recodeSrc($description, XML::UTF8))) 
			return false;
		
		return true;
	}
 
 	/**
 	 * 
 	 * @param string date
 	 * @return unknown_type
 	 */
	public static function checkDateFormat($date) {

	    if (!$date)  return NULL;
		
		if ((int) preg_match("/([0-9]{1,2})[-\/]([0-9]{1,2})[-\/]([0-9]{2,4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", 
			$date, $regs) == 0) {
			XMD_Log::info("Invalid date format $date");
			return NULL;
		}

		return mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[1],$regs[3]);
	}

}

?>
