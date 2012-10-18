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

 


# codigos de error: 0=OK, 200:problema en acceso a servidor, 10: problema de configuración, 255: die por problema de invocación

BEGIN {
        my $script = $0; $script =~ s[/+][/]g;
        my @path = split ("/", $script); pop @path;
        my $path = join("/", @path); $path = "." unless $path;

        $::SCRIPT_PATH = $path;
}
use lib "$::SCRIPT_PATH/../XIMDEXLIB";

use strict;

use ximdexCONFIG;
use XIMCRON;

use Getopt::Long;
use DBI;

$|=1;

$::version = "3.01";
$::verboselog = 5;
#$::MaxLevelRetries = 10;

my @fechahoy = localtime(time);
$::fechahoy = sprintf("%02d-%02d-%04d %02d:%02d.%02d", $fechahoy[3], (1+$fechahoy[4]), (1900+$fechahoy[5]), $fechahoy[2], $fechahoy[1], $fechahoy[0]);

# Parameters:
# hostid, table password, servers to data connection 
# localbasepath, path to local file system... it is used indirectly and direct upload task
# iduser,
#
# DIRECT MODE -->
# dlfile, name of local file
# drpath, relative path of remote server (it joins to the server path)
# drfile, name of remote file
# dcommand, type of command (upload, remove)
# directmode, it shoul be to enhance the call
# INDIRECT MODE --> 
# List of commands with form u:id or r:id, where u=upload, r=remove and id is
# the ID of node in the sync table
 
# general
my $hostid = undef; 
my $localbasepath = undef;
my $directmode = 0;
$::verbose = 1;
$::iduser = "ximdex";
$::ErrorDetectado = undef;
my $taskNumber = undef;

# for direct call
my $directLocalFileName = undef;
my $directRemotePath = undef;
my $directRemoteFileName = undef;
my $directCommand = undef;

my %options = ("verbose"=> \$::verbose, "hostid" => \$hostid, "localbasepath" => \$localbasepath, "iduser" => \$::iduser, "direct" => \$directmode, "dlfile" => \$directLocalFileName, "drpath" => \$directRemotePath, "drfile" => \$directRemoteFileName, "dcommand" => \$directCommand, "tasknumber" => \$taskNumber);

# Searching general parameters
GetOptions(\%options, "verbose:s", "hostid:s", "localbasepath:s", "iduser:s", "direct!", "dlfile:s", "drpath:s", "drfile:s", "dcommand:s", "tasknumber:s") or die "ERROR: It has not been possible to process command\n";

my $mylog = "$ximdexCONFIG::XIMDEX_LOG_DIR/ximCRON.log";
open(MYLOG, ">>$mylog") || die "ERROR: It can be opened the log file $mylog ($!)";

# Lista de tareas a realizar
my @tareas = @ARGV;

# Calculamos si es modo directo o indirecto

Logger::MyLog(1, "ximCRON ver. $::version ($::fechahoy, id_proceso $::iduser, usuarioR:$< usuarioE:$>) starting..."); 

my $errorsintaxis = undef;

if ($directmode) {

    if (!$directCommand) {

        $errorsintaxis = 1;
        Logger::MyLog(1, "Command to perform direct mode has not been passed");

    } elsif ("remove" =~ /^$directCommand/i) {
        $directCommand = "remove";

        unless ($directRemoteFileName) {
            $errorsintaxis = 2;
            Logger::MyLog(1, "Parameters required on direct mode for command remove have not been passed: drfile");
        }

    } elsif ("upload" =~ /^$directCommand/i) {
        $directCommand = "upload";

        unless ($localbasepath && $directLocalFileName && $directRemoteFileName) {
            $errorsintaxis = 3;
            Logger::MyLog(1, "Parameters required on direct mode for command upload have not been passed: localbasepath, dlfile, drfile");
        }

    } 

    if ($directCommand ne "upload" && $directCommand ne "remove") {
            $errorsintaxis = 4;
            Logger::MyLog(1, "Command dcommand should be upload or remove");
    }

} else { # indirecto

    if (!@tareas) {
        $errorsintaxis = 3;
        Logger::MyLog(1, "A list of task has not been passed on indirect mode");
    }
    if (!$localbasepath) {
        $errorsintaxis = 4;
        Logger::MyLog(1, "Localbasepath has not been indicated on indirect mode");
    }
    Logger::MyLog(1, "Tasks to process: ".@tareas);
    if ($taskNumber) {
        Logger::MyLog(1, "Extracted number of tasks (".@tareas.") and declared ($taskNumber) do not match") if (@tareas != $taskNumber);
    }
}   

Logger::MyLog(1, "A list of tasks has been passed on direct mode. It is ignored!") if ($directmode && @tareas);

Logger::MyLog(1, "A direct tasks has been passed on indirect mode. It is ignored!") if (!$directmode && $directCommand);

Logger::MyLog(0, "Syntax: ximCRON.pl [--iduser iduser] [--verbose verbosity] --hostid hostid --localbasepath path_to_local_files ([--direct --dcommand (upload|remove) [--dlfile local_file_name] --drpath remote_relative_path_to_file --drfile remote_file_name] | [--tasknumber number] list_tasks_id)") if (!$hostid || $errorsintaxis);

Logger::MyLog(2, "Environment: localbasepath=$localbasepath, verbose=$::verbose, hostid=$hostid"); 

if ($directmode) {
    Logger::MyLog(2, "Environmet: dcommand=$directCommand, dlfile=$directLocalFileName, drpath=$directRemotePath, drfile=$directRemoteFileName"); 
}


# Abrimos la BBDD
my $dbhcad = "DBI:mysql:";
$dbhcad   .= "$ximdexCONFIG::DBNAME".":";
$dbhcad   .= "$ximdexCONFIG::DBHOST".":";
Logger::MyLog(6, "Conectando with SGBD instance '$dbhcad'"); 
my $dbh = DBI->connect( $dbhcad,
                        $ximdexCONFIG::DBUSER, $ximdexCONFIG::DBPASSWD,
                        { RaiseError => 0, AutoCommit => 1}  );

Logger::MyLog(0, "Error while accessing DB") unless $dbh;

# Leemos el host
# para ximdex 2.5 y superiores
my @campos = $dbh->selectrow_array("SELECT * from Servers where IdServer = $hostid") or Logger::MyLog(0, "Properties of host to ID $hostid are not accessible,.. ".$dbh->errstr);  
my (undef, undef, $type, $user, $pass, $host, $port, undef, $rem_basepath) = @campos;
Logger::MyLog(6, "Servidor -> type:'$type', user:'$user', pass:'****', host:'$host', port:'$port', rem_basepath: '$rem_basepath'"); 

# overriding de campos...
#$type = "LOCAL";

# puertos accesibles
$port = 21 if(!$port && $type =~ /^FTP$/i);
$port = 22 if(!$port && $type =~ /^SSH$/i);
$port = undef if($type =~ /^LOCAL$/i);

Logger::MyLog(1, "Processing SINCRO tasks (type $type, port $port) for $user\@$host");

# arrancamos la conexion remota...
my $remote = new Conexion($host, $port, $user, $type, $pass);

unless ($remote) {
    Logger::MyLog(1, "It has not been possible set a communication link with $host: $@");
    exit(200);
}

my $error = $remote->myRunCommand("pwd");
if ($error) {
  my $errorcause = $remote->getErrorCause();
  Logger::MyLog(1, "It has not been posible set a exchange link with $host -> $errorcause (deptherror=$error)");
  exit(200);
}

if ($directmode) {
    # Direct mode
    my ($status, $comment, $deptherror) = ();
    my $localfilename = "$localbasepath/$directLocalFileName";

    if ($directCommand eq "upload") {
        Logger::MyLog(1, "COPYING file $directRemoteFileName to $host via $type (localpath:$localbasepath, localfile:$directLocalFileName -> remote_basepath:$rem_basepath, remote_relativepath:$directRemotePath, filename:$directRemoteFileName) ...");
        ($status, $comment, $deptherror) = $remote->task_upload($localfilename, $rem_basepath, $directRemotePath, $directRemoteFileName);
        Logger::MyLog(1, "Estatus:$status Comment:$comment");
        Logger::MyLog(3, "Command error: $deptherror") if $deptherror;
        $::ErrorDetectado = 1 if ($status !~ /^OK/);
    }

    if ($directCommand eq "remove") {
        Logger::MyLog(1, "DELETING file $directRemoteFileName via $type from $rem_basepath/$directRemotePath...");
        ($status, $comment) = $remote->task_delete($rem_basepath, $directRemotePath, $directRemoteFileName);
        Logger::MyLog(1, "Estatus:$status Comment:$comment");
        Logger::MyLog(3, "Command error: $deptherror") if $deptherror;
        $::ErrorDetectado = 2 if ($status !~ /^OK/);
    }

} else {

    # Indirect mode
    # Traversing tasks
    my @campos = undef; 
    my ($status, $comment, $deptherror) = ();

    foreach my $tarea (@tareas) {
        my($command, $taskid) = split(/:/, $tarea);
        Logger::MyLog(0, "The task $tarea has not expected format command:identifier") unless ($command && $taskid); 
        Logger::MyLog(2, "Executing task $tarea of sync ... ") unless ($command && $taskid);
 
        if (@campos = $dbh->selectrow_array("SELECT * from Synchronizer where IdSync=$taskid")) {

            # for ximdex <= 2.5
            my ($idtask, $olderror, $olderrorlevel, $rem_relativepath, $filename, $retries) = @campos[0,7,8,9,10,11];

            # for ximdex > 2.5 (control of publication pools)
            #my   ($idtask, $olderror, $olderrorlevel, $rem_relativepath, $filename, $retries) = @campos[0,9,10,11,12,13];

            my $localfilename = "$localbasepath/$idtask";

            if ($command =~ /^u$/i) {
                Logger::MyLog(1, "COPYING file $filename to $host via $type (localpath:$localbasepath, localfile:$idtask -> remote_basepath:$rem_basepath, remote_relativepath:$rem_relativepath, filename:$filename) ...");
                ($status, $comment, $deptherror) = $remote->task_upload($localfilename, $rem_basepath, $rem_relativepath, $filename);
                Logger::MyLog(1, "Estatus:$status Comment:$comment");
                Logger::MyLog(3, "Command: $deptherror") if $deptherror;
                $::ErrorDetectado |= 3 if ($status !~ /^OK/);
        
                AlmacenaStatusEnBD($dbh, $idtask, "IN", $status, $comment, $retries, $olderror, $olderrorlevel);
            }
        
            if ($command =~ /^r$/i) {
                Logger::MyLog(1, "DELETING file $filename via $type from $rem_basepath/$rem_relativepath...");
                ($status, $comment) = $remote->task_delete($rem_basepath, $rem_relativepath, $filename);
                Logger::MyLog(1, "Estatus:$status Comment:$comment");
                Logger::MyLog(3, "Command error: $deptherror") if $deptherror;
                $::ErrorDetectado |= 4 if ($status !~ /^OK/);

                AlmacenaStatusEnBD($dbh, $idtask, "OUT", $status, $comment, $retries, $olderror, $olderrorlevel);

                # for ximdex > 2.5
                # DeleteLinksForOldFrame($dbh, $idtask) if ($status =~ /^OK/);
            }
        } else {
            Logger::MyLog(1, "Task with identifier $taskid does not exist DB".$dbh->errstr);  
        }
    }
}

$dbh->disconnect;
# Disconnecting ftp
if ($remote->{_type} eq "FTP") {
	my $ftp = $remote->{_ftp};
	$ftp->quit;
}
close (MYLOG);

# A task has caused an error!
# 1 direct upload, 2 -> directa deletion
# 3 indirect upload, 4 -> indirect deletion, 7 -> both
if ($::ErrorDetectado) {
    #print "FINISHED WITH ERRORS\n";
    exit(10);
}

exit (0);


sub AlmacenaStatusEnBD {
    my($dbh, $idtask, $operacion, $status, $comment, $retries, $olderror, $olderrorlevel) = @_;
    my ($error, $errorlevel) = ("", "");

    my %LevelsError = ("OK"=>0, "SOFT_ERROR"=>1, "HARD_ERROR"=>2, "FATAL_ERROR"=>3);
    my @LevelsError = qw(OK SOFT_ERROR HARD_ERROR FATAL_ERROR);

    my $intErrorLevel = $LevelsError{$status};
    my $intOldErrorLevel = $LevelsError{$olderrorlevel};

    #idtarea, operacion (in,out), *_ERROR|OK, error, reintentos, $olderror, $olderrorlevel

    if ($intErrorLevel > 0) {
        $operacion = "DUE";
        $retries++;
        $error = $comment;
        $errorlevel = $status;
    }

    if ($intErrorLevel < $intOldErrorLevel) {
        $retries = 1;
    } 

    if ($intErrorLevel > $intOldErrorLevel) {
        $retries = 1;
    } 
    if ($intErrorLevel == 0) {
        $error = ""; 
        $errorlevel = "";
        $retries = 0;
    }

#   if ($retries > $::MaxLevelRetries) {
#       $intErrorLevel++;
#       if ($intErrorLevel >= $LevelsError{FATAL_ERROR}) {
#           $intErrorLevel = $LevelsError{FATAL_ERROR};
#       } else {
#           $retries = 1;
#       }
#       $errorlevel = $LevelsError[$intErrorLevel];
#   }

    ArchivaEnTareaBD($dbh, $idtask, $operacion, $error, $errorlevel, $retries);
}

sub ArchivaEnTareaBD {
    my($dbh, $idtask, $operacion, $error, $errorlevel, $retries) = @_;
    my $sql = "UPDATE Synchronizer SET Error='$error', ErrorLevel='$errorlevel', Retry='$retries', State='$operacion', Linked=0 WHERE IdSync='$idtask'";
    #Logger::MyLog(1, "DEBUG --> DO $sql");
    my $rows = $dbh->do($sql); 
    Logger::MyLog(1, "Inconsistency has been detected while writting status in DB") if (!defined($rows));
}

sub DeleteLinksForOldFrame {
    my ($dbh, $idtask) = @_;
    my $sql = "DELETE from SynchronizerFrameDependencies WHERE SourceFrame='$idtask'";
    #Logger::MyLog(1, "DEBUG --> DO $sql");
    my $rows = $dbh->do($sql); 
    Logger::MyLog(1, "Inconsistency has been detected while deleting the Frame") if (!defined($rows));
}
