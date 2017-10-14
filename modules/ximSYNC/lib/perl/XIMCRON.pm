 
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

	# es redundante por ahora, no existe el objeto
	$refer->{_connected} = undef;
	my $blessvalue = undef;

	if ($refer->{_type} =~ /ssh/i) {
		$refer->{_type} = "SSH";
		$refer->{_string} = $con_cad;
		$blessvalue = bless $refer, $clase;
		my $on = $refer->checkConn();
		if ($on) {
			Logger::MyLog(2, "Conectado a $host como $user, puerto $port");
			return $blessvalue;
		} else {
			Logger::MyLog(2, "No se ha podido acceder al host $host");
			return 0;
		}

	} elsif ($refer->{_type} =~ /ftp/i) {
		$refer->{_type} = "FTP";
		$refer->{_string} = $con_cad;
		my $ftp = Net::FTP->new($refer->{_host},  Port => $refer->{_port}, Timeout => 240, Passive => 0, Debug => 0 );
		if ($ftp) {
			Logger::MyLog(2, "Conectado via FTP a $host, puerto $port");
			my $ok = $ftp->login($user, $pass);
			$pass = "x"x(length($pass)); # borramos la clave 
			if ($ok) {
				Logger::MyLog(3, "Autenticado correctamente en servidor FTP con user '$user'");
				$refer->{_ftp} = $ftp;
				$blessvalue = bless $refer, $clase;
				my $on = $refer->checkConn();
				$refer->{_ftpbasepath} = $ftp->pwd;
				return $blessvalue;
			} else {
				my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
				Logger::MyLog(4, "No ha sido posible autenticarse correctamente en servidor FTP con user '$user': $ftperrorstring");
				return 0;
			}
		} else {
			Logger::MyLog(2, "No se ha podido acceder al host $host");
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
		Logger::MyLog(2, "Tipo desconocido: ".$refer->{_type});
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
	# Probamos a ejecutar un comando remoto...
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
	Logger::MyLog(2, "Ruta absoluta para variable remote_relative_path convertida a relativa [$rem_relativepath]") if ($rem_relativepath =~ s|^/||);
	my $remotepath = "$rem_basepath/$rem_relativepath";
	$remotepath =~ s|/+|/|g;
	my $remotefile = "$remotepath/$remotefile";
	$remotefile =~ s|/+|/|g;

	Logger::MyLog(3, "UPLOAD PARAMS --> origenfile:$origenfile rem_basepath:$rem_basepath rem_relativepath:$rem_relativepath remotefile:$remotefile");
	return ("FATAL_ERROR", "NO EXISTE ARCHIVO ORIGEN $origenfile") unless -e $origenfile;
	#return ("FATAL_ERROR", "NO ES RUTA RELATIVA [$rem_relativepath]") if $rem_relativepath =~ /^\//;

	# chequeamos que existe el directorio remoto de base
	my $error = $refer->checkdir($rem_basepath);
	if ($error) {
		my $error_cause = $refer->getErrorCause(); 
		return ("FATAL_ERROR", "EL TIPO SOLICITADO NO SOPORTA LA FUNCIONALIDAD " . $error_cause) if ($error == 5);
		my $on = $refer->checkConn(); 
		return ("FATAL_ERROR", "NO EXISTE DIRECTORIO BASE [$rem_basepath]") if ($on && $error == 10);
		return ("SOFT_ERROR", "SE HA PERDIDO EL ACCESO AL HOST") unless ($on);
		return ("FATAL_ERROR", "ERROR INDETERMINADO #1", $error_cause);
	}

	# Creamos ruta remota...
	if ($rem_relativepath) {
		$error = $refer->createPath($rem_basepath, $rem_relativepath); 
		if ($error) {
			my $error_cause = $refer->getErrorCause(); 
			my $on = $refer->checkConn(); 
			return ("FATAL_ERROR", "NO EXISTEN LOS PERMISOS ADECUADOS EN DIRECTORIO REMOTO [$rem_basepath, $rem_relativepath]") if ($on && $error == 10);
			return ("FATAL_ERROR", "SERVIDOR FTP NO HA PODIDO CREAR RECURSIVAMENTE EL DIRECTORIO PEDIDO [$rem_basepath, $rem_relativepath]") if ($on && $error == 40);
			return ("FATAL_ERROR", "EXISTE ELEMENTO CON EL MISMO NOMBRE [$rem_basepath, $rem_relativepath]") if ($on && $error == 30);
			return ("FATAL_ERROR", "EL TIPO SOLICITADO NO SOPORTA LA FUNCIONALIDAD " . $error_cause) if ($error == 5);
			return ("SOFT_ERROR", "SE HA PERDIDO EL ACCESO AL HOST") unless ($on);
			return ("FATAL_ERROR", "ERROR INDETERMINADO #2", $error_cause);
		}
	} else {
		Logger::MyLog(3, "rem_relativepath:$rem_relativepath vacio, no se crea estructura de directorio relativo");
	}

	# Pending: verificar si existe elemento diferente y eliminar...
	Logger::MyLog(2, "Copiando archivo [$origenfile] como [$remotefile] en [$remotepath]");
	my $error = $refer->myscp($origenfile, $remotefile);
	if ($error) {
		Logger::MyLog(2, "Archivo NO copiado por [".$refer->{_errorstring}."]");
		return ("FATAL_ERROR", "ERROR EN COPIADO POR TIPO NO SOPORTADO") if ($error == 5);

		return("SOFT_ERROR", "ERROR EN COPIADO a $remotefile");
	} else {
		Logger::MyLog(2, "Archivo $remotefile copiado");
		return("OK", "COPIADO a $remotefile finalizado correctamente");
	}
	return ("SOFT_ERROR", "ERROR NO CARACTERIZADO");
}

sub task_delete {
	my $refer = shift;
	my ($rem_basepath, $rem_relativepath, $remotefile) = @_;
	# limpiamos los paths...
	$rem_basepath =~ s|/+|/|g;
	$rem_relativepath =~ s|/+|/|g;
	Logger::MyLog(2, "Ruta absoluta para variable remote_relative_path convertida a relativa [$rem_relativepath]") if ($rem_relativepath =~ s|^/||);
	#return ("ABORT+COMM", "NO ES RUTA RELATIVA [$rem_relativepath]") if $rem_relativepath =~ /^\//;
	my $path_completo = "$rem_basepath/$rem_relativepath";
	$path_completo =~ s|/+|/|g;

	my $error = $refer->remove($path_completo, $remotefile);
	if ($error) {
		my $error_cause = $refer->getErrorCause(); 
		my $on = $refer->checkConn(); 
		return ("FATAL_ERROR", "NO EXISTEN LOS PERMISOS ADECUADOS EN DIRECTORIO REMOTO [$path_completo]") if ($on && $error == 10);
		return ("OK", "NO EXISTIA EL ARCHIVO A BORRAR [$remotefile] EN DIRECTORIO REMOTO [$path_completo]") if ($on && $error == 20);
		return ("FATAL_ERROR", "EL TIPO SOLICITADO NO SOPORTA LA FUNCIONALIDAD " . $error_cause) if ($error == 5);
		return ("SOFT_ERROR", "SE HA PERDIDO EL ACCESO AL HOST") unless ($on);
		return ("FATAL_ERROR", "ERROR INDETERMINADO #3", $error_cause);
	}

	# Borramos los directorios contenedores ...
	Logger::MyLog(3, "Eliminando directorios padres vacios...");
	my @lista = split ("/", $rem_relativepath); 
	while (my $dir = pop @lista) {
		my $path_completo = "$rem_basepath/".join("/", @lista);
		Logger::MyLog(4, "Eliminando directorio $dir en $path_completo si vacio");
		$refer->rmdir($path_completo, $dir);
	}	
	return("OK", "ARCHIVO $remotefile ha sido eliminado");
}

# remove file
# 0 si OK
# 10, 20, 99 si NOT OK, 40 fallo en ftp
# Propaga error 5
sub remove {
	my $refer = shift;
	my ($pathcompleto, $remotefile) = @_;
	# añadir soporte ftp

	my $type = $refer->{_type};
	my $error = undef;

	if ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		my $ok = $ftp->cwd($refer->{_ftpbasepath});
		if ($ok) {
			$ok = $ftp->delete("$pathcompleto/$remotefile");
			unless ($ok) {
				my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
				$error = "FTP delete ha fallado... $ftperrorstring";
				$refer->setErrorCause(40, $error);
				Logger::MyLog(4, "FTP delete para $pathcompleto/$remotefile ha fallado: ". $ftperrorstring);
			}

		} else {
			my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
			$error = "FTP cwd ha fallado... $ftperrorstring";
			$refer->setErrorCause(40, $error);
			Logger::MyLog(4, "FTP cwd previo a borrado de $pathcompleto/$remotefile ha fallado: ". $ftperrorstring);
		}
	} else {
		my $cad = "rm -rf $pathcompleto/$remotefile";
		$error = $refer->execute($cad);
	}

	if (!$error) {
		Logger::MyLog(2, "Archivo [$remotefile] en [$pathcompleto] eliminado via $type");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "No existen los permisos adecuados para su borrado en el directorio [$pathcompleto] que lo contiene");
			return 10;
		} elsif ($refer->errorCauseContains("no such file or directory", "No existe el fichero o el directorio", "cannot find the file")) {
			Logger::MyLog(4, "No existe el archivo [$remotefile] en [$pathcompleto] en el servidor remoto");
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
	Logger::MyLog(2, "Ruta absoluta para variable remote_relative_path convertida a relativa [$rem_relativepath]") if ($rem_relativepath =~ s|^/||);
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
			my $remotefileexists = $ftp->mdtm("$remotefile");
			if ($remotefileexists) {
				Logger::MyLog(4, "Remote file $remotefile (rename from) exists");
				my $newremotefileexists = $ftp->mdtm("$newremotefile");
				if ($newremotefileexists) {
					Logger::MyLog(4, "Remote file $newremotefile (rename to) exists");
					my $newremotefiledelete = $ftp->delete("$newremotefile");
					if ($newremotefiledelete) {
						Logger::MyLog(4, "Remote file $newremotefile (rename to) deleted");
					} else {
						my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
						$error = "FTP delete ha fallado... $ftperrorstring";
						$refer->setErrorCause(40, $error);
						Logger::MyLog(4, "FTP delete para $newremotefile (por renombrado con existencia de destino) ha fallado: ". $ftperrorstring);
					}
				}
				my $okrename = $ftp->rename("$remotefile", "$newremotefile");
				if ($okrename) {
					Logger::MyLog(4, "Remote file $remotefile renamed to $newremotefile");
				} else {
					my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
					$error = "FTP rename ha fallado... $ftperrorstring";
					$refer->setErrorCause(40, $error);
					Logger::MyLog(4, "FTP rename para $remotefile => $newremotefile ha fallado: ". $ftperrorstring);
				}
			} else {
				my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
				Logger::MyLog(4, "mdtm para $remotefile ha fallado: ". $ftperrorstring);
			}

		} else {
			my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
			$error = "FTP cwd ha fallado... $ftperrorstring";
			$refer->setErrorCause(40, $error);
			Logger::MyLog(4, "FTP cwd previo a renombrado de $remotefile => $newremotefile ha fallado: ". $ftperrorstring);
		}
	} else {
		my $cad = "mv $remotefile $newremotefile";
		$error = $refer->execute($cad);
	}

	if (!$error) {
		Logger::MyLog(2, "Archivo [$remotefile] renombrado a [$newremotefile] via $type");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "No existen los permisos adecuados para su borrado en el directorio que lo contiene");
			return 10;
		} elsif ($refer->errorCauseContains("no such file or directory", "No existe el fichero o el directorio", "cannot find the file")) {
			Logger::MyLog(4, "No existe el archivo [$remotefile] en el servidor remoto");
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
	
	# Borramos el origen!
	# my $unlinking = unlink $ori;
	# if ($unlinking == 1) {
	# 	Logger::MyLog(3, "Archivo local $ori eliminado.");
	# } else {
	# 	Logger::MyLog(3, "AVISO: Archivo local $ori no ha podido ser eliminado (devuelto=$unlinking).");
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
			Logger::MyLog(4, "Transferencia FTP NO realizada: $ftperrorstring");
			return $ftp->message;
		}
	} else {
		$refer->setErrorCause(5, "Tipo desconocido en ximcp");
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

	Logger::MyLog(3, "Creando directorio remoto [$rem_relativepath] de forma recursiva en [$rem_basepath]");
	if ($type eq "FTP") {
		my $ftp = $refer->{_ftp};
		# ya estamos en el base
		my $dir = $ftp->mkdir("$rem_relativepath", 1);
		if ($dir) {
			Logger::MyLog(4, "El directorio remoto [$dir] ha sido creado de forma recursiva via FTP");
			return 0;
		} else {
			my $ftperrorstring = "Código: ".$ftp->code." Causa: ".$ftp->message; chomp($ftperrorstring);
			Logger::MyLog(4, "El directorio remoto [$rem_relativepath] NO ha sido creado de forma recursiva via FTP por: $ftperrorstring");

			$refer->setErrorCause(40, "No se ha podido crear el directorio por el servidor remoto ftp de forma recursiva: $ftperrorstring");
			return 40;
		}
	} else {
		my @lista = split("/", $rem_relativepath);
		foreach my $directory (@lista) {
			my $error = $refer->mkdir($basepath, $directory);	
			# error 20, ya existe el directorio no es causa de error...
			$error = 0 if $error == 20;
	
			return $error if $error;
			$basepath = "$basepath/$directory";
		}

		my $error = $refer->checkdir("$rem_basepath/$rem_relativepath");
	
		if ($error) {
			Logger::MyLog(4, "El directorio remoto [$rem_relativepath] no ha sido creado correctamente");
			# PENDING: verificar si ya existe algo diferente en remoto y eliminar.
			return $error;
		} else {
			Logger::MyLog(4, "Verificado directorio remoto [$rem_relativepath] creado");
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
		Logger::MyLog(4, "Existe $remotepath");
		return 0;
	} else {
		if ($refer->errorCauseContains("no such file or directory", "No existe el fichero o el directorio")) {
			Logger::MyLog(4, "No existe la ruta de destino");
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
		Logger::MyLog(4, "Directorio remoto [$directory] creado en [$basepath]");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "No existen los permisos adecuados para crearlo");
			return 10;
		} elsif ($refer->errorCauseContains("File exist", "fichero existe", "fichero ya existe", "archivo ya existe")) {
			Logger::MyLog(4, "El directorio pedido ya existe");
			return 20;
		} elsif ($refer->errorCauseContains("exists but is not a directory", "existe pero no es un directorio")) {
			Logger::MyLog(4, "Existe un elemento no directorio con el mismo nombre");
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
		Logger::MyLog(4, "Borrado directorio remoto [$directory] en [$basepath]");
	} else {
		if ($refer->errorCauseContains("Permission denied", "Permiso denegado")) {
			Logger::MyLog(4, "No existen los permisos adecuados para el borrado");
			return 10;
		} elsif ($refer->errorCauseContains("no such file", "No existe el fichero")) {
			Logger::MyLog(4, "El directorio pedido no existe");
			return 20;
		} elsif ($refer->errorCauseContains("directory not empty", "directorio no est")) { # directorio no vacio
			Logger::MyLog(4, "El directorio contiene elementos");
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

	Logger::MyLog(5, "  Ejecutando $cmd en $remote via $type...");
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
		$refer->setErrorCause(5, "Tipo $type desconocido");
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

	# Ejecutamos comando sobre objeto ftp...
	if ($argument) {
		$output = $ftp->$cmd($argument);
	} else {
		$output = $ftp->$cmd;
	}

	# Chequeo de errores...
	if ($output) {
		Logger::MyLog(5, "  Comando $cmd($argument) via FTP devuelve $output");
		$error = 0;
	} else {
		my $ftperrorstring = "Causa: (".$ftp->code.") ".$ftp->message; chomp($ftperrorstring);
		Logger::MyLog(5, "  Comando $cmd($argument) via FTP devuelve error: $ftperrorstring");
		$output = $ftperrorstring;
		$error = 1;
	}

	return ($error, $output);
}

# Devuelve la cadena resultante o undef si error
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
