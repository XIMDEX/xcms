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

# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #
# # Framework: ximDEX v3
# #
# # Module: dexpumper,  version: 3.01
# # Author: Juan A. Prieto
# #
# # Last modification --> 04/10/2004 by JAP (void relative path)
# # Last modification --> 21/07/2005 by JLF (330: Linked=0)
# # Last modification --> 28/11/2005 by JAP (task number control)
# # Last modification --> 16/04/2007 by JAP (daemon mode)
# # Last modification --> 24/04/2007 by JAP (daemon mode)
# # Last modification --> 23/09/2011 by Aluque (translating)
# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #

# codigos de error: 
# 0:	OK 
# 200:	Server access problem
# 10:	Configuration problem
# 255:	die due to invocation problem

BEGIN {

	my $script = $0; $script =~ s[/+][/]g;
	my @path = split ("/", $script); pop @path;
	my $path = join("/", @path); $path = "." unless $path;

	$::SCRIPT_PATH = $path;
}
use lib "$::SCRIPT_PATH/../../../lib/perl";

use strict;

use DBI;

use ximdexCONFIG;
use XIMCRON;

use Getopt::Long;

$|=1;

$::version = "4.00";
$::verboselog = 5;
$::MaxLevelRetries = 10;

my @fechahoy = localtime(time);
$::fechahoy = sprintf("%02d-%02d-%04d %02d:%02d.%02d", $fechahoy[3], (1+$fechahoy[4]), (1900+$fechahoy[5]), $fechahoy[2], $fechahoy[1], $fechahoy[0]);

# generales
my $localbasepath = undef;
my $pumperid = undef;
my $tryserver = undef;

$::verbose = 1;
$::iduser = "ximdex";
$::ErrorDetectado = undef;

my $maxvoidcycles = 100; # max number of cycles without activity
my $sleeptime = 10; # seconds sleeping
my $voidcycles = 0; # cycles without activity
my $totalcycles = 0; 
my $registered = 0;
my $activeForPumping = 1;

my $batchField = undef;
my $framesField = undef;
my $sql = undef;

my %options = ("pumperid"=> \$pumperid, "verbose"=> \$::verbose, "iduser" => \$::iduser, "localbasepath"=>\$localbasepath, "tryserver"=>\$tryserver, "maxvoidcycles"=>\$maxvoidcycles, "sleeptime"=>\$sleeptime);

# looking for general params
GetOptions(\%options, "verbose:s", "iduser:s", "pumperid:s", "localbasepath:s", "tryserver:s", "maxvoidcycles:s", "sleeptime:s") or die "ERROR: Command could not been procesed\n";

my $mylog = "$ximdexCONFIG::XIMDEX_LOG_DIR/dexpumper.log";
open(MYLOG, ">>$mylog") || die "ERROR: The log file $mylog ($!) could not been opened";

# Calculating if it direct or indirect mode
Logger::MyLog(1, "dexpumper daemon ver. $::version ($::fechahoy, id_user $::iduser, usuarioR:$< usuarioE:$>, PID:$$) pumperid=$pumperid..."); 

Logger::MyLog(0, "Syntax: dexpumper.pl [--iduser iduser] [--verbose verbosity] [--maxvoidcycles num] [--sleeptime num] (--pumperid id --localbasepath path | --tryserver hostid)") if ((!$pumperid && !$localbasepath) && !$tryserver);

Logger::MyLog(2, "Environment: localbasepath=$localbasepath, verbose=$::verbose, pumperid=$pumperid, tryserver=$tryserver, sleeptime=$sleeptime, maxvoidcycles=$maxvoidcycles"); 

# Open the DB
my $dbhcad = "DBI:mysql:";
$dbhcad   .= "$ximdexCONFIG::DBNAME".":";
$dbhcad   .= "$ximdexCONFIG::DBHOST".":";
Logger::MyLog(6, "Connecting with DBMS instance '$dbhcad'"); 
my $dbh = DBI->connect( $dbhcad,
		$ximdexCONFIG::DBUSER, $ximdexCONFIG::DBPASSWD,
		{ RaiseError => 0, AutoCommit => 1}  );

Logger::MyLog(0, "DB access Error") unless $dbh;

my ($host, $port, $user, $type, $pass, $rem_basepath);
my $openhost = undef;
my $remote = undef;

# Server test
if ($tryserver) {

	my ($host, $port, $user, $type, $pass) = GetHostData($tryserver);
	dexPumperToLog(1, "Testing connection with host (type $type, port $port) for $user\@$host with hostid=$tryserver");

	if ($remote = new Conexion($host, $port, $user, $type, $pass)) {

		dexPumperToLog(1, "Conection for $tryserver OK");
		UpdateServerState($tryserver, "0");
		exit(0);
	} else {
		dexPumperToLog(1, "Connection for $tryserver NOT OK");
		UpdateServerState($tryserver, "1");
		exit(200);
	}
}

# We're in pumper mode, checking NEW state and registering STARTED and PID
my $successRegistered = RegistryInPumperRegistry($pumperid);
if($successRegistered == 0) {
	exit(400);
}

# task loop 
my $endpump = 0;
while (!$endpump) {

# calculating task
	my ($status, $comment, $deptherror) = ();
	my @campos = undef;
	$sql = "SELECT s.* FROM ServerFrames s, Batchs b WHERE s.IdBatchUp = b.IdBatch AND (s.State = 'Due2In' OR " .
		"s.State = 'Due2Out' OR (s.State = 'Pumped' AND b.State = 'Closing')) " .
		"AND s.PumperId = $pumperid LIMIT 1";
	if (@campos = $dbh->selectrow_array($sql)) {

		$voidcycles = 0;
		$totalcycles++;
		dexPumperToLog(1, "Executing cycle $totalcycles for pumper $pumperid...");
		my ($idtask, $hostid, $state, $olderror, $olderrorlevel, $rem_relativepath, $filename, $retries, $idbatch) = @campos[0,1,4,5,6,7,8,9,12];
		dexPumperToLog(1, "Task pumperid=$pumperid, taskid=$idtask, hostid=$hostid, state=$state, olderror=$olderror, olderrorlevel=$olderrorlevel, rem_relativepath=$rem_relativepath, filename=$filename, retries=$retries, idbatch=$idbatch");
		my $localfilename = "$localbasepath/$idtask";

		UpdateTimeInPumperRegistry($pumperid);

# Reading host
		if ($openhost != $hostid) {

			$openhost = undef;
			$remote = undef;
			($host, $port, $user, $type, $pass, $rem_basepath) = GetHostData($hostid);
			dexPumperToLog(1, "Opening host (type $type, port $port) for $user\@$host");
			$remote = new Conexion($host, $port, $user, $type, $pass);

			if ($remote) {

				my $error = $remote->myRunCommand("pwd");
				if (!$error) {

# Connection OK
					$openhost = $hostid;
				} else {

					my $errorcause = $remote->getErrorCause();
					dexPumperToLog(1, "It was not possible to establish an exchanger link with  $host -> $errorcause (deptherror=$error)");
				}
			} else {

				dexPumperToLog(1, "It was not possible to establish a communication link with $host: $@");
				UpdateServerState($hostid, "1");
				exit(200);
			}
		}

		if ($openhost) {

			UpdateTimeInPumperRegistry($pumperid);
# Executing asigned task
			if ($state eq "Due2In") {

				dexPumperToLog(1, "[UPLOAD][START][$idtask][$filename][$host][$type]COPYING file $filename (." . $idtask . "_" . $filename . ") to $host through $type (localpath:$localbasepath, localfile:$idtask -> remote_basepath:$rem_basepath, remote_relativepath:$rem_relativepath, filename:$filename) ...", 1);

# Place to perform the fork...
# stat -c %s filename

				($status, $comment, $deptherror) = $remote->task_upload($localfilename, $rem_basepath, $rem_relativepath, "." . $idtask . "_" . $filename);
				dexPumperToLog(1, "[UPLOAD][STOP|$status][$idtask][$filename][$host][$type]State:$status Comment:$comment", 1);
				dexPumperToLog(3, "Command Error: $deptherror") if $deptherror;
				$::ErrorDetectado |= 3 if ($status !~ /^OK/);

# It is not setted as IN yet, as it still has to be renamed.
				AlmacenaStatusEnBD($dbh, $idtask, "Pumped", $status, $comment, $retries, $olderror, $olderrorlevel);
				UpdateServerState($openhost, ($comment =~ /HOST ACCESS LOST/)); 
				UpdateTimeInPumperRegistry($pumperid);
			}

			if ($state eq "Due2Out") {

				dexPumperToLog(1, "[DELETE][START][$idtask][$filename][$host][$type]DELETING file $filename through $type of $rem_basepath/$rem_relativepath...", 1);
				($status, $comment) = $remote->task_delete($rem_basepath, $rem_relativepath, $filename);
				dexPumperToLog(1, "[DELETE][STOP|$status][$idtask][$filename][$host][$type]State:$status Comment:$comment", 1);
				dexPumperToLog(3, "Command Error: $deptherror") if $deptherror;
				$::ErrorDetectado |= 4 if ($status !~ /^OK/);

				AlmacenaStatusEnBD($dbh, $idtask, "Out", $status, $comment, $retries, $olderror, $olderrorlevel);
				UpdateServerState($openhost, ($comment =~ /HOST ACCESS LOST/)); 
				UpdateTimeInPumperRegistry($pumperid);

# For ximdex > 2.5
# DeleteLinksForOldFrame($dbh, $idtask) if ($status =~ /^OK/);
			}

			if ($state eq "Pumped") {
				dexPumperToLog(1, "Checking end state (TRUE|FALSE) for batch #$idbatch");
				my $batchState = isBatchEndedById($idbatch, $hostid);
				if ($batchState == 1) {

# All the batch files have to be renamed
					my $renameStatus = renameFilesByBatch($idbatch, $rem_basepath, $remote, $hostid);
				}
			}
		}
	} else {

# No existing tasks for this pumper...
		if ($voidcycles++ < $maxvoidcycles) {

			dexPumperToLog(3, "No more existing tasks to process in the cycle $voidcycles (max $maxvoidcycles)... sleeping $sleeptime seconds.");
			UpdateTimeInPumperRegistry($pumperid);
			sleep($sleeptime);
		} else {

			dexPumperToLog(3, "No more existing tasks to process in the cycle  $voidcycles (max $maxvoidcycles) and maximum allowed has been exceeded. Exiting form pumper $pumperid.");
			$endpump = 1;

			dexPumperToLog(5, "[executed Tasks]: $totalcycles", 1);
		}
	}
} # END WHILE

UnRegistryInPumperRegistry ($pumperid);
$dbh->disconnect;
# Disconnecting ftp
if ($remote->{_type} eq "FTP") {

	my $ftp = $remote->{_ftp};
	$ftp->quit;
}
close (MYLOG);

# Some task has caused error!
# 1 direct upload, 2 -> direct delete
# 3 indirect upload, 4 -> indirect delete, 7 -> both
if ($::ErrorDetectado) {

#print "ENDED WITH ERRORS\n";
	exit(10);
}

exit (0);

sub AlmacenaStatusEnBD {

	my($dbh, $idtask, $operacion, $status, $comment, $retries, $olderror, $olderrorlevel) = @_;
	my %LevelsError = ("OK"=>0, "SOFT_ERROR"=>1, "FATAL_ERROR"=>2);
	my ($ErrorLabel, $ErrorLevel) = ($comment,  $LevelsError{$status});
# my $retriesToHardError = 2;
	my $retriesToFatalError = 5;

	if ($ErrorLevel > 0) {

		if ($retries > $retriesToFatalError) {

			$operacion = "Due2".$operacion."WithError";
		} else {
			#$operacion = "Due2" . $operacion;
                        if($operacion eq "Out") { 
                                $operacion = "Due2Out"; 
                        } else { 
                                $operacion = "Due2In"; 
                        } 
                        #The line above generates non-controled states. Is this affecting to unpublishing errors?
			$retries++;
		}
	}

	if ($ErrorLevel == 0) {

		$ErrorLabel = "";
		$retries = 0;
	}

	ArchivaEnTareaBD($dbh, $idtask, $operacion, $ErrorLabel, $ErrorLevel, $retries);
}

sub ArchivaEnTareaBD {

	my($dbh, $idtask, $operacion, $error, $errorlevel, $retries) = @_;
	my $sql = "UPDATE ServerFrames SET Error='$error', ErrorLevel='$errorlevel', Retry='$retries', State='$operacion', Linked=0 WHERE IdSync='$idtask'";
#dexPumperToLog(1, "DEBUG --> DO $sql");
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Inconsistency detected while writinga states in DB") if (!defined($rows));
	my $sqlReport = ""; 
 	if($operacion eq "Pumped") { 
 	    $sqlReport = "UPDATE PublishingReport SET State='Pumped', Progress='80' WHERE IdSync='$idtask' AND Progress != '-1'"; 
 	} else { 
 	    $sqlReport = "UPDATE PublishingReport SET State='$operacion', Progress='-1' WHERE IdSync='$idtask' AND Progress != '-1'";        
 	} 
 	my $rowsReport = $dbh->do($sqlReport);  
 	dexPumperToLog(1, "Detectada inconsistencia durante escritura de estados para informe en BBDD") if (!defined($rowsReport)); 

}

sub DeleteLinksForOldFrame {

	my ($dbh, $idtask) = @_;
	my $sql = "DELETE from SynchronizerFrameDependencies WHERE SourceFrame='$idtask'";
#dexPumperToLog(1, "DEBUG --> DO $sql");
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Inconsistency detected while deleting Frame") if (!defined($rows));
}

sub GetHostData {

# for ximdex >= 2.5 
	my $hostid = shift @_;
	my @campos = $dbh->selectrow_array("SELECT * from Servers where IdServer = $hostid") or dexPumperToLog(0, "The properties of the host with identificator $hostid are not accessible,.. ".$dbh->errstr);  
	my (undef, undef, $type, $user, $pass, $host, $port, undef, $rem_basepath) = @campos;
	dexPumperToLog(6, "Server -> type:'$type', user:'$user', pass:'****', host:'$host', port:'$port', rem_basepath: '$rem_basepath'"); 

# overriding the fields...
#$type = "LOCAL";

# Accessible ports
	$port = 21 if(!$port && $type =~ /^FTP$/i);
	$port = 22 if(!$port && $type =~ /^SSH$/i);
	$port = undef if($type =~ /^LOCAL$/i);
	return ($host, $port, $user, $type, $pass, $rem_basepath);
}

sub UpdateServerState {

	my ($hostid, $state) = @_;
# dexPumperToLog(1, "**************** host $hostid state $state **************************");
	$state = ($state ? "1":"0");

	my $sql = "UPDATE ServerErrorByPumper SET WithError='$state' WHERE ServerId='$hostid'";
	my $rows = $dbh->do($sql); 

	if (!defined($rows)) {
		dexPumperToLog(1, "Server state could not been updated $hostid") ;
	} else {
# Disabling/Enabling server for pumping

		$activeForPumping = ($state ? "0":"1");
		my $sql = "UPDATE Servers SET ActiveForPumping='$activeForPumping' WHERE IdServer='$hostid'";
		my $rows = $dbh->do($sql); 

		dexPumperToLog(1, "Error setting pumping activity on server $hostid") if (!defined($rows));
	}

}

sub UpdateTimeInPumperRegistry {

	my $pumperid = shift @_;
	my $time = time();
	my $sql = "UPDATE Pumpers SET CheckTime='$time' WHERE PumperId='$pumperid'";
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Pumper control time could not been updated $pumperid") if (!defined($rows));
}

sub RegistryInPumperRegistry {

	my $pumperid = shift @_;
	my $time = time();
	$sql = "SELECT * from Pumpers WHERE PumperId='$pumperid'";
	my @campos = $dbh->selectrow_array($sql)
		or dexPumperToLog(0, "The data of pumper $pumperid are not accessible... ".$dbh->errstr);  
	my (undef,undef,$state,$StartTime,$CheckTime,$ProcessId) = @campos;

	if ($state ne "New") {

		dexPumperToLog(0, "The pumper $pumperid started at $StartTime could not been registered due to its state was $state instead of NEW"); 
		exit(0);
	} else {

		my $sql = "UPDATE Pumpers SET State='Started',ProcessId='$$',CheckTime='$time' WHERE PumperId='$pumperid' AND State != 'Started'";
		my $rows = $dbh->do($sql);
		dexPumperToLog(1, "Affected rows: $rows");
		if (!defined($rows) || $rows == 0) {
			dexPumperToLog(1, "The pumper $pumperid could not been registered due to a DB error");
			return (0);
		}
	}

	my $delay = $time - $StartTime;
	dexPumperToLog(1, "Pumper $pumperid successfully stated with delay=$delay (StartTime=$StartTime") ;
	$registered = 1;

	return (1);
}

sub UnRegistryInPumperRegistry {

	my $pumperid = shift @_;
	my $time = time();
	$sql = "SELECT * from Pumpers WHERE PumperId='$pumperid'";
	my @campos = $dbh->selectrow_array($sql)
		or dexPumperToLog(0, "The data of the pumper $pumperid are not accessible in query 2... ".$dbh->errstr);  
	my (undef,undef,$state,$StartTime,$CheckTime,$ProcessId) = @campos;

	if ($state ne "Started") {

		dexPumperToLog(0, "The pumper $pumperid started at $StartTime could not been unregistered due to its state was $state instead of Started"); 
	} else {

		my $sql = "UPDATE Pumpers SET State='Ended',CheckTime='$time' WHERE PumperId='$pumperid'";
		my $rows = $dbh->do($sql); 
		dexPumperToLog(1, "The pumper $pumperid could not been unregistered due to a DB error") if (!defined($rows));
	}

	my $delay = $StartTime - $time;
	dexPumperToLog(1, "Pumper $pumperid successfully ended (life_time=$delay, StartTime=$StartTime)") ;
	$registered = 0;
}

# Updating the ServerFrames counter in Batchs (DEPRECATED)
sub UpdateCounterBatch {

	my($dbh, $idtask, $state,$errorLevel) = @_;

	dexPumperToLog(1, "UpdateCounterBatch params: idtask $idtask state $state error $errorLevel ");
	if ($state eq 'In' or $state eq 'Pumped') {

		$sql = "SELECT IdBatchUp from ServerFrames WHERE IdSync='$idtask'";
	} else {

		$sql = "SELECT Batchs.IdBatchDown from ServerFrames, Batchs WHERE " .
			"ServerFrames.IdBatchUp = Batchs.IdBatch AND ServerFrames.IdSync='$idtask'";
	}

	if ($errorLevel > 0) {

		$framesField = 'ServerFramesError';
	} else {

		$framesField = 'ServerFramesSucess';
	}

	my @campos = $dbh->selectrow_array($sql)
		or dexPumperToLog(0, "The data of the serverframe $idtask are not accessible... ".$dbh->errstr);  
	my ($idbatch) = @campos;

	my $sql = "UPDATE Batchs SET $framesField = $framesField + 1 WHERE IdBatch = $idbatch";
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Error updating the batch $idbatch counter") if (!defined($rows));
}

sub renameFilesByBatch {

	my $idbatch = shift @_;
	my $rem_basepath = shift @_;
	my $remote = shift @_;
	my $idserver = shift @_;
	my $time = time();

# Renaming the batch files...
# ... With the new approach of sub-batchs server should be also specified
	dexPumperToLog(1, "Renaming files of batch #$idbatch ...");

	$sql = "SELECT IdSync, RemotePath, FileName FROM ServerFrames WHERE State IN ('Pumped')" .
		"AND IdBatchUp = '" . $idbatch . "' " . 
		"AND IdServer = '" . $idserver . "'";

	my $consulta = $dbh->prepare("$sql");
	my $basura = $consulta->execute();

	while (my ($idsync, $rem_relativepath, $filename) = $consulta->fetchrow()) {

		dexPumperToLog(1, "$idsync is a file of the batch #$idbatch and it has to be renamed.");
		dexPumperToLog(1, "Rem. Base Path: $rem_basepath");
		dexPumperToLog(1, "Rem. Relative Path: $rem_relativepath");
		dexPumperToLog(1, "FileName: ." . $idsync . "_" . $filename);
		dexPumperToLog(1, "New FileName: $filename");

		my $error = $remote->rename($rem_basepath, $rem_relativepath, "." . $idsync . "_" . $filename, $filename);
	}

# Now, all this frames have to be stablished as 'IN'
	my $sql = "UPDATE ServerFrames SET State = 'In' WHERE IdBatchUp = '" . $idbatch . "' AND IdServer = '" . $idserver . "' AND State = 'Pumped'";
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Serverfranes could not be updated from 'Pumped' to 'In' due to a DB error") if (!defined($rows));


        my $sqlReport = "UPDATE PublishingReport SET State = 'In', Progress = '100', PubTime = " . time . " - PubTime WHERE IdBatch = '" . $idbatch . "' AND IdSyncServer = '" . $idserver . "' AND State = 'Pumped'"; 
        my $rowsReport = $dbh->do($sqlReport);  
        dexPumperToLog(1, "No ha sido posible actualizar la tabla del informe de 'Pumped' hacia 'In' por error en BD") if (!defined($rowsReport)); 

	return (0);
}

sub isBatchEndedById {

	my $idbatch = shift @_;
	my $idserver = shift @_;
	my $pumperid = shift @_;
	my $time = time();

# Scheduler changes the batch state. We only have to check state and verify that it is 'Closing'
	$sql = "SELECT State from Batchs where IdBatch = '" . $idbatch . "'";

	my @campos = $dbh->selectrow_array($sql) 
		or dexPumperToLog(0, "Query Error: $sql Batch ended... ".$dbh->errstr);  
	my ($bState) = @campos;

	if ($bState == 'Closing') {

		dexPumperToLog(1, "The batch #$idbatch is pumped.");
		return (1); 
	} else {

		dexPumperToLog(1, "The batch #$idbatch IS NOT pumped.");
		return (0);
	}
}

# Writing in dexpumper log. If indicated, also in synchronizer Stats table.
sub dexPumperToLog {

	my($nivel, $texto, $doInsertSql) = @_;
	my $time = time();

	Logger::MyLog($nivel, $texto);

	if ($doInsertSql) {
		my $sql = "INSERT INTO SynchronizerStats (PumperId, File, Type, Level, Time, Comment) VALUES ($pumperid, 'dexpumper.pl', '[CACTI]PUMP-INFO', $nivel, $time, '$texto')";
		my $rows = $dbh->do($sql);
	}

}
