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




class Paragraph extends ParserRoot {
	function build() {
		// Paragraph styles
		$array_style = $this->style->get_style_element($this->element);

		// Link collection of the paragraph
		$links = & $this->element->getElementsByPath("link");

		for ($l=0; $l < $links->getLength(); $l++) {
			$link = $links->item($l);
			$urlId = $link->getAttribute("url");
			// It differentiate a external link and a link given by a nodeID
			if (!is_int($urlId)) {
				$url = $urlId;
			} else {
				$node = new Node($urlId);
				$url = $node->class->GetUrl();
			}

			$link_style = $this->style->get_style_element($link);
			if (!array_key_exists ("color", $link_style)) {
				if(array_key_exists ("color", $array_style)) {
					// If a link has not it own colour it copy it from the paragraph
					$link_style["color"] = $array_style["color"];
				} else {
					// If the paragraph has not it own colour too, it is black
					$link_style["color"] = "0,0,0";
				}
			}

			$buffer = $this->renderer->wrap_link($link->getText(), $url, $link_style);
			$root = & $this->element->ownerDocument;
			$enlace = & $root->createTextNode($buffer);
			$this->element->replaceChild($enlace, $link);
		}

//		$parrafo = $this->element->getText();
		$parrafo = $this->element->getAttribute("value");
		$parrafo = $this->special_chars($parrafo);
		$r = $this->element->getAttribute("rotation");
		$align = $this->element->getAttribute("align");
		$fontSize = $array_style["font-size"];
		$lineHeight = $array_style["line-height"];
		$x = $array_style["x"];
		$y = $array_style["y"];
		$width = $array_style["width"] - 5;
		$height = $array_style["height"] - 50;
		$class_name = $array_style["class"];

		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute("class")));
		$this->renderer->setPos($x, $y);
		$this->renderer->setWidth($width);
		$this->renderer->setRotation($r);
		$this->renderer->setStyle($class_name);
		$this->renderer->setContent($parrafo);
		$this->renderer->setAlign($align);
		$this->renderer->setFontSize($fontSize);
		$this->renderer->setLineHeight($lineHeight);

		$buffer = $this->renderer->add_paragraph();
		fwrite($this->handler, $buffer);
	}

}

?>
