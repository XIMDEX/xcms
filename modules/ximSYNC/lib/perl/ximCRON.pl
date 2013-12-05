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
use lib "$::SCRIPT_PATH/../lib/perl";

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

# Parametros:
# hostid, clave de acceso a tabla servers para datos conexion
# localbasepath, path a sistema local de archivos... se usa en indirecto y tarea upload directa
# iduser,
#
# MODO DIRECTO -->
# dlfile, nombre archivo local
# drpath, ruta relativa en servidor remoto (se une a la del servidor)
# drfile, nombre archivo remoto
# dcommand, tipo de comando (upload, remove)
# directmode, debe venir indicado para reforzar la invocacion
# MODO INDIRECTO --> 
# Lista de comandos en forma u:id o r:id, donde u=upload, r=remove e id es
# identificador del nodo en la tabla de sincro
 
# generales
my $hostid = undef; 
my $localbasepath = undef;
my $directmode = 0;
$::verbose = 1;
$::iduser = "ximdex";
$::ErrorDetectado = undef;
my $taskNumber = undef;

# para llamada directa
my $directLocalFileName = undef;
my $directRemotePath = undef;
my $directRemoteFileName = undef;
my $directCommand = undef;

my %options = ("verbose"=> \$::verbose, "hostid" => \$hostid, "localbasepath" => \$localbasepath, "iduser" => \$::iduser, "direct" => \$directmode, "dlfile" => \$directLocalFileName, "drpath" => \$directRemotePath, "drfile" => \$directRemoteFileName, "dcommand" => \$directCommand, "tasknumber" => \$taskNumber);

# buscamos parametros generales
GetOptions(\%options, "verbose:s", "hostid:s", "localbasepath:s", "iduser:s", "direct!", "dlfile:s", "drpath:s", "drfile:s", "dcommand:s", "tasknumber:s") or die "ERROR: No ha sido posible procesar el comando\n";

my $mylog = "$ximdexCONFIG::XIMDEX_LOG_DIR/ximCRON.log";
open(MYLOG, ">>$mylog") || die "ERROR: no puedo abrir archivo de logs $mylog ($!)";

# Lista de tareas a realizar
my @tareas = @ARGV;

# Calculamos si es modo directo o indirecto

Logger::MyLog(1, "ximCRON ver. $::version ($::fechahoy, id_proceso $::iduser, usuarioR:$< usuarioE:$>) comenzando..."); 

my $errorsintaxis = undef;

if ($directmode) {

    if (!$directCommand) {

        $errorsintaxis = 1;
        Logger::MyLog(1, "No se ha pasado el comando a realizar en modo directo");

    } elsif ("remove" =~ /^$directCommand/i) {
        $directCommand = "remove";

        unless ($directRemoteFileName) {
            $errorsintaxis = 2;
            Logger::MyLog(1, "No se han pasado los parametros requeridos en modo directo para comando remove: drfile");
        }

    } elsif ("upload" =~ /^$directCommand/i) {
        $directCommand = "upload";

        unless ($localbasepath && $directLocalFileName && $directRemoteFileName) {
            $errorsintaxis = 3;
            Logger::MyLog(1, "No se han pasado los parametros requeridos en modo directo para comando upload: localbasepath, dlfile, drfile");
        }

    } 

    if ($directCommand ne "upload" && $directCommand ne "remove") {
            $errorsintaxis = 4;
            Logger::MyLog(1, "El comando dcommand ha de ser upload o remove");
    }

} else { # indirecto

    if (!@tareas) {
        $errorsintaxis = 3;
        Logger::MyLog(1, "No se ha pasado una lista de tareas en modo indirecto");
    }
    if (!$localbasepath) {
        $errorsintaxis = 4;
        Logger::MyLog(1, "No se ha indicado el localbasepath en modo indirecto");
    }
    Logger::MyLog(1, "Tareas a procesar: ".@tareas);
    if ($taskNumber) {
        Logger::MyLog(1, "El número de tareas extraído (".@tareas.") y el declarado ($taskNumber) no coinciden!") if (@tareas != $taskNumber);
    }
}   

Logger::MyLog(1, "Se ha pasado una lista de tareas en modo directo. Se omiten!") if ($directmode && @tareas);

Logger::MyLog(1, "Se ha pasado una tarea directa en modo indirecto . Se omite!") if (!$directmode && $directCommand);

Logger::MyLog(0, "Syntax: ximCRON.pl [--iduser iduser] [--verbose verbosity] --hostid hostid --localbasepath path_to_local_files ([--direct --dcommand (upload|remove) [--dlfile local_file_name] --drpath remote_relative_path_to_file --drfile remote_file_name] | [--tasknumber number] list_tasks_id)") if (!$hostid || $errorsintaxis);

Logger::MyLog(2, "Entorno: localbasepath=$localbasepath, verbose=$::verbose, hostid=$hostid"); 

if ($directmode) {
    Logger::MyLog(2, "Entorno: dcommand=$directCommand, dlfile=$directLocalFileName, drpath=$directRemotePath, drfile=$directRemoteFileName"); 
}


# Abrimos la BBDD
my $dbhcad = "DBI:mysql:";
$dbhcad   .= "$ximdexCONFIG::DBNAME".":";
$dbhcad   .= "$ximdexCONFIG::DBHOST".":";
Logger::MyLog(6, "Conectando con SGBD instancia '$dbhcad'"); 
my $dbh = DBI->connect( $dbhcad,
                        $ximdexCONFIG::DBUSER, $ximdexCONFIG::DBPASSWD,
                        { RaiseError => 0, AutoCommit => 1}  );

Logger::MyLog(0, "Error en acceso a BBDD") unless $dbh;

# Leemos el host
# para ximdex 2.5 y superiores
my @campos = $dbh->selectrow_array("SELECT * from Servers where IdServer = $hostid") or Logger::MyLog(0, "Las propiedades del host para identificador $hostid no son accesibles,.. ".$dbh->errstr);  
my (undef, undef, $type, $user, $pass, $host, $port, undef, $rem_basepath) = @campos;
Logger::MyLog(6, "Servidor -> type:'$type', user:'$user', pass:'****', host:'$host', port:'$port', rem_basepath: '$rem_basepath'"); 

# overriding de campos...
#$type = "LOCAL";

# puertos accesibles
$port = 21 if(!$port && $type =~ /^FTP$/i);
$port = 22 if(!$port && $type =~ /^SSH$/i);
$port = undef if($type =~ /^LOCAL$/i);

Logger::MyLog(1, "Procesando tareas SINCRO (tipo $type, puerto $port) para $user\@$host");

# arrancamos la conexion remota...
my $remote = new Conexion($host, $port, $user, $type, $pass);

unless ($remote) {
    Logger::MyLog(1, "No ha sido posible establecer un vínculo de comunicación con $host: $@");
    exit(200);
}

my $error = $remote->myRunCommand("pwd");
if ($error) {
  my $errorcause = $remote->getErrorCause();
  Logger::MyLog(1, "No ha sido posible establecer un vínculo de intercambio con $host -> $errorcause (deptherror=$error)");
  exit(200);
}

if ($directmode) {
    # Modo directo
    my ($status, $comment, $deptherror) = ();
    my $localfilename = "$localbasepath/$directLocalFileName";

    if ($directCommand eq "upload") {
        Logger::MyLog(1, "COPIANDO archivo $directRemoteFileName a $host via $type (localpath:$localbasepath, localfile:$directLocalFileName -> remote_basepath:$rem_basepath, remote_relativepath:$directRemotePath, filename:$directRemoteFileName) ...");
        ($status, $comment, $deptherror) = $remote->task_upload($localfilename, $rem_basepath, $directRemotePath, $directRemoteFileName);
        Logger::MyLog(1, "Estado:$status Comentario:$comment");
        Logger::MyLog(3, "Error de comando: $deptherror") if $deptherror;
        $::ErrorDetectado = 1 if ($status !~ /^OK/);
    }

    if ($directCommand eq "remove") {
        Logger::MyLog(1, "BORRANDO archivo $directRemoteFileName via $type de $rem_basepath/$directRemotePath...");
        ($status, $comment) = $remote->task_delete($rem_basepath, $directRemotePath, $directRemoteFileName);
        Logger::MyLog(1, "Estado:$status Comentario:$comment");
        Logger::MyLog(3, "Error de comando: $deptherror") if $deptherror;
        $::ErrorDetectado = 2 if ($status !~ /^OK/);
    }

} else {

    # Modo indirecto
    # Recorremos tareas...
    my @campos = undef; 
    my ($status, $comment, $deptherror) = ();

    foreach my $tarea (@tareas) {
        my($command, $taskid) = split(/:/, $tarea);
        Logger::MyLog(0, "Tarea $tarea no tiene el formato esperado comando:identificador") unless ($command && $taskid); 
        Logger::MyLog(2, "Ejecutando tarea $tarea de sincro ... ") unless ($command && $taskid);
 
        if (@campos = $dbh->selectrow_array("SELECT * from ServerFrames where IdSync=$taskid")) {

            # Para ximdex <= 2.5
            my ($idtask, $olderror, $olderrorlevel, $rem_relativepath, $filename, $retries) = @campos[0,7,8,9,10,11];

            # Para ximdex > 2.5 (control de piscinas de publicación)
            #my   ($idtask, $olderror, $olderrorlevel, $rem_relativepath, $filename, $retries) = @campos[0,9,10,11,12,13];

            my $localfilename = "$localbasepath/$idtask";

            if ($command =~ /^u$/i) {
                Logger::MyLog(1, "COPIANDO archivo $filename a $host via $type (localpath:$localbasepath, localfile:$idtask -> remote_basepath:$rem_basepath, remote_relativepath:$rem_relativepath, filename:$filename) ...");
                ($status, $comment, $deptherror) = $remote->task_upload($localfilename, $rem_basepath, $rem_relativepath, $filename);
                Logger::MyLog(1, "Estado:$status Comentario:$comment");
                Logger::MyLog(3, "Error de comando: $deptherror") if $deptherror;
                $::ErrorDetectado |= 3 if ($status !~ /^OK/);
        
                AlmacenaStatusEnBD($dbh, $idtask, "In", $status, $comment, $retries, $olderror, $olderrorlevel);
            }
        
            if ($command =~ /^r$/i) {
                Logger::MyLog(1, "BORRANDO archivo $filename via $type de $rem_basepath/$rem_relativepath...");
                ($status, $comment) = $remote->task_delete($rem_basepath, $rem_relativepath, $filename);
                Logger::MyLog(1, "Estado:$status Comentario:$comment");
                Logger::MyLog(3, "Error de comando: $deptherror") if $deptherror;
                $::ErrorDetectado |= 4 if ($status !~ /^OK/);

                AlmacenaStatusEnBD($dbh, $idtask, "Out", $status, $comment, $retries, $olderror, $olderrorlevel);

                # Para ximdex > 2.5
                # DeleteLinksForOldFrame($dbh, $idtask) if ($status =~ /^OK/);
            }
        } else {
            Logger::MyLog(1, "Tarea con identificador $taskid no existe en BBDD".$dbh->errstr);  
        }
    }
}

$dbh->disconnect;
# desconectamos ftp
if ($remote->{_type} eq "FTP") {
	my $ftp = $remote->{_ftp};
	$ftp->quit;
}
close (MYLOG);

# Alguna tarea ha provocado error!
# 1 upload directa, 2 -> delete directa
# 3 upload indirecta, 4 -> delete indirecta, 7 -> ambas
if ($::ErrorDetectado) {
    #print "FINALIZADO CON ERRORES\n";
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
        $operacion = "Due2In";
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
    my $sql = "UPDATE ServerFrames SET Error='$error', ErrorLevel='$errorlevel', Retry='$retries', State='$operacion', Linked=0 WHERE IdSync='$idtask'";
    #Logger::MyLog(1, "DEBUG --> DO $sql");
    my $rows = $dbh->do($sql); 
    Logger::MyLog(1, "Detectada inconsistencia durante escritura de estados en BBDD") if (!defined($rows));
}

sub DeleteLinksForOldFrame {
    my ($dbh, $idtask) = @_;
    my $sql = "DELETE from SynchronizerFrameDependencies WHERE SourceFrame='$idtask'";
    #Logger::MyLog(1, "DEBUG --> DO $sql");
    my $rows = $dbh->do($sql); 
    Logger::MyLog(1, "Detectada inconsistencia durante borrado del Frame") if (!defined($rows));
}
