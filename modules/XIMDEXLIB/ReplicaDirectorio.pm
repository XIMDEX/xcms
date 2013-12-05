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
package ReplicaDirectorio;

my $DEBUG = 0;

# Global variables
my @tareas=();
my ($directorio_origen, $directorio_destino, $verbose);

# Main function
sub Replica {
	($directorio_origen, $directorio_destino, $verbose) = @_;
	$verbose ||= 5;
	::MyLog(3, "$0 --> Parameters: origen=$directorio_origen, destino=$directorio_destino, verbose=$verbose");
	
	-d $directorio_origen or ::MyLog(0, "Source directory [$directorio_origen] does not exits", "RepliDir");
	
	# main
	CreaTareaDeReplica($directorio_origen, $directorio_destino);
	ReplicaDirectorioRecursivamente();
} 

# subrutinas...
sub ReplicaDirectorioRecursivamente {
	while ( my $tarea = ExtraeTareaDeReplica() ) {
		my ($dir_ori, $dir_des) = @$tarea;
		my @nuevosdires = ProcesaDirectorio($dir_ori, $dir_des);
		foreach my $dire (@nuevosdires) {
			my $ori = "$dir_ori/$dire";
			my $des = "$dir_des/$dire";
			CreaTareaDeReplica($ori, $des);
		}
	}
}

sub CreaTareaDeReplica {
	my ($ori, $des) = @_;
	my $tarea = [$ori, $des];
	push @tareas, $tarea;
}

sub ExtraeTareaDeReplica {
	my $tarea = shift @tareas;
	return $tarea;
}

sub ProcesaDirectorio {
	my ($directorio_origen, $directorio_destino) = @_;

        ::MyLog(2, "Comparing directory [$directorio_origen] with [$directorio_destino]", "RepliDir");

	-d $directorio_origen or ::MyLog(0, "Source directory [$directorio_origen] does not exist", "RepliDir");

	unless (-d $directorio_destino) {
		mkdir $directorio_destino or ::MyLog(0, "IMPOSIBLE", "RepliDir");
        	::MyLog(3, "Creating destination directory [$directorio_destino]", "RepliDir");
	}
	#-d $directorio_destino or ::MyLog(0, "Directorio destino [$directorio_destino] no existe", "RepliDir");

        # Reading source directory
	my $origen = new Directorio($directorio_origen, ".") or ::MyLog(0, "It is not a directory", "RepliDir");
	$origen->ImprimeInfo() if $DEBUG;
        # Reading destination directory
	my $destino = new Directorio($directorio_destino, ".") or ::MyLog(0, "It is not a directory", "RepliDir");
	$destino->ImprimeInfo() if $DEBUG;

	# First it deletes what is left in destination because of it does not exist in the source...
	$destino->eliminaElementos($origen);

	# Now creates and copies 
	$origen->replicaArchivos($destino);

	# and extracts list of directories to create recursively tasks...
	my @directorios = $origen->listaDirectorios("nombres");
	return @directorios;
}


# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# --------------------------------------------------------------------------#
#                                                                           #
# (C) Open Ximdex Evolution SL 2003 # www.ximdex.com info@ximdex.com #
#                                                                           #
# --------------------------------------------------------------------------#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

package Archivo;

sub new {
	my $proto = shift;
	my $clase = ref($proto) || $proto;
	my $refer = {};

	$refer->{_nombre} = shift;
	$refer->{_ruta} = shift;

	my $archi = "$refer->{_ruta}/$refer->{_nombre}";

	if (-l $archi) {
		$refer->{_tipo} = "l";
		my @stat = lstat($archi);
		#print "LSTAT $archi @stat\n";
		$refer->{_stat} = \@stat;
	} else {
		my @stat = stat($archi);
		#print "STAT $archi @stat\n";
		$refer->{_stat} = \@stat;
		$refer->{_tipo} = "d" if -d _;
		$refer->{_tipo} = "f" if -f _;
	}	

	return bless $refer, $clase;
}

sub ImprimeInfo {
	my $refer = shift;

	print "ARCHIVO: $refer->{_nombre}\n";
	print "A Ruta: $refer->{_ruta}, Tipo $refer->{_tipo}\n"; 
	print "Info: @{$refer->{_stat}}\n"
}

sub fecha {
	my $refer = shift;

	my $fecha = $refer->{_stat}[9];
	::MyLog(5, "FECHA ".$refer->rutaCompleta." es $fecha", "RepliDir");
	return $fecha;
}

sub rutaCompleta {
	my $refer = shift;
	return "$refer->{_ruta}/$refer->{_nombre}";
}

sub tipo {
	my $refer = shift;
	return $refer->{_tipo};
}

sub nombre {
	my $refer = shift;
	return $refer->{_nombre};
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# --------------------------------------------------------------------------#
#                                                                           #
# (C) Open Ximdex Evolution SL 2003 # www.ximdex.com ximdex@ximdex.com #
#                                                                           #
# --------------------------------------------------------------------------#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

package Directorio;

sub new {
	my $proto = shift;
	my $clase = ref($proto) || $proto;
	my $refer = {};

	my $nombre_completo = shift;
	$nombre_completo =~ s|/+|/|g; #eliminamos slashes seguidos

	my @comps = split("/", $nombre_completo);
	my $nombre = pop @comps;
	my $ruta = join("/", @comps);
	$ruta = "." unless $ruta;
	
	#my $nombre = shift;
	#my $ruta = shift;
	#my $nombre_completo = "$ruta/$nombre";

	$refer->{_ruta} = $ruta;
	$refer->{_nombre} = $nombre;

	$refer->{_propiedades} = undef;
	$refer->{_archivos} = undef;
	$refer->{_directorios} = undef;
	$refer->{_existe} = 0;

	if (-e $ruta) {
		if (-d $ruta) {
			$refer->{_propiedades} = new Archivo($refer->{_nombre}, $refer->{_ruta});
			$refer->{_existe} = "1";
			($refer->{_archivos}, $refer->{_directorios}) = CargaDatosArchivos($nombre_completo);
		} else {
			return 0;
		}
	} 

	return bless $refer, $clase;
}

sub rutaCompleta {
	my $refdir = shift;
	return "$refdir->{_ruta}/$refdir->{_nombre}";
}

sub CargaDatosArchivos {
	my $ruta = shift;
	opendir(DIR, $ruta) or ::MyLog(0, "It could not be processed [$ruta]", "RepliDir");

	my %archivos = ();
	my %directorios = ();

	while (defined (my $archivo =readdir(DIR))) {
                if ($archivo ne "." and $archivo ne "..") {
			my $archiref = new Archivo($archivo, $ruta);
			if ($archiref->tipo eq "d") {
				$directorios{$archivo} = $archiref;
				#print "ALMACENADO DIR $archiref\n";
			} else {
				$archivos{$archivo} = $archiref;
				#print "ALMACENADO FIL $archiref como $archivo\n";
			}
		}
	}
	closedir(DIR);
	return (\%archivos, \%directorios);
}

sub ImprimeInfo {
	my $refdir = shift;

	print "DIRECTORIO: $refdir->{_nombre}, ruta: $refdir->{_ruta}, existe: $refdir->{_existe}\n";
	#print "D Info: \n".$refdir->{_propiedades}->ImprimeInfo."\n";
	my @archis = keys(%{$refdir->{_archivos}});
	print "Archivos: @archis \n";
	my @dires = keys(%{$refdir->{_directorios}});
	print "Directorios: @dires \n";
	print "\n";
}

sub listaArchivos {
	my $refdir = shift;
	my $datos = shift @_;

	if ($datos =~ /ref/i) {
		return values(%{$refdir->{_archivos}});
	} else {
		return keys(%{$refdir->{_archivos}});
	}

}


sub listaDirectorios {
	my $refdir = shift;
	my $datos = shift @_;

	if ($datos =~ /ref/i) {
		return values(%{$refdir->{_directorios}});
	} else {
		return keys(%{$refdir->{_directorios}});
	}

}

sub eliminaElementos {
	my $destino = shift;
	my $origen = shift;

	my $directorio_origen = $origen->rutaCompleta;
	my $directorio_destino = $destino->rutaCompleta;

        foreach my $refarchivo ($destino->listaArchivos("referencia")) {
		$destino->borraElemento($refarchivo->nombre) unless ($origen->existeArchivo($refarchivo->nombre) or $origen->existeDirectorio($refarchivo->nombre));
	}
        foreach my $refarchivo ($destino->listaDirectorios("referencia")) {
		$destino->borraElemento($refarchivo->nombre) unless ($origen->existeArchivo($refarchivo->nombre) or $origen->existeDirectorio($refarchivo->nombre));
	}
}

sub replicaArchivos {
	my $origen = shift;
	my $destino = shift;

	my $directorio_origen = $origen->rutaCompleta;
	my $directorio_destino = $destino->rutaCompleta;

        foreach my $refarchivo ($origen->listaArchivos("referencia")) {
                $refarchivo->ImprimeInfo() if $DEBUG;
                ::MyLog(3, "Processing file '".$refarchivo->nombre."' from [$directorio_origen] to [$directorio_destino]...", "RepliDir");

		my $condicion = 0;
		if (my $desfile = $destino->existeArchivo($refarchivo->nombre)){
			if ($desfile->fecha < $refarchivo->fecha) {
				$condicion++;
				::MyLog(5, "Destination file is younger than source file", "RepliDir");
			}
			if ($destino->existeArchivo($refarchivo->nombre)->tipo ne $refarchivo->tipo) {
			$condicion++;
			::MyLog(5, "Different types detected", "RepliDir");
			}
		} else {
			$condicion++;
		}

		$destino->copiaArchivo($refarchivo) if $condicion; 
        }
}

sub copiaArchivo {
	my $destino = shift;
	my $refarchivo = shift;

	# If there is something previous in destination with same name, we delete it
	$destino->borraElemento($refarchivo->nombre) if $destino->existeElemento($refarchivo->nombre);

	# Copying file
	my $ruta1 = $refarchivo->rutaCompleta;
	my $ruta2 = $destino->rutaCompleta;
	if ($refarchivo->tipo eq "l") {
		# If it is a symbolic link, we recreate it
		my $apunta = readlink($ruta1 = $refarchivo->rutaCompleta);
		my $nombre = "$ruta2/".$refarchivo->nombre;
		symlink($apunta, $nombre) or ::MyLog(1, "Error recreating symbolic link '$ruta1' pointing to '$apunta' in [$ruta2]", "RepliDir");
		::MyLog(1, "Copying simbolic link [$ruta1] which point to  $apunta to [$ruta2]", "RepliDir");

	} else {
	        ::copy($ruta1, $ruta2) or ::MyLog(0, "File can be copied in [$ruta1] in directory [$ruta2]", "RepliDir");         
		::MyLog(1, "Copying file [$ruta1] a [$ruta2]", "RepliDir");
	}
}

sub existeArchivo {
	my $refdir = shift;
	my $nombre = shift;

	return $refdir->{_archivos}{$nombre};
}

sub existeDirectorio {
	my $refdir = shift;
	my $nombre = shift;

	return $refdir->{_directorios}{$nombre};
}

sub existeElemento {
	my $refdir = shift;
	my $nombre = shift;
	return $refdir->{_archivos}{$nombre} if $refdir->{_archivos}{$nombre};
	return $refdir->{_directorios}{$nombre};
}

sub borraElemento {
	my $refdir = shift;
	my $nombre = shift;

	$nombre = $refdir->rutaCompleta."/$nombre";
	if (-d $nombre) {
		::rmtree($nombre, 0, 1);
		::MyLog(4, "Deleting directory '$nombre' en [".$refdir->rutaCompleta."]", "RepliDir");
	} else {
		::MyLog(4, "Deleting file '$nombre' en [".$refdir->rutaCompleta."]", "RepliDir");
		unlink $nombre;
	}
}

1;

