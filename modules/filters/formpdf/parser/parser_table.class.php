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



class Table extends ParserRoot {
	var $style;
	var $columns = array();
	var $handler;
	var $renderer;
	var $element;
	var $open_table;
	var $body_table = array();
	var $close_table;
	var $str_cab = "";
	var $str_cab_aux = "";
	
	var $columna;
	var $fila;
	
	// This arrays contains info about colspan and rowspan which
	// are producing during the table scan.
	var $array_rowspan = array();
	var $array_colspan = array();
	
	function table(& $element) {
		$this->element = $element;
		
		// It point to coordinate (0, 0) of the table
		// (first row, first column)
		$this->columna = 0;
		$this->fila = 0;
	}
	
	// Opens a latex table
	function open_table($style) {
		$buffer = $this->renderer->open_table($style);
		fwrite($this->handler, $buffer);
	}
	
	// Closes a latex table
	function close_table($style) {
		$buffer = $this -> renderer -> close_table($style);
		fwrite($this->handler, $buffer);
	}
	
	// Add a row
	function add_row($style) {
		if (!array_key_exists("height",$style)) {
			$style["height"] = "1";
		}
		$buffer = $this -> renderer -> add_row($style);
		fwrite($this->handler,$buffer);
	}
	
	// Add a column
	// If there is a rowspan or  colspan, It could see in the array $style
	// $this->row y $this->column are visible from here.
	function add_col($element, $f, $col, $style) {
		$this->renderer->array_colspan = $this->array_colspan;
		$this->renderer->array_rowspan = $this->array_rowspan;
		$this->renderer->total_filas = $this->num_filas;
		$this->renderer->total_columnas = $this->total_columnas;
		$buffer = $this->renderer->add_col($element, $f, $col, $style);
		fwrite($this->handler, $buffer);
	}
	
	// Build a complete table with the form elements
	function build() {
		$formulario = array("input","select","attach","button");
		$classname = $this->element->getAttribute("class");
		$array_style = $this->style->get_style_element($this->element);
		
		$ncol = explode("|",$array_style["columns"]);
		
		$ncol = count($ncol);
		
		$this->total_columnas = $ncol;
		
		$filas = & $this->element->childNodes;
		$numFilas = & $this->element->childCount;
		
		$this->num_filas = $numFilas;
		
		// It opens the table
		$this->open_table($array_style);
		
		$reopen = false;
		$open = false;
		$mayusculas = array("B","C","D","E","F","G","H");
		
		// for each row it obtains the set of columns that make up it (cells, in fact)
		// and it iterates on them.
		// Index are to save info about rowspan y colspan
		// in their corresponding arrays
		$indice_rowspan = 0;
		$indice_colspan = 0;
		for ($f = 0; $f < $numFilas; $f++) {
			
			$this->fila = $f;
			$this->fila_real = $f;
			
			$columnas = & $filas[$f]->childNodes;
			$numColumnas = & $filas[$f]->childCount;
			$style_row = $this->style->get_style_element($filas[$f]);
			
			// Here it calculates the multicolumn size to the written cases
			// less columns than the maximum expected (ncol).
			$multicolum = $ncol - $numColumnas + 1;
			
			// It iterates in each column of current row.
			for ($col = 0; $col < $numColumnas; $col++) {
				
				$this->columna = $col;
				
				// inner_columna contains DOM of what it is "inside" of current column.
				$inner_columna = & $columnas[$col]->childNodes ;
				$inner_columna = $inner_columna[0];
				
				if (in_array($inner_columna->nodeName, $formulario)) {
					// If nodeName of the node into the column
					// is a form element, it creates a form_element
					// which it is passed as element $inner_columna
					// and as enderer, $this->renderer
					if (!class_exists('form_element')) {
						ModulesManager::file('/formpdf/parser/parser_formelement.class.php', 'filters');
					}
					$form_element = new form_element($inner_columna, $this->renderer);
					$form_element->env = $style_row;
					$form_element->style = & $this->style;
					$element = $form_element->build();

				} else {

					// If it is not a form element
					// it creates a dynamic class of specific type
					// according to $inner_columna->nodeName
					$nombre_clase = $inner_columna->nodeName;
					if (!class_exists($nombre_clase)) {
						ModulesManager::file("/formpdf/parser/parser_".$nombre_clase.".class.php", 'filters');
					}

					$reg_element = new $nombre_clase ($inner_columna);
					$reg_element->renderer = $this->renderer;
					$reg_element->style = & $this -> style;
					$reg_element->handler = & $this -> handler;
					$reg_element->env = "tabla";
					$element = $reg_element->build(true);
				}
				
				$array_styleC = $this -> style -> get_style_element($columnas[$col]);
				if (!array_key_exists("background-color",$array_styleC)) {

					if (array_key_exists("background-color",$style_row)) {
						$array_styleC["background-color"] = $style_row["background-color"];
					} else {
						if (array_key_exists("background-color",$array_style)) {
							$array_styleC["background-color"] = $array_style["background-color"];
						} else {
							$array_styleC["background-color"] = "1,1,1";
						}
					}
				}

				if (($col + 1) >= $numColumnas && $multicolum > 1) {
					$array_styleC["multicolum"] = $multicolum;
				}

				// when ther is a colspan the value of multicolum it is modified
				// reducing it number os points as the colspan indicates.
				// as minimum, multicolumn will be 1 (just one column of width).
				if (array_key_exists("colspan", $array_styleC)) {
					$array_styleC["multicolum"] -= $array_styleC["colspan"] + 1;
					if ($array_styleC["milticolum"] <= 0) {
						$array_styleC["multicolum"] = 1;
					}
				}
				
				// Data of found rowspan
				if (array_key_exists('rowspan', $array_styleC)) {
					$this->array_rowspan[$indice_rowspan]['fila'] = $this->fila;
					$this->array_rowspan[$indice_rowspan]['columna'] = $this->columna;
					$this->array_rowspan[$indice_rowspan]['fila_real'] = $this->fila_real;
					$this->array_rowspan[$indice_rowspan]['columna_real'] = $this->columna_real;
					$this->array_rowspan[$indice_rowspan]['rowspan'] = $array_styleC['rowspan'];
					
					$indice_rowspan++;
				}
				
				// Data of found colspan
				if (array_key_exists('colspan', $array_styleC)) {
					$this->array_colspan[$indice_colspan]['fila'] = $this->fila;
					$this->array_colspan[$indice_colspan]['columna'] = $this->columna;
					$this->array_colspan[$indice_colspan]['colspan'] = $array_styleC['colspan'];
					
					$indice_colspan++;
				}
				
				// TODO: Differenciate between text-row and form columns
				// And it is even better match the text rows to the form-columns
				$this->add_col($element, $f, $col, $array_styleC);
				
				//echo "fila: ".$this->fila.", columna: ".$this->columna."<br />";
				
			} // End of column
			
			// Obtaining of row styles
			$array_styleF = $this -> style -> get_style_element($filas[$f]);
			$next = $f +1;
			if ($next < $numFilas) {
				$array_styleF2 = $this -> style -> get_style_element($filas[$next]);
				if (!array_key_exists ("border-width",$array_styleF) && array_key_exists ("border-width",$array_styleF2)) {
					$array_styleF["border-width"] = $array_styleF2["border-width"];
				}
				if (!array_key_exists ("border-color",$array_styleF) && array_key_exists ("border-color",$array_styleF2)) {
					$array_styleF["border-color"] = $array_styleF2["border-color"];
				}
			}
			
			// It adds a row. In fact, add_row adds a \\hline
			$this->add_row($array_styleF);
			
		} // End of row
		
		// It closes the table
		$this->close_table($array_style);
	}
	
	// is this function not used?
	function build_items($items) {
		$this->open_table();
		$numFilas = count($tabla_items);
		$numColumnas = count($tabla_items[0]);
		for ($f = 0; $f < $numFilas; $f++) {
			for ($col = 0; $col < $numColumnas; $col++) {
				$element = $tabla_items[$f][$col];
				$this->add_col($element,$col);
			}
			$this->add_row();
		}
		$this->close_table();
	}
}

?>
