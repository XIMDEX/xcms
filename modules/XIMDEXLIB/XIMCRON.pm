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
# * $Id: XIMCRON.pm 7826 2011-08-24 16:48:21Z aluque $
# */

 
# # # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #
# # # Framework: ximDEX v2.5
# # #
# # # Module: XIMCRON.pm, version: 3.01
# # # Author: Juan A. Prieto
# # # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #

use strict;

package Logger;
sub MyLog {
        my ($nivel, $texto) = @_;
	my $cabecera = "ximCRON:$::iduser:$$ $::fechahoy ($nivel)";

	print ::MYLOG "$cabecera  --> $texto\n" if $nivel <= $::verboselog;
        die "$cabecera ERROR --> $texto\n" unless $nivel;

        print "$cabecera --> $texto\n" if $nivel <= $::verbose;
}

package Conexion;
#use Net::SSH qw(ssh_cmd);
use Net::FTP;

sub new {
	my $proto = shift;
	my $clase = ref($proto) || $proto;
	my $refer = ();

	my $host = shift;
	$refer->{_host} = $host;
	my $port = shift;
	$refer->{_port} = $port;

	my $user = shift;
	$refer->{_user} = $user;
	$refer->{_type} = shift;

	my $pass = shift;

	$refer->{_connected} = undef;

	$refer->{_taskabortcause} = "";
	$refer->{_taskactionpostabort} = ();

	clearStatus($refer);

	my $con_cad = $refer->{_user}.'\@'.$refer->{_host};

	# is redundant, object does not exist
	$refer->{_connected} = undef;
	my $blessvalue = undef;

	if ($refer->{_type} =~ /ssh/i) {
		$refer->{_type} = "SSH";
		$refer->{_string} = $con_cad;
		$blessvalue = bless $refer, $clase;
		my $on = $refer->checkConn();
		if ($on) {
			Logger::MyLog(2, "Connected to $host as $user, port $port");
			return $blessvalue;
		} else {
			Logger::MyLog(2, "It has not been possible to access to $host");
			return 0;
		}

	} elsif ($refer->{_type} =~ /ftp/i) {
		$refer->{_type} = "FTP";
		$refer->{_string} = $con_cad;
		my $ftp = Net::FTP->new($refer->{_host},  Port => $refer->{_port}, Timeout => 240, Passive => 0, Debug => 0 );
		if ($ftp) {
			Logger::MyLog(2, "Connected via FTP to $host, port $port");
			my $ok = $ftp->login($user, $pass);
			$pass = "x"x(length($pass)); # borramos la clave 
			if ($ok) {
				Logger::MyLog(3, "Properly authenticated in FTP server with user '$user'");
				$refer->{_ftp} = $ftp;
				$blessvalue = bless $refer, $clase;
				my $on = $refer->checkConn();
				$refer->{_ftpbasepath} = $ftp->pwd;
				return $blessvalue;
			} else {
				my $ftperrorstring = "Cause: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
				Logger::MyLog(4, "It has not been possible to authenticate successfully in FTP server with user '$user': $ftperrorstring");
				return 0;
			}
		} else {
			Logger::MyLog(2, "It has not been possible to access to host $host");
			return 0;
		}
	} elsif ($refer->{_type} =~ /local/i) { 
		$refer->{_string} = "LOCAL";
		$refer->{_type} = "LOCAL";
		$refer->{_host} = "localserver";
		$refer->{_port} = "";
		$refer->{_user} = "localuser";
		$blessvalue = bless $refer, $clase;
		return $blessvalue;
	} else {
		Logger::MyLog(2, "Unknow type: ".$refer->{_type});
		return 0;
	}

	return undef;
}

sub setABORT {
	my $refer = shift;
	$refer->{_taskabortcause} = shift;
	$refer->{_taskactionpostabort} = @_
}

sub mustABORT {
	my $refer = shift;
	return 1 if $refer->{_taskabortcause};
	return 0;
}


sub checkConn {
	# Trying to execute a remote command...
	my $refer = shift;
	my $con_cad = $refer->{_string};
	my $error = myRunCommand($refer, "pwd", 1);

	if (!$error) {
		$refer->{_connected} = 1;
	} else {
		$refer->{_connected} = 0;
	}
	return $refer->{_connected};
}

sub clearStatus {
	my $refer = shift;
	$refer->{_error}= 0;
	$refer->{_errorstring} = "";
	$refer->{_output} = "";
}

sub errorCauseContains {
	my $refer = shift;
	my @list = @_;
	foreach my $cause (@list) {
		return 1 if $refer->{_errorstring} =~ /$cause/i;
	}
	return 0;
}

sub getErrorCause {
	my $refer = shift;
	return "ERROR: ". $refer->{_errorstring};
}

sub setErrorCause {
	my $refer = shift;
	my ($error, $error_string) = @_;
	$refer->{_errorstring} = $error_string;
	$refer->{_error} = $error;
}

sub getoutput {
	my $refer = shift;
	return $refer->{_output};
}

sub setoutput {
	my $refer = shift;
	$refer->{_output} = shift @_;
}

# Funciones de tareas principales
sub task_upload {
        my $refer = shift;
	my ($origenfile, $rem_basepath, $rem_relativepath, $remotefile) = @_;

	# limpiamos los paths...
	$origenfile =~ s|/+|/|g;
	$rem_basepath =~ s|/+|/|g;
	$rem_relativepath =~ s|/+|/|g;
	Logger::MyLog(2, "Absolute path of variable remote_relative_path convert to relative [$rem_relativepath]") if ($rem_relativepath =~ s|^/||);
	my $remotepath = "$rem_basepath/$rem_relativepath";
	$remotepath =~ s|/+|/|g;
	my $remotefile = "$remotepath/$remotefile";
	$remotefile =~ s|/+|/|g;

	Logger::MyLog(3, "UPLOAD PARAMS --> origenfile:$origenfile rem_basepath:$rem_basepath rem_relativepath:$rem_relativepath remotefile:$remotefile");
	return ("FATAL_ERROR", "SOURCE FILE DOES NOT EXIST $origenfile") unless -e $origenfile;
	#return ("FATAL_ERROR", "IT IS NOT A RELATIVE PATH [$rem_relativepath]") if $rem_relativepath =~ /^\//;

	# Checking if remote directory of base exists.
	my $error = $refer->checkdir($rem_basepath);
	if ($error) {
		my $error_cause = $refer->getErrorCause(); 
		return ("FATAL_ERROR", "REQUESTED TYPE DOES NOT SUPPORT THIS FUNCIONALITY " . $error_cause) if ($error == 5);
		my $on = $refer->checkConn(); 
		return ("FATAL_ERROR", "BASE DIRECTORY DOES NOT EXIST [$rem_basepath]") if ($on && $error == 10);
		return ("SOFT_ERROR", "ACESS TO HOST HAS BEEN LOST") unless ($on);
		return ("FATAL_ERROR", "UNKNOW ERROR #1", $error_cause);
	}

	# Creamos ruta remota...
	if ($rem_relativepath) {
		$error = $refer->createPath($rem_basepath, $rem_relativepath); 
		if ($error) {
			my $error_cause = $refer->getErrorCause(); 
			my $on = $refer->checkConn(); 
			return ("FATAL_ERROR", "IT DOES NOT EXIST APPROPRIATE PERMITS AT REMOTE DIRECTORY [$rem_basepath, $rem_relativepath]") if ($on && $error == 10);
			return ("FATAL_ERROR", " FTP SERVER COULD NOT CREATE RECURSIVELY THE REQUESTED DIRECTORY [$rem_basepath, $rem_relativepath]") if ($on && $error == 40);
			return ("FATAL_ERROR", "EXISTS AN ELEMENT WITH SAME NAME [$rem_basepath, $rem_relativepath]") if ($on && $error == 30);
			return ("FATAL_ERROR", "REQUESTED TYPE DOES NOT SUPPORT THIS FUNCIONALITY " . $error_cause) if ($error == 5);
			return ("SOFT_ERROR", "ACCESS TO HOST HAS BEEN LOST") unless ($on);
			return ("FATAL_ERROR", "UNKNOW ERROR #2", $error_cause);
		}
	} else {
		Logger::MyLog(3, "rem_relativepath:$rem_relativepath vacio, no se crea estructura de directorio relativo");
	}

	# Pending: verify if exists different element and delete it..
	Logger::MyLog(2, "Copying file[$origenfile] as [$remotefile] in [$remotepath]");
	my $error = $refer->myscp($origenfile, $remotefile);
	if ($error) {
		Logger::MyLog(2, "File NO copied because of [".$refer->{_errorstring}."]");
		return ("FATAL_ERROR", "ERROR WHILE COPYING BECAUSE OF NOT SUPPORTED TYPE") if ($error == 5);

		return("SOFT_ERROR", "ERROR WHILE COPYING to $remotefile");
	} else {
		Logger::MyLog(2, "File $remotefile copied");
		return("OK", "COPIED to $remotefile successfully finished");
	}
	return ("SOFT_ERROR", "UNCHARACTERIZED ERROR");
}

sub task_delete {
	my $refer = shift;
	my ($rem_basepath, $rem_relativepath, $remotefile) = @_;
	# limpiamos los paths...
	$rem_basepath =~ s|/+|/|g;
	$rem_relativepath =~ s|/+|/|g;
	Logger::MyLog(2, "Absolute path for variable remote_relative_path convert to relative [$rem_relativepath]") if ($rem_relativepath =~ s|^/||);
	#return ("ABORT+COMM", "IT IS NOT A RELATIVE PATH [$rem_relativepath]") if $rem_relativepath =~ /^\//;
	my $path_completo = "$rem_basepath/$rem_relativepath";
	$path_completo =~ s|/+|/|g;

	my $error = $refer->remove($path_completo, $remotefile);
	if ($error) {
		my $error_cause = $refer->getErrorCause(); 
		my $on = $refer->checkConn(); 
		return ("FATAL_ERROR", "IT DOES NOT EXIST APPROPRIATE PERMITS AT REMOTE DIRECTORY [$path_completo]") if ($on && $error == 10);
		return ("OK", "IT DOES NOT EXIST FILE TO DELETE [$remotefile] AT REMOTE DIRECTORY [$path_completo]") if ($on && $error == 20);
		return ("FATAL_ERROR", "REQUESTED TYPE DOES NOT SUPPORT THIS FUNCIONALITY  " . $error_cause) if ($error == 5);
		return ("SOFT_ERROR", "ACCESS TO HOST HAS BEEN LOST") unless ($on);
		return ("FATAL_ERROR", "UNKNOW ERROR #3", $error_cause);
	}

	# Deleting container directories
	Logger::MyLog(3, "Deleting empty parent directories...");
	my @lista = split ("/", $rem_relativepath); 
	while (my $dir = pop @lista) {
		my $path_completo = "$rem_basepath/".join("/", @lista);
		Logger::MyLog(4, "Deleting directory $dir in $path_completo if it is empty");
		$refer->rmdir($path_completo, $dir);
	}	
	return("OK", "FILE $remotefile has been deleted");
}

# remove file
# 0 si OK
# 10, 20, 99 si NOT OK, 40 fallo en ftp
# Propaga error 5
sub remove {
	my $refer = shift;
	my ($pathcompleto, $remotefile) = @_;
	# Add ftp support ftp

	my $type = $refer->{_type};
	my $error = undef;

	if ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		my $ok = $ftp->cwd($refer->{_ftpbasepath});
		if ($ok) {
			$ok = $ftp->delete("$pathcompleto/$remotefile");
			unless ($ok) {
				my $ftperrorstring = "Cause: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
				$error = "FTP delete has failed... $ftperrorstring";
				$refer->setErrorCause(40, $error);
				Logger::MyLog(7, "FTP delete para $pathcompleto/$remotefile ha fallado: ". $ftperrorstring);
			}

		} else {
			my $ftperrorstring = "Cause: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
			$error = "FTP cwd has failed... $ftperrorstring";
			$refer->setErrorCause(40, $error);
			Logger::MyLog(7, "FTP cwd previous to deletion of $pathcompleto/$remotefile has failed: ". $ftperrorstring);
		}
	} else {
		my $cad = "rm -rf $pathcompleto/$remotefile";
		$error = $refer->execute($cad);
	}

	if (!$error) {
		Logger::MyLog(2, "File [$remotefile] ein [$pathcompleto] deleted via $type");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "It does not exist appropriate permits to deletion in directory [$pathcompleto] which contains it");
			return 10;
		} elsif ($refer->errorCauseContains("no such file or directory", "File or directory does not exist", "cannot find the file")) {
			Logger::MyLog(4, "File does not exist [$remotefile] in [$pathcompleto] in remote server");
			return 20;
		} elsif ($error == 5) {
			# Lo propagamos hacia arriba
			return 5;
		} else {
			Logger::MyLog(4, $refer->getErrorCause());
			return 99;
		}
	}
	return 0;
}

# rename file
# 0 si OK
# 10, 20, 99 si NOT OK, 40 fallo en ftp
# Propaga error 5
sub rename {
	my $refer = shift;
	my ($rem_basepath, $rem_relativepath, $remotefile, $newremotefile) = @_;

	# limpiamos los paths...
	$rem_basepath =~ s|/+|/|g;
	$rem_relativepath =~ s|/+|/|g;
	Logger::MyLog(2, "Absolute path for variable remote_relative_path convert to relative [$rem_relativepath]") if ($rem_relativepath =~ s|^/||);
	my $remotepath = "$rem_basepath/$rem_relativepath";
	$remotepath =~ s|/+|/|g;
	my $remotefile = "$remotepath/$remotefile";
	$remotefile =~ s|/+|/|g;
	my $newremotefile = "$remotepath/$newremotefile";
	$newremotefile =~ s|/+|/|g;

	my $type = $refer->{_type};
	my $error = undef;

	if ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		my $ok = $ftp->cwd($refer->{_ftpbasepath});
		if ($ok) {
			$ok = $ftp->rename("$remotefile", "$newremotefile");
			unless ($ok) {
				my $ftperrorstring = "Cause: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
				$error = "FTP remove ha fallado... $ftperrorstring";
				$refer->setErrorCause(40, $error);
				Logger::MyLog(7, "FTP remove for $remotefile => $newremotefile has failed: ". $ftperrorstring);
			}

		} else {
			my $ftperrorstring = "Cause: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
			$error = "FTP cwd has failed... $ftperrorstring";
			$refer->setErrorCause(40, $error);
			Logger::MyLog(7, "FTP cwd previous to remane of $remotefile => $newremotefile has failed: ". $ftperrorstring);
		}
	} else {
		my $cad = "mv $remotefile $newremotefile";
		$error = $refer->execute($cad);
	}

	if (!$error) {
		Logger::MyLog(2, "File [$remotefile] rename to [$newremotefile] via $type");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "It does not exist appropriate permits to deletion in directory which contains it");
			return 10;
		} elsif ($refer->errorCauseContains("no such file or directory", "File or directory does not exist", "cannot find the file")) {
			Logger::MyLog(4, "File does not exist [$remotefile] in remote server");
			return 20;
		} elsif ($error == 5) {
			# Lo propagamos hacia arriba
			return 5;
		} else {
			Logger::MyLog(4, $refer->getErrorCause());
			return 99;
		}
	}
	return 0;
}

# myscp
# 1 NOT OK, 5 NO SOPORTADO, 
# 0 OK
sub myscp {
	my $refer = shift;
	my($ori, $rem) = @_;

	my $user = $refer->{_user};
	my $host = $refer->{_host};
	my $port = $refer->{_port};

	Logger::MyLog(3, "INVOCANDO ximcp para $ori -> $user\@$host:$rem [$port]");
	my $error = $refer->ximcp($ori, "$user\@$host", $rem, $port);

	return 5 if ($error == 5); # Propagamos error de metodo no soportado

	if (!defined($error)) {
		$refer->{_errorstring} = "ximcp can not found scp executable";
		return 1;
	} else {
		if ($error) {
			chomp($error);
			$refer->setErrorCause(1, $error);
			return 1;
		}
	}
	
	# Deleting origin!
	# my $unlinking = unlink $ori;
	# if ($unlinking == 1) {
	# 	Logger::MyLog(3, "Local file $ori deleted.");
	# } else {
	# 	Logger::MyLog(3, "ADVERT: Local file $ori could not be deleted (devuelto=$unlinking).");
	# }

	# ha ido bien el scp, lo reportamos
	return 0;
}

sub ximcp {
	my $refer = shift;
	my ($ori, $userhost, $end, $port) = @_;
	my $type = $refer->{_type};
	my $error = undef;
	if ($type  eq "SSH") {
		$error = qx/scp -P $port $ori ${userhost}:${end} 2>&1/;
	} elsif ($type eq "LOCAL") {
		# @@
		$error = qx/cp -p $ori $end 2>&1/;
		
		
		# Opcion control de time out
		# eval {
		# 	local $SIG{ALRM} = sub {die "alarm\n"};
		# 	alarm 10;
		# 	$error = qx/cp -p $ori $end 2>&1/;
		# 	alarm 0;
		# };
		
		# if ($@ eq "alarm\n") {
		# 	Logger::MyLog(1, "[UPLOAD] Timed Out");
		# 	$error = "timed out";
		# }
		
		# Opcion forking para supervision
		# my $pid = fork();
		# if (not defined $pid) {
		# 	Logger::MyLog(1, "Ha fallado el fork. Se ejecutara 'cp -p $ori $end 2>&1' sin proceso paralelo de comprobacion de stat");
		# 	$error = qx/cp -p $ori $end 2>&1/;
		# } elsif ($pid == 0) {
		# 	Logger::MyLog(1, "IM PUMPER CHILD. Ejecutando 'cp -p $ori $end 2>&1'...");
		# 	# $error = qx/cp -p $ori $end 2>&1/;
	    #     system "echo -n";
		# 	exit(0);
		# } else {
		# 	Logger::MyLog(1, "IM PUMPER PARENT. Supervising 'cp -p $ori $end 2>&1'...");
		# 	waitpid($pid,0);
		# 	Logger::MyLog(1, "IM PUMPER PARENT. CHILD HAS TERMINATED...");
		# }
		
	} elsif ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		my $ok = $ftp->cwd($refer->{_ftpbasepath});
		return "no base path" unless $ok;
		$ok = $ftp->binary();
		return "no binary mode" unless $ok;
		$ok = $ftp->put($ori, $end);
		if ($ok) {
			Logger::MyLog(4, "Transferencia FTP realizada.");
			return 0;
		} else {
			my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
			Logger::MyLog(4, "FTP transfer NOT performed: $ftperrorstring");
			return $ftp->message;
		}
	} else {
		$refer->setErrorCause(5, "Unknow type in ximcp");
		return 5;
	}
	return $error; 
}

# createPath
# O OK, 1 NOT OK, 40 error ftp, 2 indeterminado
sub createPath {
        my $refer = shift;
	my $type = $refer->{_type};
	my ($rem_basepath, $rem_relativepath) = @_;
	my $basepath = $rem_basepath;

	Logger::MyLog(3, "Creating remote directory [$rem_relativepath] with recursively form in [$rem_basepath]");
	if ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		# ya estamos en el base
		my $dir = $ftp->mkdir("$rem_relativepath", 1);
		if ($dir) {
			Logger::MyLog(4, "Remote directory [$dir] has been created recursively by FTP");
			return 0;
		} else {
			my $ftperrorstring = "Código: ".$ftp->code." Causa: ".$ftp->message; chomp($ftperrorstring);
			Logger::MyLog(4, "Remote directory [$rem_relativepath] has NOT been recursively created by FTP because of: $ftperrorstring");

			$refer->setErrorCause(40, "It has not been possible to create recursively the directory by FTP remote server: $ftperrorstring");
			return 40;
		}
	} else {
		my @lista = split("/", $rem_relativepath);
		foreach my $directory (@lista) {
			my $error = $refer->mkdir($basepath, $directory);	
			# error 20, alredy exists directory it is not an error cause...
			$error = 0 if $error == 20;
	
			return $error if $error;
			$basepath = "$basepath/$directory";
		}

		my $error = $refer->checkdir("$rem_basepath/$rem_relativepath");
	
		if ($error) {
			Logger::MyLog(4, "Remote directory [$rem_relativepath] has not been succesfully created");
			# PENDING: verify if exists some different  at remote and delete it.
			return $error;
		} else {
			Logger::MyLog(4, "Verifying createdremote directory [$rem_relativepath]");
			return 0;
		}
	}
	return 2;
}

sub pwd {
        my $refer = shift;
	return $refer->runinline("pwd");
}

# checkdir
# Devuelve 0 si OK
# Devuelve 10, 99 si NOT OK
# Propaga 5
sub checkdir {
	my $refer = shift;
	my $type = $refer->{_type};
	my $remotepath = shift;

	my $error = undef;
	if ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		my $output = undef;
		($error, $output) = $refer->FTPCommand($ftp, "cwd", $remotepath);
		$refer->setErrorCause(40, $output) if ($error);
	} else {
		$error = $refer->execute("cd $remotepath;");
	}


	if (!$error) {
		Logger::MyLog(4, "Exists $remotepath");
		return 0;
	} else {
		if ($refer->errorCauseContains("no such file or directory", "No existe el fichero o el directorio")) {
			Logger::MyLog(4, "Destination path does not exist");
			return 10;
		} elsif ($error == 5) {
			return 5; # lo propagamos
		} else {
			Logger::MyLog(4, $refer->getErrorCause());
			return 99;
		}
	}
}

# mkdir
# Devuelve 0 si OK
# Devuelve 10, 20, 30, 99 si NOT OK
# Propaga 5
sub mkdir {
	my $refer = shift;
	my ($basepath, $directory) = @_;
  
	# la alternativa para ftp se implementa en createPath al tener RECURSE

  #my $cad = "cd $basepath; mkdir $directory";
  my $cad = "echo ''; cd $basepath; mkdir $directory"; # el echo '' corrige BUG con activación de salida estandar terminales SUN
  
	my $error = $refer->execute($cad);
	if (!$error) {
		Logger::MyLog(4, "Remote directory [$directory] created in [$basepath]");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "Appropriate permits to create it does not exist");
			return 10;
		} elsif ($refer->errorCauseContains("File exist", "fichero existe", "fichero ya existe")) {
			Logger::MyLog(4, "Requested directory already exists");
			return 20;
		} elsif ($refer->errorCauseContains("exists but is not a directory", "existe pero no es un directorio")) {
			Logger::MyLog(4, "Exists an element, not directory, with same name");
			return 30;
		} elsif ($error == 5) {
			return 5; # lo propagamos
		} else {
			Logger::MyLog(4, $refer->getErrorCause());
			return 99;
		}
	}
	return 0;
}

# rmdir
# Devuelve 0 si OK
# Devuelve 10, 20, 30, 99 si NOT OK
# Propaga 5
# Atención--> NUNCA poner recursivo. Debe fallar si contiene algun elemento... 
sub rmdir {
	my $refer = shift;
	my ($basepath, $directory) = @_;
	my $type = $refer->{_type};

	my $error = undef;
	if ($type eq "FTP") {
		my $dir = "$basepath/$directory";
		my $output = undef;
		my $ftp = $refer->{_ftp};
		# Atencion--> NO poner recursivo. Debe fallar si contiene algo
		($error, $output) = $refer->FTPCommand($ftp, "rmdir", $dir);
		$refer->setErrorCause(40, $output) if $error;
	} else {
		my $cad = "rmdir $basepath/$directory";
		# Atencion--> NUNCA cambiar a rm -rf !!!. Usar remove para ello
		$error = $refer->execute($cad);
	}

	if (!$error) {
		Logger::MyLog(4, "Deleted remote directory [$directory] in [$basepath]");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "Appropriate permits to delete it does not exist");
			return 10;
		} elsif ($refer->errorCauseContains("no such file", "No existe el fichero")) {
			Logger::MyLog(4, "Requested directory does not exist");
			return 20;
		} elsif ($refer->errorCauseContains("directory not empty", "directorio no vacio")) { # directorio no vacio
			Logger::MyLog(4, "Directory contains elements");
			return 30;
		} elsif ($error == 5) {
			return 5; # lo propagamos
		} else {
			Logger::MyLog(4, $refer->getErrorCause());
			return 99;
		}
	}
	return 0;
}



# FUNCIONES BAJO NIVEL
# Devuelve 0 si OK
# Propaga el error de myRunCommand si NOT OK (1, 5)
sub execute {
        my $refer = shift;
	my $remote_comm = shift;

	my $error = $refer->myRunCommand($remote_comm);

	return ($error);
}

# Ejecuta comando remoto
# Devuelve 0 OK 
# Devuelve 1 NOT OK y 5 Metodo no valido para tipo
sub myRunCommand {
	my $refer= shift;
	my $cmd = shift;

	my $notwriteerror = shift || 0;
	Logger::MyLog(9, "  Not-write error status flag propagated is: $notwriteerror");

	clearStatus($refer) unless $notwriteerror;

	my $remote = $refer->{_string};
	my $port = $refer->{_port};
	my $output = undef;
	my $type = $refer->{_type};

	my $comando = "";

	Logger::MyLog(5, "  Executing $cmd in $remote via $type...");
	if ($type eq "SSH") {
		$comando = "ssh -p $port $remote \"$cmd\" ";
		# lo ejecutamos posteriormente
	} elsif ($type eq "LOCAL") {
		$comando = "$cmd";
		# lo ejecutamos posteriormente
	} elsif ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		my $error = undef;
		($error, $output) = $refer->FTPCommand($ftp, $cmd);
		unless ($error) {
			Logger::MyLog(5, "OUTPUT: $output");
			$refer->{_output} = $output;
			return 0;
		} else {
			Logger::MyLog(5, "ERROR: $output");
			$refer->{_errorstring} = $output unless $notwriteerror;
			return 1;
		}
	} else {
		$refer->setErrorCause(5, "Unknow type $type");
		return 5;
	}

	#
	# ejecutamos los interactivos (ssh, local)
	#
	#my $reteval = eval {$output = qx/ssh -p $port $remote \"$cmd\" 2>&1/;};
	my $reteval = eval {$output = qx/$comando 2>&1/;};

	Logger::MyLog(9, "*RETEVAL reteval=$reteval, output=$output, errorint=$?, erroradmin=$!, errorarro=$@, defined:". defined($reteval));

	if ($?) {
		$refer->{_errorstring} = $output unless $notwriteerror;
		Logger::MyLog(5, "ERROR: $output");
		return 1;
	} else {
		$refer->{_output} = $output;
		Logger::MyLog(5, "OUTPUT $output");
		return 0;
	}
}

sub FTPCommand {
	my $refer = shift;
	my $ftp = shift;
	my $cmd = shift;
	my $argument = shift;

	my $error = 1;
	my $output = undef;

	# Executing command on FTP object...
	if ($argument) {
		$output = $ftp->$cmd($argument);
	} else {
		$output = $ftp->$cmd;
	}

	# Checking errors...
	if ($output) {
		Logger::MyLog(5, "  Command $cmd($argument) via FTP returns $output");
		$error = 0;
	} else {
		my $ftperrorstring = "Cause: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
		Logger::MyLog(5, "  Command $cmd($argument) via FTP returns error: $ftperrorstring");
		$output = $ftperrorstring;
		$error = 1;
	}

	return ($error, $output);
}

#Returns string or undef if error
sub runinline {
        my $refer = shift;
	my $remote_comm = shift;

	my $error = $refer->myRunCommand($remote_comm);

	if ($error) {
		return undef;
	} else {
		return $refer->{_output};
	}
}


1;
