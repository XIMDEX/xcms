#/**
# * ximdex v.3 --- A Semantic CMS
# *
# * @copyright (C) Open Ximdex Evolution SL, {@link http://www.ximdex.org}
# * @license GNU AFFERO v3
# * $Id$
# *
#/**
# *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
# *
# *  Ximdex a Semantic Content Management System (CMS)
# *
# *  This program is free software: you can redistribute it and/or modify
# *  it under the terms of the GNU Affero General Public License as published
# *  by the Free Software Foundation, either version 3 of the License, or
# *  (at your option) any later version.
# *
# *  This program is distributed in the hope that it will be useful,
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# *  GNU Affero General Public License for more details.
# *
# *  See the Affero GNU General Public License for more details.
# *  You should have received a copy of the Affero GNU General Public License
# *  version 3 along with Ximdex (see LICENSE file).
# *
# *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
# *
# *  @author Ximdex DevTeam <dev@ximdex.com>
# *  @version $Revision$
# */


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
