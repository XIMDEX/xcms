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
# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #

package ximdexCONFIG;

# Provides user and password for ximdex DB
# Trying to open it of module settings of PHP
# For undetermindes values it is aplied by default in section BY_DEFAULT 
#
# CONFIGURATION BLOCK
my $config = "$::SCRIPT_PATH/../../conf/install-params.conf.php";

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

# BY_DEFAULT block
# Apply obtained value or default value if it does not exist 

$DBHOST   = $params{DBHOST}   || "ximdexhost";
$DBUSER   = $params{DBUSER}   || "ximdexuser";
$DBPASSWD = $params{DBPASSWD} || "ximdexpass";
$DBNAME   = $params{DBNAME}   || "ximdexbbdd";
$XIMDEX_ROOT_PATH   = $params{XIMDEX_ROOT_PATH}   || undef;

# Computing log path
if ($XIMDEX_ROOT_PATH && -d "$XIMDEX_ROOT_PATH/logs" ) {
	$XIMDEX_LOG_DIR = "$XIMDEX_ROOT_PATH/logs";
}
else {
	$XIMDEX_LOG_DIR = "/tmp";
}

1;
