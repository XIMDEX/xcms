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
# # Module: dexpumper,  version: 3.01
# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #

# Code errors: 
# 0:	OK 
# 200:	Problem while accesing server
# 10:	Problem with settings
# 255:	die by problem of call

BEGIN {

	my $script = $0; $script =~ s[/+][/]g;
	my @path = split ("/", $script); pop @path;
	my $path = join("/", @path); $path = "." unless $path;

	$::SCRIPT_PATH = $path;
}
use lib "$::SCRIPT_PATH/../XIMDEXLIB";

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

my $batchField = undef;
my $framesField = undef;
my $sql = undef;

my %options = ("pumperid"=> \$pumperid, "verbose"=> \$::verbose, "iduser" => \$::iduser, "localbasepath"=>\$localbasepath, "tryserver"=>\$tryserver, "maxvoidcycles"=>\$maxvoidcycles, "sleeptime"=>\$sleeptime);

# Searching general parameters
GetOptions(\%options, "verbose:s", "iduser:s", "pumperid:s", "localbasepath:s", "tryserver:s", "maxvoidcycles:s", "sleeptime:s") or die "ERROR: It could not be possible to process the command\n";

my $mylog = "$ximdexCONFIG::XIMDEX_LOG_DIR/dexpumper.log";
open(MYLOG, ">>$mylog") || die "ERROR: It could not be opened file of logs $mylog ($!)";

# Calculating if it is direct or indirect mode
Logger::MyLog(1, "dexpumper daemon ver. $::version ($::fechahoy, id_user $::iduser, usuarioR:$< usuarioE:$>, PID:$$) pumperid=$pumperid..."); 

Logger::MyLog(0, "Syntax: dexpumper.pl [--iduser iduser] [--verbose verbosity] [--maxvoidcycles num --sleeptime num (--pumperid id --localbasepath path | --tryserver hostid)") if ((!$pumperid && !$localbasepath) && !$tryserver);

Logger::MyLog(2, "Entorno: localbasepath=$localbasepath, verbose=$::verbose, pumperid=$pumperid, tryserver=$tryserver, sleeptime=$sleeptime, maxvoidcycles=$maxvoidcycles"); 

# Opening BBDD
my $dbhcad = "DBI:mysql:";
$dbhcad   .= "$ximdexCONFIG::DBNAME".":";
$dbhcad   .= "$ximdexCONFIG::DBHOST".":";
Logger::MyLog(6, "Conectando con SGBD instancia '$dbhcad'"); 
my $dbh = DBI->connect( $dbhcad,
						$ximdexCONFIG::DBUSER, $ximdexCONFIG::DBPASSWD,
						{ RaiseError => 0, AutoCommit => 1}  );

Logger::MyLog(0, "Error while accessing DB") unless $dbh;

my ($host, $port, $user, $type, $pass, $rem_basepath);
my $openhost = undef;
my $remote = undef;

# Test of server
if ($tryserver) {
	
	my ($host, $port, $user, $type, $pass) = GetHostData($tryserver);
	dexPumperToLog(1, "Testing connection with host (type $type, port $port) for $user\@$host with hostid=$tryserver");

	if ($remote = new Conexion($host, $port, $user, $type, $pass)) {

		dexPumperToLog(1, "Connection OK for $tryserver");
		UpdateServerState($tryserver, "0");
		exit(0);
	} else {
		dexPumperToLog(1, "Connection NOT OK for $tryserver");
		UpdateServerState($tryserver, "1");
		exit(200);
	}
}

# It is in pumper mode, check NEW status and register STARTER and PID
RegistryInPumperRegistry($pumperid);

# Loop of tasks
my $endpump = 0;
while (!$endpump) {

	# Calculating tasg
	my ($status, $comment, $deptherror) = ();
	my @campos = undef;
	$sql = "SELECT * FROM ServerFrames WHERE (ServerFrames.State = 'Due2In' OR " .
			"ServerFrames.State = 'Due2Out') AND ServerFrames.PumperId = $pumperid LIMIT 1";
	if (@campos = $dbh->selectrow_array($sql)) {
	
		$voidcycles = 0;
		$totalcycles++;
		dexPumperToLog(1, "Executing cycle $totalcycles for pumper $pumperid...");
		my ($idtask, $hostid, $state, $olderror, $olderrorlevel, $rem_relativepath, $filename, $retries, $idbatch) = @campos[0,1,4,5,6,7,8,9,12];
		dexPumperToLog(1, "Task pumperid=$pumperid, taskid=$idtask, state=$state");
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
					dexPumperToLog(1, "It has not been possible to set a link for exchange with $host -> $errorcause (deptherror=$error)");
				}
			} else {
				
				dexPumperToLog(1, "It has not been possible to set a communication link with $host: $@");
				UpdateServerState($hostid, "1");
				exit(200);
			}
		}

		if ($openhost) {
			
			UpdateTimeInPumperRegistry($pumperid);
			# Executing assinged task
			if ($state eq "Due2In") {

				dexPumperToLog(1, "[UPLOAD][START][$idtask][$filename][$host][$type]COPYING file $filename (." . $idtask . "_" . $filename . ") to $host via $type (localpath:$localbasepath, localfile:$idtask -> remote_basepath:$rem_basepath, remote_relativepath:$rem_relativepath, filename:$filename) ...", 1);

				# Place to do the fork...
				# stat -c %s filename

				($status, $comment, $deptherror) = $remote->task_upload($localfilename, $rem_basepath, $rem_relativepath, "." . $idtask . "_" . $filename);
				dexPumperToLog(1, "[UPLOAD][STOP|$status][$idtask][$filename][$host][$type]Estado:$status Comentario:$comment", 1);
				dexPumperToLog(3, "Command error: $deptherror") if $deptherror;
				$::ErrorDetectado |= 3 if ($status !~ /^OK/);

				# We still do not set to IN, because it necessary rename it.
				AlmacenaStatusEnBD($dbh, $idtask, "Pumped", $status, $comment, $retries, $olderror, $olderrorlevel);
				UpdateServerState($openhost, ($comment =~ /ACCESS TO HOST HAS BEEN LOSTED/)); 
				UpdateTimeInPumperRegistry($pumperid);

				dexPumperToLog(1, "Checking finalization status (TRUE|FALSE) for batch #$idbatch");
				my $batchState = isBatchEndedById($idbatch, $hostid);
				if ($batchState == 1) {

					# It necessary remane all files of batch...
					my $renameStatus = renameFilesByBatch($idbatch, $rem_basepath, $remote, $hostid);
				}
			}

			if ($state eq "Due2Out") {

				dexPumperToLog(1, "[DELETE][START][$idtask][$filename][$host][$type]DELETING file $filename via $type of $rem_basepath/$rem_relativepath...", 1);
				($status, $comment) = $remote->task_delete($rem_basepath, $rem_relativepath, $filename);
				dexPumperToLog(1, "[DELETE][STOP|$status][$idtask][$filename][$host][$type]Estado:$status Comentario:$comment", 1);
				dexPumperToLog(3, "Command error: $deptherror") if $deptherror;
				$::ErrorDetectado |= 4 if ($status !~ /^OK/);

				AlmacenaStatusEnBD($dbh, $idtask, "Out", $status, $comment, $retries, $olderror, $olderrorlevel);
				UpdateServerState($openhost, ($comment =~ /ACCESS TO HOST HAS BEEN LOSTED/)); 
				UpdateTimeInPumperRegistry($pumperid);

				# For ximdex > 2.5
				# DeleteLinksForOldFrame($dbh, $idtask) if ($status =~ /^OK/);
			}
		}
	} else {
        
		# There is no tasks for this pumper...
		if ($voidcycles++ < $maxvoidcycles) {

			dexPumperToLog(3, "There is no more task to proccess in cycle $voidcycles (maximum $maxvoidcycles)... sleeping $sleeptime seconds.");
			UpdateTimeInPumperRegistry($pumperid);
			sleep($sleeptime);
        } else {
			
			dexPumperToLog(3, "There is no more tasks to process in cycle $voidcycles (maximum $maxvoidcycles) and maximum has been exceeded. Exiting of pumper $pumperid.");
			$endpump = 1;
			
			dexPumperToLog(5, "[executed Tasks]: $totalcycles", 1);
        }
    }
} # END WHILE
    
UnRegistryInPumperRegistry ($pumperid);
$dbh->disconnect;
# desconectamos ftp
if ($remote->{_type} eq "FTP") {

	my $ftp = $remote->{_ftp};
	$ftp->quit;
}
close (MYLOG);

# A task has incited an error!
# 1 direct upload, 2 -> direct deletion 
# 3 indirect upload, 4 -> indirect deletion, 7 -> both
if ($::ErrorDetectado) {

	#print "FINISHED WITH ERRORS\n";
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
			
			$operacion = "Due2In";
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
	dexPumperToLog(1, "Inconsistency detected while writting DB status") if (!defined($rows));
}

sub DeleteLinksForOldFrame {
	
	my ($dbh, $idtask) = @_;
	my $sql = "DELETE from SynchronizerFrameDependencies WHERE SourceFrame='$idtask'";
	#dexPumperToLog(1, "DEBUG --> DO $sql");
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Inconsistency detected while deleting of Frame") if (!defined($rows));
}
    
sub GetHostData {
    
	# para ximdex 2.5 y superiores
	my $hostid = shift @_;
	my @campos = $dbh->selectrow_array("SELECT * from Servers where IdServer = $hostid") or dexPumperToLog(0, "Properties of host to id $hostid are not accesible,.. ".$dbh->errstr);  
	my (undef, undef, $type, $user, $pass, $host, $port, undef, $rem_basepath) = @campos;
	dexPumperToLog(6, "Servidor -> type:'$type', user:'$user', pass:'****', host:'$host', port:'$port', rem_basepath: '$rem_basepath'"); 
    
	# overriding de campos...
	#$type = "LOCAL";
    
	# puertos accesibles
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
	dexPumperToLog(1, "It has not been possible to update server status $hostid") if (!defined($rows));
}

sub UpdateTimeInPumperRegistry {
    
	my $pumperid = shift @_;
	my $time = time();
	my $sql = "UPDATE Pumpers SET CheckTime='$time' WHERE PumperId='$pumperid'";
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "It has not been possible update time control for pumper $pumperid") if (!defined($rows));
}

sub RegistryInPumperRegistry {
    
	my $pumperid = shift @_;
	my $time = time();
	$sql = "SELECT * from Pumpers WHERE PumperId='$pumperid'";
	my @campos = $dbh->selectrow_array($sql)
		or dexPumperToLog(0, "Data of pumper $pumperid are not accesible... ".$dbh->errstr);  
	my (undef,undef,$state,$StartTime,$CheckTime,$ProcessId) = @campos;
    
	if ($state ne "New") {
        
		dexPumperToLog(0, "It has been not possible register pumper $pumperid started $StartTime while having status $state instead of NEW"); 
    } else {
        
		my $sql = "UPDATE Pumpers SET State='Started',ProcessId='$$',CheckTime='$time' WHERE PumperId='$pumperid'";
		my $rows = $dbh->do($sql); 
		dexPumperToLog(1, "It has not been possible register pumper $pumperid because of an error on DB") if (!defined($rows));
    }

	my $delay = $StartTime - $time;
	dexPumperToLog(1, "Pumper $pumperid successfully started with delay=$delay (StarTime=$StartTime") ;
	$registered = 1;
}

sub UnRegistryInPumperRegistry {
	
	my $pumperid = shift @_;
    my $time = time();
    $sql = "SELECT * from Pumpers WHERE PumperId='$pumperid'";
    my @campos = $dbh->selectrow_array($sql)
    	or dexPumperToLog(0, "Pumper data of $pumperid in query 2 are not accesible... ".$dbh->errstr);  
    my (undef,undef,$state,$StartTime,$CheckTime,$ProcessId) = @campos;
    
    if ($state ne "Started") {
        
		dexPumperToLog(0, "It has not been posible register pumper $pumperid started $StartTime because of have status $state instead of Started"); 
    } else {
        
		my $sql = "UPDATE Pumpers SET State='Ended',CheckTime='$time' WHERE PumperId='$pumperid'";
		my $rows = $dbh->do($sql); 
		dexPumperToLog(1, "It has not been possible unregister pumper $pumperid because of an error on DB") if (!defined($rows));
    }

	my $delay = $StartTime - $time;
	dexPumperToLog(1, "Pumper $pumperid successfully finished (life_time=$delay, StarTime=$StartTime)") ;
	$registered = 0;
}

# Updated counter of ServerFrames in Batchs (DEPRECATED)
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
		or dexPumperToLog(0, "Server frame data $idtask are not accesible... ".$dbh->errstr);  
	my ($idbatch) = @campos;

	my $sql = "UPDATE Batchs SET $framesField = $framesField + 1 WHERE IdBatch = $idbatch";
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "Error while updating batch counter $idbatch") if (!defined($rows));
}

sub renameFilesByBatch {
	
	my $idbatch = shift @_;
	my $rem_basepath = shift @_;
	my $remote = shift @_;
	my $idserver = shift @_;
	my $time = time();
	
	# Renaming files of batch...
	# ... With new approach of sub-batches we need
	# specify server too.
	dexPumperToLog(1, "Renaming files corresponding with...");
	
	$sql = "SELECT IdSync, RemotePath, FileName FROM ServerFrames WHERE State IN ('In', 'Pumped', 'Canceled'," .
			" 'Removed', 'Replaced', 'Outdated') AND IdBatchUp = '" . $idbatch . "' " . 
			"AND IdServer = '" . $idserver . "'";
	
	my $consulta = $dbh->prepare("$sql");
	my $basura = $consulta->execute();

	while (my ($idsync, $rem_relativepath, $filename) = $consulta->fetchrow()) {

		dexPumperToLog(1, "$idsync is a file of Batch #$idbatch and it should be renamed.");
		dexPumperToLog(1, "Rem. Base Path: $rem_basepath");
		dexPumperToLog(1, "Rem. Relative Path: $rem_relativepath");
		dexPumperToLog(1, "FileName: ." . $idsync . "_" . $filename);
		dexPumperToLog(1, "New FileName: $filename");

		my $error = $remote->rename($rem_basepath, $rem_relativepath, "." . $idsync . "_" . $filename, $filename);
	}
	
	# it is necessary puts status of all frames to 'IN'
	my $sql = "UPDATE ServerFrames SET State = 'In' WHERE IdBatchUp = '" . $idbatch . "' AND IdServer = '" . $idserver . "' AND State = 'Pumped'";
	my $rows = $dbh->do($sql); 
	dexPumperToLog(1, "It has not been possible update serverframes of 'Pumped' to 'In' because of an error on DB") if (!defined($rows));

	return (0);
}

sub isBatchEndedById {
	
	my $idbatch = shift @_;
	my $idserver = shift @_;
	my $time = time();

	# Initially query did not consider the server.
	# Introducing sub-batches (set of serverframes 
	# belonging to the same batch and server) it is necessary this consideration
	# when it time to know finalization time of a sub-batch
	# instead of a batch.
	# Note:  continue using  method which calculates finalization of complete batchs?

	$sql = "SELECT IdBatchUp, SUM( IF( State = 'Due2PumpedWithError', 1, 0 ) ) AS Errors, SUM( IF( State " .
			"IN ( " .
			"'In', 'Pumped', 'Canceled', 'Removed', 'Replaced', 'Outdated' " .
			"), 1, 0 ) ) AS Success, COUNT( IdSync ) AS Total " . 
			"FROM ServerFrames " .
			"WHERE IdBatchUp = '" . $idbatch . "' AND IdServer = '" . $idserver . "' " .
			"GROUP BY IdBatchUp";
			# "HAVING Total = Errors + Success";
    
	my @campos = $dbh->selectrow_array($sql) 
		or dexPumperToLog(0, "Error on query of $sql Batch Finished... ".$dbh->errstr);  
	my ($idbatchdb,$errors,$success,$total) = @campos;

	if ($total == $errors + $success) {
		
		dexPumperToLog(1, "Batch #idbatch is finished.");
		return (1); 
	} else {
		
		dexPumperToLog(1, "Batch #idbatch is NOT finished.");
		return (0);
	}
}

# It registers in dexpumper log, and if appropiate in table of stats  
# of syncronizer.
sub dexPumperToLog {
	
	my($nivel, $texto, $doInsertSql) = @_;
	my $time = time();

	Logger::MyLog($nivel, $texto);
	
	if ($doInsertSql) {
		my $sql = "INSERT INTO SynchronizerStats (PumperId, File, Type, Level, Time, Comment) VALUES ($pumperid, 'dexpumper.pl', '[CACTI]PUMP-INFO', $nivel, $time, '$texto')";
		my $rows = $dbh->do($sql);
	}

}
