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
 *  @version $Revision: 7825 $
 */



class Attach extends ParserRoot {

	var $env;


	function build() {
		// element style
		$array_style = $this->style->get_style_element($this->element);
		$this->renderer->setStyle($array_style);

		// element position 
		$this->renderer->setPos($array_style['x'], $array_style['y']);

		// attach attributes
		$iconID = $this->element->getAttribute('icon');
		$this->renderer->setIcon($iconID);

		$fileID = $this->element->getAttribute('file');
		if (is_numeric($fileID)) {
			$node->SetID($fileID);
			$path_file = $node->class->GetNodePath();
		} else {
			$path_file = $fileID;
		}
		$this->renderer->setFile($path_file);

		// parsing of an attach

		// parsing of elements of children of an attach
		$elementos = $this->element->childNodes;
		for ($i = 0; $i <= $this->element->childCount; $i++) {
			if (isset($elementos[$i]) && is_object($elementos[$i])) {
				switch ($elementos[$i]->nodeName) {
					case 'author':
						// autor parsing
						$author = $elementos[$i];
						$texto_author = $author->getText();
						$data_author = $this->special_chars($texto_author);
						$this->renderer->setAuthor($data_author);
						break;

					case 'description':
						// description parsing
						$description = $elementos[$i];
						$texto_description = $description->getText();

						$data_description = $this->special_chars($texto_description);
						$this->renderer->setDescription($data_description);

						break;

					case 'subject':
						// subject parsing
						$subject = $elementos[$i];
						$texto_subject = $subject->getText();

						$data_subject = $this->special_chars($texto_subject);
						$this->renderer->setSubject($data_subject);
						break;

					default:
						break;
				}
			}
		}

		if (is_readable($path_file)) {
			$buffer = $this->renderer->create_attach();
			fwrite($this->handler, $buffer);
		}
	}


//
}

?>
