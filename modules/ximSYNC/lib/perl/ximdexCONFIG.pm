
#/**
# * ximdex v.3 --- A Semantic CMS
# * Copyright (C) 2010, Open Ximdex Evolution SL <dev@ximdex.org>
# *
# * This program is commercial software.
# * Check version 2 of ximdex for the open source version.
# *
# * @author XIMDEX Team <dev@ximdex.org>
# *
# * @version $Revision: $
# *
# *
# * @copyright (C) Open Ximdex Evolution SL, {@link http://www.ximdex.org}
# * @license Commercial (check ximdex version 2 for the open source software)
# *
# * $Id$
# */

# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #
# # Framework: ximDEX v2.5
# #
# # Module: ximdexCONFIG, version: 1.10
# # Author: Juan A. Prieto
# #
# # Last modification --> 30/04/2004 by JAP
# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #

package ximdexCONFIG;

# Proporciona el usuario y clave para BBDD ximdex
# Tratamos de abrirlo de la configuración de módulos PHP
# Para valores no deteminados se aplican por defecto en la sección POR_DEFECTO 
#
# Bloque CONFIGURACION 
my $config = "$::SCRIPT_PATH/../../../../../conf/install-params.conf.php";

if (-e $config && open(CONFIG, $config)) {
	my @config = <CONFIG>;
	close(CONFIG);
	foreach (@config) {
		if (/^\s*\$(\w+)\s*=\s*("|')(.*?)\2/) {
			$params{$1} = $3;
			#print "DEBUG --> CATCH '$1' '$3'\n" if $1;
		}
	}
}

# Bloque POR_DEFECTO
# Aplicamos el valor obtenido o valor por defecto si no existe 

$DBHOST   = $params{DBHOST}   || "ximdexhost";
$DBUSER   = $params{DBUSER}   || "ximdexuser";
$DBPASSWD = $params{DBPASSWD} || "ximdexpass";
$DBNAME   = $params{DBNAME}   || "ximdexbbdd";
$XIMDEX_ROOT_PATH   = $params{XIMDEX_ROOT_PATH}   || undef;

# Calculamos la ruta de logs
if ($XIMDEX_ROOT_PATH && -d "$XIMDEX_ROOT_PATH/logs" ) {
	$XIMDEX_LOG_DIR = "$XIMDEX_ROOT_PATH/logs";
}
else {
	$XIMDEX_LOG_DIR = "/tmp";
}

1;
