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



if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../"));

include_once XIMDEX_ROOT_PATH . '/inc/model/node.inc';

/*
	Makes dext:import
*/

function dext_import($pattern, $replacement, $fileImport) {

	$content = FsUtils::file_get_contents($fileImport);
	$pattern = "/$pattern/i";

	$content = preg_replace($pattern, $replacement, $content);

	return $content;
}

/*
	Replace some content on PTD for correct XSL translation
*/

function preTransformation($ptdContent, $fileName, $idSection = NULL) {
	// Modificamos los atributos del estilo att='"test"' por el estilo att="'test'"
	$ptdContent = utf8_encode($ptdContent);
	$nameMatches = array();
	preg_match('/(.*)\..*$/', $fileName, $nameMatches);
	if (!empty($nameMatches) && !empty($nameMatches[1]) && count($nameMatches[1]) == 1) {
		$firstTagMatch = array();
		preg_match('/^(<\?xml[^\?]*\?>\s*)?(<!\s*DOCTYPE\s+\w+\s+\[[^\]]+]>)?\s*<([^\s|\>]+)/', $ptdContent, $firstTagMatch);
		$firstTag = $nameMatches[1];
		if (!empty($firstTagMatch) && !empty($firstTagMatch[3]) && count($firstTagMatch[3]) == 1) {
			$ptdContent = preg_replace('/^(<\?xml[^\?]*\?>\s*)?(<!\s*DOCTYPE\s+\w+\s+\[[^\]]+]>)?\s*<([^\s|\>]+)/', '<' . $firstTag, $ptdContent);
			$ptdContent = preg_replace('/<\/([^>]+)>\s*$/', sprintf('</%s>', $firstTag), $ptdContent);
		}
	}
	
	$matches = array();
	preg_match_all("/((\w+)\s?\=\s?\'([^\']+)\')/", $ptdContent, $matches);
	if (!empty($matches)) {
		if (!empty($matches[0])) {
			foreach ($matches[0] as $key => $value) {
				$replaceString = sprintf('%s="%s"', $matches[2][$key], str_replace('"', "'", $matches[3][$key]));
				$ptdContent = str_replace($matches[0][$key], $replaceString, $ptdContent);
			}
		}
	}


	$ptdContent = setNameSpace($ptdContent, $fileName);
	

	// Comments cdata sections

	$pattern = '/<!\[CDATA\[(.+?)\]\]>/is';
	$replacement = "<!-- [CDATA] \${1} -->";
	$ptdContent = preg_replace($pattern, $replacement, $ptdContent);
	
	$section = new Node($idSection);
	
	// Replacing %%% in tag names by calltemplate
	$pattern = '/(<\s*((INCLUDE|docxap)(([\w|\-|\_|\d]*(%%%[\w|\d|-|_]+%%%)?([\w|\-|\_|\d]+)?)+))\s*([\/]?)\s*>)/';
	preg_match_all($pattern, $ptdContent, $matches);
	if (count($matches) > 0 && isset($matches[0]) && $matches[0] > 0) {
		foreach ($matches[0] as $key => $value) {
			$dinamic = false;
			if ($section->get('IdNode') > 0) {
				// Si el template docxap o include es dinámico, se inserta asociado a la sección
				if (preg_match('/%%%[\w|\-|\_|\d]+%%%/', $value) > 0) {
					$dinamic = true;
					$dinamicTemplateList = $section->getProperty('dinamic_template_list');
					$dinamicTemplateList = $dinamicTemplateList[0];
					if (empty($dinamicTemplateList)) {
						$dinamicTemplateListArray = array();
					} else {
						$dinamicTemplateListArray = explode(', ', $dinamicTemplateList);
					}
					$dinamicTemplateListArray[] = $matches[2][$key];
					$dinamicTemplateListArray = array_unique($dinamicTemplateListArray);
					$dinamicTemplateList = implode(', ', $dinamicTemplateListArray);
					$section->setProperty('dinamic_template_list', $dinamicTemplateList);
				}
			}
			$dinamicTemplate = preg_replace('/%%%([\w|\-|\_|\d]+)%%%/', "_\${1}_", $matches[4][$key]);
			if ($dinamic) {
				$ptdContent = str_replace($value, 
					"<dext:calltemplate dinamic=\"" . $matches[3][$key] 
					. $dinamicTemplate . "\" " . $matches[8][$key] . ">", 
					$ptdContent);
			} else {
				$ptdContent = str_replace($value, 
					"<dext:calltemplate expr=\"" . $matches[3][$key] 
					. $dinamicTemplate . "\" " . $matches[8][$key] . ">", 
					$ptdContent);
			}
		}
	}

//	$ptdContent = preg_replace($pattern, $replacement, $ptdContent);

	$pattern = '/(<(\s*\/\s*(INCLUDE|docxap)([\w|\-|\_|\d]*(%%%[\w|\d|-|_]+%%%)?([\w|\-|\_|\d]+)?)+\s*)>)/';
	$replacement = "</dext:calltemplate>";
	$ptdContent = preg_replace($pattern, $replacement, $ptdContent);

	// Replacing %%% in tag names by calltemplate
	$pattern = '/(<\s*(([\w|\-|\_|\d]*(%%%[\w|\d|-|_]+%%%)+([\w|\-|\_|\d]+)?)+)\s*([\/]?)\s*>)/';
	preg_match_all($pattern, $ptdContent, $matches);
	if (count($matches) > 0 && isset($matches[0]) && $matches[0] > 0) {
		foreach ($matches[0] as $key => $value) {
			$dinamic = false;
			if ($section->get('IdNode') > 0) {
				// Si el template docxap o include es dinámico, se inserta asociado a la sección
				if (preg_match('/%%%[\w|\-|\_|\d]+%%%/', $value) > 0) {
					$dinamic = true;
					$dinamicTemplateList = $section->getProperty('dinamic_template_list');
					$dinamicTemplateList = $dinamicTemplateList[0];
					if (empty($dinamicTemplateList)) {
						$dinamicTemplateListArray = array();
					} else {
						$dinamicTemplateListArray = explode(', ', $dinamicTemplateList);
					}
					$dinamicTemplateListArray[] = $matches[2][$key];
					$dinamicTemplateListArray = array_unique($dinamicTemplateListArray);
					$dinamicTemplateList = implode(', ', $dinamicTemplateListArray);
					$section->setProperty('dinamic_template_list', $dinamicTemplateList);
				}
			}
			$dinamicTemplate = preg_replace('/%%%([\w|\-|\_|\d]+)%%%/', "_\${1}_", $matches[2][$key]);
			if ($dinamic) {
				$ptdContent = str_replace($value, 
					"<dext:calltemplate dinamic=\"" . $dinamicTemplate . "\" " 
					. $matches[6][$key] . ">", $ptdContent);
			} else {
				$ptdContent = str_replace($value, 
					"<dext:calltemplate expr=\"" . $dinamicTemplate . "\" " 
					. $matches[6][$key] . ">", $ptdContent);
			}
			
		}
	}
	

	$pattern = '/(<(\s*\/\s*([\w|\-|\_|\d]*(%%%[\w|\d|-|_]+%%%)+([\w|\-|\_|\d]+)?)+\s*)>)/';
	$replacement = "</dext:calltemplate>";
	$ptdContent = preg_replace($pattern, $replacement, $ptdContent);
	
	//Comienzo de parsing de ifcondition
	preg_match_all("/(dext:ifcondition\s+expr\s*\=\s*\"([^\"]+)\")/s", $ptdContent, $matches);

	$arraySearch = array('eq', 'ne', 'AND', 'OR');
	$arrayReplace = array('=', '!=', 'and', 'or');
	if (!empty($matches)) {
		if (!empty($matches[0])) {
			foreach ($matches[0] as $key => $value) {
				$expression = $matches[2][$key];
				$formedExpression = '';
				preg_match_all("/(([\(]*\'[^\']*\')([^\']*)?)+?/", $expression, $result);

				if (isset($result[2]) && count($result[2]) > 0) {
					$expressions = $result[2];
					$operators = $result[3];
					foreach ($operators as $operatorKey => $operatorValue) {
						$operators[$operatorKey] = str_replace($arraySearch, $arrayReplace, $operatorValue);
					}
					$addition = '';
					$operatorCount = 1;
					
					foreach ($expressions as $expressionKey => $expressionValue) {
						$lastExpression = isset($expressions[$expressionKey-1]) ? $expressions[$expressionKey-1] : NULL;
						$localExpression = $expressionValue;
						$nextExpression = isset($expressions[$expressionKey+1]) ? $expressions[$expressionKey+1] : NULL;

						if (preg_match('/\!\=/', $operators[$expressionKey]) > 0) {
							if ((int) preg_match('/_BODY_/', $expressionValue) == 0) {

								if (preg_match('/:/', $expressionValue) > 0) {

									if (preg_match('/%%%[\w|\_|\:|\-]+%%%/', $expressionValue)) {
										$addition .= " or not($expressionValue)"; 
									}

									if (($nextExpression) 
										&& preg_match('/%%%[\w|\_|\:|\-]+%%%/', $nextExpression) > 0) {
										$addition .= " or not($expressionValue)"; 
									}
								
								} else {

									if (preg_match('/%%%[\w|\_|\-]+%%%/', $expressionValue)) {
										$addition .= sprintf(' or count(ancestor-or-self::*[@%s]) = 0', 
											substr($expressionValue, 4, -4));
									}

									if (($nextExpression) 
										&& preg_match('/%%%[\w|\_|\-]+%%%/', $nextExpression) > 0) {
										$addition .= sprintf(' or count(ancestor-or-self::*[@%s]) = 0', 
											substr($nextExpression, 4, -4));
									}

								}
							}
						
						}
						
						if (!(preg_match('/[\!|\=]/', $operators[$expressionKey]) > 0)) {
							$formedExpression .= '(';
							if ($operatorCount == 2) {//binario
								$formedExpression .= $lastExpression 
									. $operators[$expressionKey - 1] . $localExpression;
							} else { //operador unario
								 $formedExpression .=  $localExpression;
							}
							if (!empty($addition)) {
								$formedExpression .= $addition;
								$addition = ''; 
							}
							$formedExpression .= ')';
							$formedExpression .= $operators[$expressionKey];
							$operatorCount = 1;
						} else {
							$operatorCount = 2;
						}
					}
					$expression = $formedExpression;
				}
				$expression = _resolveExpression($expression);
				$replaceString = sprintf('dext:ifcondition expr="%s"', $expression);
				$ptdContent = str_replace($matches[0][$key], $replaceString, $ptdContent);
			}
		}
	}
	//fin de procesamiento de ifconditions
	//%%%_BODY_%%%
	$ptdContent = preg_replace('/(>[^<]*)%%%_BODY_%%%([^<]*<)/', "\${1}<dext:var_body/>\${2}", $ptdContent);
	$ptdContent = preg_replace('/%%%_BODY_%%%/', "'{.}'", $ptdContent);
	
	//Expresiones xpath 
	preg_match_all('/>([^<|%]*)((%%%(([\w|\-|\_]+\:)+[\w|\-|\_]+)%%%)([^<|%]*))+</s', $ptdContent, $matches);
	if (!empty($matches) && !empty($matches[0])) {
		foreach ($matches[0] as $key => $match) {
			preg_match_all('/(%%%(([\w|\-|\_]+)[\:]?)+%%%)/', $match, $parts);

			$textToSearch = $matches[0][$key];
			$textToReplace = $matches[0][$key];
			if (isset($parts[0]) && count($parts[0]) > 0) {
				foreach ($parts[0] as $gxmlExpression) {				
					preg_match('/([\w|\-|\_]+[\:]?)+/', $gxmlExpression, $preXpathExpression);
					if (isset($preXpathExpression[0]) && count($preXpathExpression) > 0) {
						if ($preXpathExpression[1] == '_BODY_') {
							$xpath = sprintf('<dext:applytemplates expr="%s"/>', _resolveXpath($preXpathExpression[0]));
						} else {
							$xpath = sprintf('<dext:getvalue expr="%s"/>', _resolveXpath($preXpathExpression[0]));
						}
						$textToReplace = str_replace($gxmlExpression, $xpath, $textToReplace);
						$ptdContent = str_replace($textToSearch, $textToReplace, $ptdContent);
					}
				}					
			}
		}
	}
	
	//Expresiones xpath
	
	preg_match_all('/(%%%([\w|\-|\_]+\:)+[\w|\-|\_]+%%%)/', $ptdContent, $matches);
	if (!empty($matches) && !empty($matches[0])) {
		$matches = array_unique($matches[0]);
		foreach ($matches as $match) {
			$ptdContent = str_replace($match, '{' . _resolveXpath($match) . '}', $ptdContent);
		}
	}
	
	preg_match_all('/dext\:calltemplate\s+.*\s*expr\=\"([^\"]+)\"/', $ptdContent, $matches);
	if (isset($matches[1]) && count($matches[1]) > 0) {
		foreach ($matches[1] as $key => $value) {
			$value = preg_replace('/(([^%]*)%%%([^%]+)%%%([^%]*))+?/', "\${2}{@\${3}}\${4}", $value);
			$ptdContent = preg_replace('/dext\:calltemplate(\s+.*\s*)expr\=\"'.$value.'\"/', "dext:calltemplate\${1}expr=\"".$value."\"", $ptdContent);
		}
	}

	//Sustitución de macros por atributos
	$ptdContent = preg_replace('/>([^<|%]*)%%%([\w|\_|\-]+)%%%([^<]*)</s', 
		">\${1}<dext:getvalue expr=\"ancestor-or-self::*[@\${2}][1]/@\${2}\" />\${3}<", $ptdContent);
	$ptdContent = preg_replace('/[\']?[\{]?%%%([\w|\_|\-]+)%%%[\}]?[\']?/', 
		"{ancestor-or-self::*[@\${1}][1]/@\${1}}", $ptdContent);
	//<dext:getvalue expr=\"        \" />
	
	// Adding namespace on html tags
	$htmlTags = "a|abbr|acronym|address|applet|area|b|base|basefont|bdo|big|"
			. "blockquote|body|br|button|caption|center|cite|code|col|colgroup|"
			. "dd|del|dfn|dir|div|dl|dt|em|fieldset|font|form|frame|frameset|"
			. "h1|h2|h3|h4|h5|h6|head|hr|html|i|iframe|img|input|ins|isindex|"
			. "kbd|label|legend|li|link|map|menu|meta|noframes|noscript|object|"
			. "ol|optgroup|option|p|param|pre|q|s|samp|script|select|small|"
			. "span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|"
			. "th|thead|title|tr|tt|u|ul|var";

	$pattern = "/<([\/]*)($htmlTags)([\s|>|\/>])/i";
	$replacement = "<\${1}html:\${2}\${3}";
	$ptdContent = preg_replace($pattern, $replacement, $ptdContent);
	
	// Comments cdata sections

	$pattern = '/<!\[CDATA\[(.+)\]\]>/i';
	$replacement = "<!-- [CDATA] \${1} -->";
	$ptdContent = preg_replace($pattern, $replacement, $ptdContent);

	// Gets default attributes and makes dext variables list (comma separated)
	
	preg_match_all('/ ([\d|\w]+)-default\s*=\s*["|\']/i', $ptdContent, $defaultVars);

	$varsList = implode(',', $defaultVars[1]);
	$ptdContent = preg_replace('/\"{(@[^}]+?)}\"/', "\"\${1}\"", $ptdContent);
	$ptdContent = utf8_encode(_getHeader()) . "\n" . $ptdContent;

	return array($ptdContent, $varsList);
}

function _getHeader() {
	$docTypeTag = Config::getValue('DoctypeTag');
	preg_match_all('/<\!ENTITY\s+(\w+)\s+\"(\w+)\"\s*>/', $docTypeTag, $matches);
	$entities = array();
	if (!empty($matches[1]) && count($matches[1]) > 0) {
		foreach ($matches[1] as $entity) {
			$entities[] = sprintf('<!ENTITY %s "%s">', $entity, 
				html_entity_decode(sprintf('&%s;', $entity)));
		}
	}
	return sprintf("<!DOCTYPE docxap [ %s ]>", implode("\n", $entities)); 
}

function _resolveXpath($gxmlXpathExpression) {
	preg_match_all('/([\w|\-|\_]+)\:?/', $gxmlXpathExpression, $parts);
	if (!empty($parts) && !empty($parts[1])) {
		$lastPart = $parts[1][count($parts[1]) - 1];
		unset($parts[1][count($parts[1]) - 1]);
		if ($lastPart == '_BODY_') {
			$xpath = sprintf('%s/.', implode('/', $parts[1]));
		} else {
			$xpath = sprintf('%s/@%s', implode('/', $parts[1]), $lastPart);
		}
		return $xpath;
	}
	return NULL;
}

function _resolveExpression($gxmlSingleExpression) {
	if (preg_match('/_BODY_/', $gxmlSingleExpression) > 0) {
		return preg_replace('/\'%%%_BODY_%%%\'/', ".", $gxmlSingleExpression);
	}

	preg_match_all('/(\'(%%%(([\w|\-|\_]+\:)+[\w|\-|\_]+)%%%)\')/', $gxmlSingleExpression, $matches);
	if (isset($matches[0]) && count($matches[0]) > 0) {
		foreach ($matches[0] as $key => $value) {
			$gxmlSingleExpression = str_replace($matches[0][$key], _resolveXpath($matches[2][$key]), $gxmlSingleExpression);
		}
	}
	
	return preg_replace('/\'%%%([\w|\-|\_]+)%%%\'/', "ancestor-or-self::*[@\${1}][1]/@\${1}",$gxmlSingleExpression);
}

/**
	Call a xslt template
	@deprecated
*/
function call_template_dynamic($templateToCall, $matchNode, $pathToInclude, $xmlCode) {
	$xslCode = '<?xml version="1.0" encoding="UTF-8"?>
		<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
		xmlns:php="http://php.net/xsl" exclude-result-prefixes="php" extension-element-prefixes="php">
		<xsl:include href="'.$pathToInclude.'"/>
		<xsl:output method="xml" omit-xml-declaration = "yes"/>
		<xsl:param name="xmlcontent"/>
		<xsl:template match="'.$matchNode.'">
		<xsl:call-template name="'.$templateToCall.'"/>
		</xsl:template>
		</xsl:stylesheet>';


	// XSLT Transformation

	$xsltHandler = new Xslt();
	$xsltHandler->setEncoding('UTF-8');
	$xsltHandler->setXmlSrc($xmlCode);

	$xsltHandler->setXsltSrc($xslCode);

	if (!$xsltHandler->process()) {
		XMD_Log::error("Error in XSLT process: ".$xsltHandler->getError());
		return '<empty/>';
	}

	$result = html_entity_decode($xsltHandler->getResult());

	if (empty($result)) {
		$result = '<empty/>';
	}

	return $result;
}

function setNameSpace($content, $fileName) {
	// Adding dext namespace on root tag
	$rootTag = substr($fileName, 0, strpos($fileName, '.'));
	$nameSpaces = "xmlns:html=\"http://www.w3.org/TR/html401\" xmlns:dext=\"http://www.ximdex.com\"";

	if ((int) preg_match('/xmlns:dext/', $content) == 0) {

		if ((int) preg_match("/<$rootTag>/", $content) == 0) {	
			$content = str_replace("<$rootTag ", "<dext:$rootTag $nameSpaces ", $content);
		} else {
			$content = str_replace("<$rootTag>", "<dext:$rootTag $nameSpaces> ", $content);
		}

	} else {
		$content = str_replace("<$rootTag ", "<dext:$rootTag xmlns:html=\"http://www.w3.org/TR/html401\" ", $content);
	}
	
	$content = str_replace("</$rootTag>", "</dext:$rootTag>", $content);
	return $content;
}
?>