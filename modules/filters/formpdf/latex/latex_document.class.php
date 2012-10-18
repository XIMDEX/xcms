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



if (!defined("XIMDEX_ROOT_PATH"))
        define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../../../../"));

include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
ModulesManager::component('/formpdf/Latex.class.php', 'filters');
ModulesManager::file('/inc/utils.inc');


class Latex_document extends Latex {

	// latex_form object
	var $form;

	function latex_document() {
		require_once("latex_form.class.php");
		$this->form =& new latex_form;
	}

	function open_document($style, $packages) {
		$start   = "\\documentclass[" . $style["text"] . ",".$style["language"]. ",".$style["orientation"]. ",".$style["dim"] . "]{". $style["docType"] ."}\n";
		$include = "";

		/*
		This block inlcudes necessary packages in LaTeX
		en LaTeX. These packages are defined in $packages array
		which is defined in parser_document.class.php in open_document method
		*/
		foreach ($packages as $package=>$options) {
			$opts = "";

			if (is_array($options)) {
				foreach($options as $option) {
					if ($opts != "") {
						$opts .= "," . $option;
					} else {
						$opts  = $option;
					}
				}
			} else {
				$opts = $options;
			}

			if ($opts != "") {
				$opts = "[" . $opts . "]";
			}

			$include .= "\\usepackage" . $opts . "{" . $package ."}\n";
		}

		/*
		After packages it includes necessary data for page layout.
		*/
		$layout_pag  = "\\pdfpagewidth " . $style["pagewidth"] . "\n";
		$layout_pag .= "\\pdfpageheight " . $style["pageheight"] . "\n";
		$layout_pag .= "\\topmargin " . $style["topmargin"] . "\n";
		$layout_pag .= "\\headheight " . $style["headheight"] ."\n";
		$layout_pag .= "\\headsep " . $style["headsep"] . "\n";
		$layout_pag .= "\\textheight " . $style["textheight"] . "\n";
		$layout_pag .= "\\textwidth " . $style["textwidth"] . "\n";
		$layout_pag .= "\\oddsidemargin " . $style["oddsidemargin"] . "\n";
		$layout_pag .= "\\evensidemargin " . $style["evensidemargin"] . "\n";

		/*
		Start document with its begin enviroment.
		It includes command.tex file with some default macros.
		*/
		$begin = "";
// 		$begin = "\\TPshowboxestrue\n";
//		$begin .= "\\textblockorigin{0mm}{0mm}\n";

		$begin .= "\\begin{document}\n";
		$begin .= "\\input{command.tex}\n";
		$begin .= "\\input{styles.tex}\n";

		XMD_Log::write ($start . $include . $layout_pag . $begin,  1);

		return $start . $include . $layout_pag . $begin;
	}

	function close_document() {
		$end = "\\end{document}\n";
		return $end;
	}

//
}

?>
