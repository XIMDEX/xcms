#!/usr/bin/perl
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


BEGIN {
        my $script = $0; $script =~ s[/+][/]g;
        my @path = split ("/", $script); pop @path;
        my $path = join("/", @path); $path = "." unless $path;

        $::SCRIPT_PATH = $path;
}
use lib "$::SCRIPT_PATH/../XIMDEXLIB";

use strict;
use DBI;
use LWP::Simple qw(head);
use Getopt::Long;

use ximdexCONFIG;

$|=1;

$::version = "1.00";
$::verbose = 2;
$::verboselog = 4;
$::iduser = "SIN_$$";

my @fechahoy = localtime(time);
$::fechahoy = sprintf("%02d-%02d-%04d %02d:%02d.%02d", $fechahoy[3], (1+$fechahoy[4]), (1900+$fechahoy[5]), $fechahoy[2], $fechahoy[1], $fechahoy[0]); 

my %options = ("verbose"=> \$::verbose, "iduser" => \$::iduser);
GetOptions(\%options, "verbose:s", "iduser:s") or die "ERROR: It has not been possible to process command\n";
;

my $mylog = "$ximdexCONFIG::XIMDEX_LOG_DIR/URLcheck.log";
open(MYLOG, ">>$mylog") || die "ERROR: It can be opened the log file $mylog ($!)";

Logger::MyLog(1, "URLcheck ver. $::version ($::fechahoy, id_proceso $::iduser, usuarioR:$< usuarioE:$>) comenzando...");
Logger::MyLog(4, "Entorno: verbose=$::verbose");

ProcesaURLs();

sub ProcesaURLs {

	# connecting...
	my $dbhcad = "DBI:mysql:";
	$dbhcad   .= "$ximdexCONFIG::DBNAME".":";
	$dbhcad   .= "$ximdexCONFIG::DBHOST".":";
	my $dbh = DBI->connect(	$dbhcad,
				$ximdexCONFIG::DBUSER, $ximdexCONFIG::DBPASSWD,
				{ RaiseError => 0, AutoCommit => 1}  );

	Logger::MyLog(0, "Error while accesing DB") unless $dbh;

	# Catching links to check...

	my $sth = undef;
	if ($sth  = $dbh->prepare("SELECT * FROM Links")) {
		$sth->execute() or Logger::MyLog(1, "Error in SELECT requested to DB");

		my @campos = ();
		while ( @campos = $sth->fetchrow_array) {
			my ($IdLink, $Url, $Error, $ErrorString, $CheckTime) = @campos;
			Logger::MyLog(5, "READ: IdLink:$IdLink, URL:$Url, Error:$Error, ErrorString:$ErrorString, CheckTime:$CheckTime");

			# Checking validity...
			my ($ok, $refheaders) = chequeaURL($Url);

			$CheckTime = time();
			if ($ok) {
				Logger::MyLog(2, "CORRECT: $Url");
				Logger::MyLog(3, "Error counter ($Error) set to zero") if $Error;
				$Error = 0;
				$ErrorString = "";
			} else {
				Logger::MyLog(2, "FAILED:  $Url");
				$Error++;
				$ErrorString = "FAIL";
			}
			ActualizaInfoBBDD($dbh, $IdLink, $Url, $Error, $ErrorString, $CheckTime);
		} # fin del while
	} else {
		Logger::MyLog(1, "Error in SELECT previous in DB");
	}
	
	$dbh->disconnect;
	close(MYLOG);
	exit(0);
}

sub ActualizaInfoBBDD {
	my ($dbh, $IdLink, $Url, $Error, $ErrorString, $CheckTime) = @_;
	Logger::MyLog(5, "WRITING: IdLink:$IdLink, URL:$Url, Error:$Error, ErrorString:$ErrorString, CheckTime:$CheckTime");
        my $sql = "UPDATE Links SET Error='$Error', ErrorString='$ErrorString', CheckTime='$CheckTime' WHERE IdLink='$IdLink'";
        Logger::MyLog(5, "DEBUG --> DO $sql");
        my $rows = $dbh->do($sql);
        Logger::MyLog(1, "Inconsistency has been detected while writting DB for $IdLink") if (!defined($rows));
}

sub chequeaURL {
	my $url = shift @_;
	#contenttype, documentlength, modifiedtime, expires, server
	my @headers =  head($url);
	Logger::MyLog(4, "URL $url ==> @headers");
	return(1, \@headers) if @headers;
	return(0, undef);
}

package Logger;
sub MyLog {
        my ($nivel, $texto) = @_;
        my $cabecera = "URLcheck:$::iduser $::fechahoy ($nivel)";

        print ::MYLOG "$cabecera  --> $texto\n" if $nivel <= $::verboselog;
        die "$cabecera ERROR --> $texto\n" unless $nivel;

        print "$cabecera --> $texto\n" if $nivel <= $::verbose;
}

