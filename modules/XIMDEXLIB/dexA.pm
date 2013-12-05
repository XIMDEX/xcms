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

use strict;
use DBI;
package dexA;

my $DEXA_DBHOST   = "localhost";
my $DEXA_DBUSER   = "ximdex";
my $DEXA_DBPASSWD = "ximdexdev01";
my $DEXA_DBNAME   = "dexa";

my %STORE = (); # used in read/write dexa:stack

my $dexa_version = "devel 1.00";


sub IntoSource {
	my ($source, $sql) = @_;
	# error levels: SOFT (non die), HARD (die)
	XIMDEX::MyLog(1, "dexA module ($dexa_version) starting ...");
	my $refsourceinfo = SourceConnectionData($source);
	my ($name, $type, $dbpath, $login, $pass) =@$refsourceinfo;
	XIMDEX::MyLog(1, "Final source params $name, $type, $dbpath, $login, $pass");
	# Open current data source
	my $error = undef;
	if ($type eq "sql") {
		$error = InsertIntoTable($dbpath, $login, $pass, $sql);
	}
}
sub OutFromTable {
	my ($dbname, $table, $field, $condition, $rowtag, $celltag) = @_;

	XIMDEX::MyLog(1, "dexA module ($dexa_version) starting ...");

	$dbname = $DEXA_DBNAME unless $dbname;

	my @cells = ();  @cells  = split(/\s*,\s*/, $celltag) if $celltag =~ /,/;
	XIMDEX::MyLog(0 ,"DB name not defined as parameter or in configuration") unless ($dbname);
	XIMDEX::MyLog(0 ,"Table not defined in table parameter [$table]") unless ($table);
	XIMDEX::MyLog(0 ,"Field string not declared $field") unless $field;

	my $cadena = undef;

	my $dbhcad = "DBI:mysql:";
	$dbhcad   .= "$dbname".":";
	$dbhcad   .= "$DEXA_DBHOST".":";
	my $dbh = DBI->connect( $dbhcad,
		$DEXA_DBUSER, $DEXA_DBPASSWD,
		{ RaiseError => 0, AutoCommit => 1}  );

		XIMDEX::MyLog(0 ,"Error while opening connection to DB server:".$DBI::errstr) unless $dbh;

	my $sth = undef;
	my $sqls = "SELECT $field FROM $table";
	$sqls .= " where $condition " if $condition;

        if ($sth  = $dbh->prepare($sqls)) {
		$sth->execute() or XIMDEX::MyLog(0, "Error while SELECT:".$dbh->errstr);

                my @campos = ();
                while ( @campos = $sth->fetchrow_array ) {
			$cadena .= "<$rowtag>" if $rowtag;
			my @cell2 = @cells;
			foreach (@campos) {
				$celltag = shift @cell2 if @cell2;
				$cadena .= "<$celltag>" if $celltag;
				$cadena .= $_;
				$cadena .= "</$celltag>" if $celltag;
			}
			$cadena .= "</$rowtag>" if $rowtag;
			$cadena .= "\n";
		}
	} else {
		XIMDEX::MyLog(0, "Error before SELECT".$dbh->errstr);
	}
	return $cadena;
}

sub OutFromSource {
	my ($source, $condition, $rowtag, $celltag) = @_;
	my $output = undef;

	# error levels: SOFT (non die), HARD (die)
	XIMDEX::MyLog(1, "dexA module ($dexa_version) starting ...");
	my $refsourceinfo = SourceConnectionData($source);
	my ($name, $type, $dbpath, $login, $pass) = @$refsourceinfo;
	XIMDEX::MyLog(1, "Final source params $name, $type, $dbpath, $login, $pass");
	XIMDEX::MyLog(1, "ROW $rowtag, CELL $celltag");
	# Open actual data source
	if ($type eq "sql") {
		$output = TableWithCondition($dbpath, $login, $pass, $condition, $rowtag, $celltag);
	}
	return ($output);
}

sub TableWithCondition {
	my($dbpath, $login, $pass, $condition, $rowtag, $celltag) = @_;

	my ($host, $db, $table) = split(/:/, $dbpath);

	my $cadena = undef;

	my $dbhcad = "DBI:mysql:$db:$host";
	my $dbh = DBI->connect( $dbhcad, $login, $pass,
				{ RaiseError => 0, AutoCommit => 1}  );

	XIMDEX::MyLog(0, "Error while accessing sql source $dbpath") unless $dbh;

	my @fields = (); @fields = split(/\s*,\s*/, $celltag) if $celltag =~ /,/;

	my $sth = undef;
	my $sqls = "SELECT * FROM $table";
	#$sqls .= " where $condition";

        if ($sth  = $dbh->prepare($sqls)) {
		$sth->execute() or XIMDEX::MyLog(0, "Error while SELECT via $dbpath:".$dbh->errstr);

                my @campos = ();
                while ( @campos = $sth->fetchrow_array ) {
			$cadena .= "<$rowtag>" if $rowtag;
			my @field = @fields;
			foreach (@campos) {
				$celltag = shift @field if @field;
				$cadena .= "<$celltag>" if $celltag;
				$cadena .= $_;
				$cadena .= "</$celltag>" if $celltag;
			}
			$cadena .= "</$rowtag>" if $rowtag;
			$cadena .= "\n";
		}

	} else {
		XIMDEX::MyLog(0, "Error before SELECT via $dbpath:".$dbh->errstr);
	}
	return $cadena;
}

sub SourceConnectionData {
	my $source = shift @_;
	my $dbhcad = "DBI:mysql:";
	$dbhcad   .= "$ximdexCONFIG::DBNAME".":";
	$dbhcad   .= "$ximdexCONFIG::DBHOST".":";
	my $dbh = DBI->connect( $dbhcad,
		$ximdexCONFIG::DBUSER, $ximdexCONFIG::DBPASSWD,
		{ RaiseError => 0, AutoCommit => 1}  );

		XIMDEX::MyLog(0 ,"Error while accesing source configuration data") unless $dbh;

	my @sourcedata = $dbh->selectrow_array("SELECT * FROM dexa_source where Name='$source'") or XIMDEX::MyLog(0, "Source $source is not declared");
	XIMDEX::MyLog(0, "Can not read configuration for $source because ".$dbh->errstr) if $dbh->errstr;

	return \@sourcedata;
}

1;
