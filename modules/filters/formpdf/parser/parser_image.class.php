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



class Image extends ParserRoot {
	function build() {
		$array_style = $this->style->get_style_element($this->element);

		$imgSrc = $this->element->getAttribute('src');

		if (is_numeric($imgSrc)) {
			$node = new Node($imgSrc);
			if ($node->GetID() > 0) {
				$imgSrc = $node->class->GetNodePath();
			}
		} else {
			$imgSrc = urldecode($imgSrc);
		}

		$filedata = pathinfo($imgSrc);

		// If image is a gif it is transformer to a jpg
		if (isset($filedata['extension']) && $filedata['extension'] == "gif") {
			$imgSrc2 = '/tmp/' . $filedata['filename'] . '.jpg';
			$this->gif2jpg($imgSrc, $imgSrc2);
			$imgSrc = $imgSrc2;
		}

		$array_style["inline"] = "\n";
		$this->renderer->setPos($array_style['x'], $array_style['y']);
		$this->renderer->setDim($array_style['width'], $array_style['height']);
		$this->renderer->setImage($imgSrc);
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class')));

		$buffer = $this->renderer->add_image();

		fwrite($this->handler, $buffer);
	}

}

?>
