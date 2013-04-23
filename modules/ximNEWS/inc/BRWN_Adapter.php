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



 ModulesManager::file('/inc/model/RelNewsBulletins.php', 'ximNEWS');

$type = $_GET['tipo'];
$nodeID = $_GET['nodeid'];
$activaImg = $_GET['activaImg'];
$resultado = "";
$conf = new Config();
$pre_path = $conf->GetValue("AppRoot") . $conf->GetValue("NodeRoot");

switch($type){
	case "noticiasB":
	    	//noticias del boletin dado en nodeID
      		$resultado = browser_news2($nodeID);
      		break;		
	case "imagenes":
	    	$resultado = browser_images($nodeID,$activaImg); 
	    	break;
	case "LoteImagenes":
	    	$resultado = browser_lotes($nodeID);
		break;
	case "Links":
	    	//$resultado = browser_links($nodeID);
		$resultado = browser_links2($nodeID);
		break;		
	case "Files":	
		$resultado = browser_files($nodeID);
      		break;
	case "Videos":	
		$resultado = browser_video($nodeID);
      		break;
	case "dataNodeID":
	        $resultado = getDataNode($nodeID);
		break;
}			
function getDataNode($nodeID)
{
	$node = new Node($nodeID);
	return $node -> GetNodeName();
}
function browser_lotes($nodeID)
{
/*	$nodeType = new NodeType();
	$nodeType->SetByName('XimNewsDateSection');
	$nodeTypeID = $nodeType->GetID();
*/
//	$nodo = new Node($nodeID);
//	$childs = $nodo -> GetChildren();
//	$child = new Node();
	$array_lotes = array();
	$result .= '<table border="0" cellpadding="4">';
	echo "el nodo es: ".$nodeID;
//	foreach($childs as $childID){
//		$child -> SetID($childID);
		//Si el lote esta dentro de una estructura de fecha, avanzamos hasta el nombre
		//del lote.
//		if($child -> GetNodeType() == $nodeTypeID){
//			$hijo = $child -> GetChildren();
//			$dia = new Node($hijo[0]);
//			$hijosDia = $dia -> GetChildren();
//			foreach($hijosDia as $hijoDia){
//				$result .=  browser_lotesAdd($hijoDia);
//			}
//		}
//		else{
//			$result .=  browser_lotesAdd($childID);
//		}
		
//	}
	$result .= "</table>";
	return $result;
}
function browser_lotesAdd($childID)
{
	$child = new Node($childID);
	if(isset($_GET['wizard'])){
		$accion1 = "show_lotes4('" .$childID . "','" .$child -> GetNodeName() ."');";
	}
	else{
		$accion1 = "show_lotes1('" .$childID . "','" .$child -> GetNodeName() ."');";
	}	 
	$accion2 = "";
	if(isset($_GET['activa'])){
		$accion2 = " activa_lote(this);";
	}	 
	$result = '<tr>';
	$result .= '<td class="filaclara" onmouseover="cambiar_color_over(this)" onmouseout="cambiar_color_out(this)" onclick="cambiar_color_clic(this)" >';
	$result .= '<span style="cursor:hand;" onclick="' .$accion1 . $accion2 . '">' .$child -> GetNodeName(). '</span>';
	$result .= '</td>';
	$result .= '</tr>';
	return $result;
}
//se envia una tabla con los thumbnails y otra con el listado de los 
//nombres de archivos.
function browser_images($nodeID,$flag=2)
{
   	$result = "";
     	$nodo = new Node($nodeID);
	$escalas = array("0.1","0.25","0.5","0.75");
	foreach($escalas as $escala){
        	$cuadros = $nodo->class->getThumbnails($flag,$escala);
		$total = count($cuadros);
		$num_filas = ceil($total/3);
		$result .= '<table name="'.$escala.'" border="0" cellpadding="4">';
		$contador = 0;
		for($n=0; $n < $num_filas; $n++){
		     $result .= '<tr>';
		     for($i=0; $i < 3; $i++){
			  $result .= '<td class="filaclara" onmouseover="cambiar_color_over(this)" onmouseout="cambiar_color_out(this)" onclick="cambiar_color_clic(this)" >';
                          if($contador < $total){
			     $result .= $cuadros[$contador++];
			  }
			  else{
			     $result .= '&nbsp;';
		          }				
			  $result .= '</td>';
		     }
		     $result .= '</tr>';			       
		}
		$result .= '</table>';
	}		

	$childs = $nodo -> GetChildren();
	$child = new Node();
	$array_imgs = array();
	foreach($childs as $childID){
		$child -> SetID($childID);
		$array_imgs[$childID] = $child -> GetNodeName();
	}
	if($flag == 2){
	   $anexo = "view_image(this);";
	}
	else{		
	   $anexo = "selectI(this);";
	}		
	$result .= '<table border="0" cellpadding="4">';
	foreach($array_imgs as $key=>$value){
		$result .= '<tr>';
		$result .= '<td class="filaclaranegrita" onmouseover="cambiar_color_over(this)" onmouseout="cambiar_color_out(this)">';
		$result .= '<span name= "'.$key.'" style="cursor:hand;" onclick="'.$anexo.'" >' .$value . '</span>';
		$result .= '</td>';
		$result .= '</tr>';	 
	}
	$result .= "</table>";
	return $result;
}		
function browser_links($nodeID)
{
    $nodo = new Node($nodeID);
		$childs = $nodo -> TraverseTree();
		$child = new Node();
		$array_links = array();
		foreach($childs as $childID){
		    $child -> SetID($childID);
				if($child -> nodeType -> GetName() == "Link"){
				   $link = array();
				   $link[0] = $child -> GetNodeName();
				   //$array_links[count($array_links)][1] = $child->class->GetUrl();
		       $link[1] = $child ->class->GetUrl();
					 $link[2] = $childID;
					 $array_links[] = $link;
				}	 
		}
    $result .= '<table border="0" cellpadding="4">';
		$result .= '<tr><td class="cabeceratabla">&nbsp;</td><td class="cabeceratabla">Enlace</td><td class="cabeceratabla">Url</td></tr>';  
		foreach($array_links as $link){
		    $result .= '<tr>';
				$result .= '<td class="filaoscuranegrita" ><input type="checkbox" onclick="selectL(this);"></td>';
				$result .= '<td class="filaoscuranegrita" style="width:100px;">';
				$result .= '<span name= "'.$link[2].'" style="cursor:hand;" onclick="selectL(this);">' .$link[0] . '</span>';
				$result .= '</td>';
				$result .= '<td class="filaoscuranegrita" style="width:200px;">';
				$result .= '<span style="cursor:hand;" onclick="selectL(this);">' .$link[1] . '</span>';
				$result .= '</td>';
				$result .= '</tr>';	 
		 }
		 $result .= "</table>";
		 return $result;
} 
function browser_links2($nodeID)
{
    	$result .= '<table border="0" cellpadding="4">';
	$result .= '<tr><td class="filaoscuranegrita">';
	$style = 'style="width:420;height:220px;visibility:show"';
	$size = 'WIDTH=302 HEIGHT=308';
	$src = 'SRC="' . XIMDEX_ROOT_PATH . '/inc/widgets/treeview/helpers/treeselector.php?contenttype=ximNEWS_links&targetid=$nodeID' . '"';
	$result .= '<IFRAME application="yes" ID="appletdiv" NAME="treeFrame" ' . $size . $style . $src . ' ></IFRAME>';
        $result .= '</td></tr>';
        $result .= '<tr><td class="filaoscuranegrita">';
        $result .= '<input type="text" readonly name="pathfield" id="pathfield" value="' . $targetPath . '" class="cajaxg" style="width:420"> ';
	$result .= '<input type="hidden" name="target" id="target" value="' .$nodeID .'">';	
	$result .= '<input type="hidden" name="enlazar" id="enlazar">';
	$result .= '</td></tr>';
	$result .= "</table>";
	return $result;
} 

function browser_files($nodeID)
{
    $nodo = new Node($nodeID);
		$childs = $nodo -> TraverseTree();
		$child = new Node();
		$array_files = array();
		foreach($childs as $childID){
		    $child -> SetID($childID);
				if($child -> nodeType -> GetName() == "BinaryFile"){
				   $file = array();
				   $file[0] = $child -> GetNodeName();
					 
					 //$path = $child ->class->GetNodePath();
		       //$file[1] =  str_replace($pre_path,"",$path);
					 $file[1] =  $child -> GetPath();
					 $file[2] = $childID;
					 $array_files[] = $file;
				}	 
		}
		//<td class="cabeceratabla">Ruta</td>
    $result .= '<table border="0" cellpadding="4">';
		$result .= '<tr><td class="cabeceratabla">&nbsp;</td><td class="cabeceratabla">Archivo</td></tr>';  
		foreach($array_files as $file){
		    $result .= '<tr>';
				$result .= '<td class="filaoscuranegrita" ><input type="checkbox" onclick="selectF(this);"></td>';
				$result .= '<td class="filaoscuranegrita" style="width:100px;">';
				$result .= '<span name= "'.$file[2].'" style="cursor:hand;" onclick="selectF(this);">' .$file[1] . '</span>';
				$result .= '</td>';
				/*
				$result .= '<td class="filaoscuranegrita" style="width:200px;">';
				$result .= '<span style="cursor:hand;" onclick="selectL(this);">' .$file[1] . '</span>';
				$result .= '</td>';
				*/
				$result .= '</tr>';	 
		 }
		 $result .= "</table>";
		 return $result;
} 
function browser_video($nodeID)
{
    $result = "";
    $nodo = new Node($nodeID);
		$childs = $nodo -> TraverseTree();
		$child = new Node();
		$array_files = array();
		foreach($childs as $childID){
		    $child -> SetID($childID);
				$file = array();
				$file[0] = $child -> GetNodeName();
				$extension = substr($file[0],strrpos($file[0],'.')+1);
				if($extension == "wav" || $extension == "mpeg" || $extension == "mov"){ 
				   //$path = $child ->class->GetNodePath();
		       //$file[1] =  str_replace($pre_path,"",$path);
					 $file[1] =  $child -> GetPath();
					 $file[2] = $childID;
					 $array_files[] = $file;
				}		
		}
		
		if(count($array_files) > 0){
		
       $result .= '<table border="0" cellpadding="4">';
		   $result .= '<tr><td class="cabeceratabla">&nbsp;</td><td class="cabeceratabla">Video</td></tr>';  
		   foreach($array_files as $file){
		       $result .= '<tr>';
				   $result .= '<td class="filaoscuranegrita" ><input type="checkbox" onclick="selectV(this);"></td>';
				   $result .= '<td class="filaoscuranegrita" style="width:100px;">';
				   $result .= '<span name= "'.$file[2].'" style="cursor:hand;" onclick="selectV(this);">' .$file[1] . '</span>';
				   $result .= '</td>';
			     $result .= '</tr>';	 
		  }
		  $result .= "</table>";
		}	
		
		return $result;
} 


function browser_news2($nodeID)
{
    $result = "";
		$boletin = new bulletin();
		$boletin -> SetID($nodeID);
	$relNewsBulletins = new RelNewsBulletins();
    $list_news = $relNewsBulletins->getBulletinFromNew($nodeID);
    $news_primary = $list_news[0];
		$cuerpo_lista = $list_news[1];
	
    $news_secundary;
    $result .= "<table class='sortable'>";
    $result .= "<thead>";
    $result .= "<tr class='cabeceratabla'>";
    foreach($news_primary as $cabecera){
			 $result .= "<th id='th" . $cabecera . "'>";
			 $result .= "<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>" . $cabecera . "</a>";
			 $result .= "</th>";
    }			
    $result .= "</tr>";
    $result .= "</thead>";
    $result .= "<tbody>";
    foreach($cuerpo_lista as $array_new){
       $result .= '<tr class="filaoscuranegrita" onmouseover="cambiar_color_over(this)" onmouseout="cambiar_color_out(this)">';
       foreach($news_primary as $cabecera){
			    $result .= "<td>" . $array_new[$cabecera] . "</td>";
	     }		
	     $result .= "</tr>";
    }
    $result .= "</tbody>";
    $result .= "</table>";
		
		return $result;
		
}		
header('Content-type: ' . 'text/text');	
echo $resultado ;
?>
