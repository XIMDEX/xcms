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


# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #
# Framework: ximDEX v2.5
#
# Module: dexT dynamic
# Author: Juan A. Prieto
# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - #

BEGIN {
        my $script = $0; $script =~ s[/+][/]g;
        my @path = split ("/", $script); pop @path;
        my $path = join("/", @path); $path = "." unless $path;

        $::SCRIPT_PATH = $path;
}
use lib "$::SCRIPT_PATH/../XIMDEXLIB";

use strict;
#use warnings; ###

use ximdexCONFIG;
use DEXT;
#use dexA; # adds .5" in ser1 to compile time!
#use ximIO;  # sub XIMIO_GetSections_ximTREE eliminada en trunk:r[1447]

my $MAPGENCODE_QUOT = 0;

$|=1;

# VERSION
my $dexTdin_version = "2.4 (for xmd 2.5)";

# generic global vars
$DEXT::Deps::REALTIME = time();
$DEXT::Deps::TAGCOUNTER = 0;

# STARTING
my @fechahoy = localtime($DEXT::Deps::REALTIME);
$::fechahoy = sprintf("%02d-%02d-%04d %02d:%02d.%02d", $fechahoy[3], (1+$fechahoy[4]), (1900+$fechahoy[5]), $fechahoy[2], $fechahoy[1], $fechahoy[0]);

my $mylog = "$ximdexCONFIG::XIMDEX_LOG_DIR/dexTdin.log";
my $openlog = 3; # log level for logfile
my $verbose = 3; # log level for stdout

open (MYLOG, ">>$mylog") or $openlog = undef;

MyLog::Log(1, "XIMDEX dexTdin version:$dexTdin_version STARTING...");
MyLog::Log(1, "Library DEXT module version: $DEXT::DEXT_version");

my $continue = 0;   # needed by DEXT module, but here it is not relevant 

# global vars
my ($templateDirectory, $commonDirectory) = (undef, undef);
my ($nodeLevel, $error2stdout, $debugmode) = (undef, 0, 0);
my ($file_in, $file_out) = (undef, undef);
my $NodeId= undef;

# Options processing
foreach (@ARGV) {
	if (my($a, $b) = /^--(\w+)(?:=(.*))?/) {
		#MyLog::Log(3, "FLAG $a --> $b");
		$error2stdout = 1 if $a =~ /^errorout/i;
		$templateDirectory = $b if $a =~ /^template/i;
		$commonDirectory = $b if $a =~ /^common/i;
		$nodeLevel = $b if $a =~ /^depth/i;
		$debugmode = 1 if $a =~ /^debug/i;
		$file_in = $b if $a =~ /^filein/i;
		$file_out = $b if $a =~ /^fileout/i;
		$NodeId = $b if $a =~ /^nodeid/i;
	}
}

MyLog::Log(0, "Syntax: dexTdin.pl --template=template_directory --common=common_template_directory [--nodeid=nodeid] [--debug] [--errorout] [--depth=node_depth] [--file=inputfile]") unless ($templateDirectory && $commonDirectory);
MyLog::Log(4, "Params: template_directory=$templateDirectory, common_template_directory=$commonDirectory, error2stdout=$error2stdout, Node_depth=$nodeLevel, Debug=$debugmode, filein=$file_in, fileout=$file_out, nodeid=$NodeId");
MyLog::Log(0, "Template directory common '$commonDirectory' does not exist") unless -e $commonDirectory;
MyLog::Log(0, "Template directory specific '$templateDirectory' does not exist") unless -e $templateDirectory;

# dynamic template definition for store, dexa and ximio modules
our %DynamicTemplateList = ('dexa:source2xml' => \&DEXA_source2xml, 'dexa:sql2xml' => \&DEXA_sql2xml, 'store:write' => \&DEXT_StackWrite, 'store:read' => \&DEXT_StackRead);

# dexT library configuration
my %config = (	'work' 		=> "",
		'final' 	=> "",
		'templateDir' 	=> $templateDirectory,
		'commonDir' 	=> $commonDirectory,
		'remappings' 	=> undef,
		'urlprefix' 	=> "/",
		'dashConvert' 	=> undef,
		'debugMode' 	=> $debugmode,
		'xhtml' 	=> "1",
		'html' 		=> "0",
		'dependances'   => "0",
		'addlAttrs'	=> \&UnknownAtt,
		'addlTemplates'	=> \%DynamicTemplateList,
	);


MyLog::Log(3,"Starting generation...");

# initializes dexT processor
my $gxml = new DEXT(\%config);

# initializes interchange buffers
my $cad_output = "";
my $cad_input = "";

if ($file_in) {
	open(IFILE, $file_in) or MyLog::Log(0, "File $file_in can not be open"); 

	MyLog::Log(4, "Processing file $file_in"); 
	# read file with XML document to be processed into string 
	$cad_input = join("", <IFILE>);
	close(IFILE);
} else {
	MyLog::Log(4, "Processing standard input"); 
	# read stdin with XML document to be processed into string
	$cad_input = join("", <STDIN>);
}	

# applying quot macro transformation
if ($MAPGENCODE_QUOT) {
  $cad_input =~ s#&quot;#_MAPGENcode_quot_#gm;
  MyLog::Log(3,"Applying transformation of entity quot previous to expat");
}

# Calling generator
my $reteval = eval {
	# direct input/output avoiding dependance control
	$cad_output = $gxml->Process($cad_input, $cad_output);

	## ProcessFile can imply dependance control
	## $gxml->ProcessFile("input.xml", "output.html");
};
MyLog::Log(3,"Done with generation.");

if (defined($reteval)) {
	# XML processing OK
	MyLog::Log(3,"Applying filters...");
	ApplyFilters(\$cad_output);
	MyLog::Log(3,"Done with filters.");
	if ($file_out) {
		open(OFILE, ">$file_out") or MyLog::Log(0, "File $file_out can not be open"); 
		print OFILE $cad_output;
		close(OFILE);
	} else {
		print $cad_output;
	}
	MyLog::Log(4, "Total Nested Tags Seen = $DEXT::Deps::TAGCOUNTER");

        DEXT::Deps::ImprimeDependenciaXML($DEXT::DT_dext_xmlFILE);
	MyLog::Log(1,"Generation OK");
} else {
	# XML processing NOT OK
	MyLog::Log(1,"Generation NOT OK");
	MyLog::Log(2, "   ... because $@");
	if ($error2stdout) {
		print "GENERATION ERROR -->\n";
		print $@;
	}
	close(MYLOG) if defined($openlog);
	exit(10);
}

close(MYLOG) if defined($openlog);
exit (0);

sub DEXT_StackWrite {
	my ($templatename) = shift @_;
	my $varname = &DEXT::Attribute("variable");
	my $value = &DEXT::Attribute("value");

	MyLog::Log(3, "DynamicTemplate $templatename: value [$value], varname [$varname]");
	MyLog::Log(0, "ERROR: variable name needed!") unless ($varname);
	$dexA::STORE{$varname} = $value;
	MyLog::Log(3, "Stored [$value] for variable $varname");
	my $template = "<store:write></store:write>";
	return \$template;
}

sub DEXT_StackRead {
	my ($templatename) = shift @_;
	my $varname = &DEXT::Attribute("variable");
	my $value = &DEXT::Attribute("value");
	my $template = &DEXT::Attribute("template");
	MyLog::Log(3, "DynamicTemplate $templatename: varname [$varname]");

	MyLog::Log(0, "ERROR: variable name needed!") unless ($varname);
	MyLog::Log(3, "Read [$value] from variable $varname!");
	my $value = $dexA::STORE{$varname};
	my $cadena = "<store:read>";
	$cadena   .= "<$template>" if $template;
	$cadena   .= $value;
	$cadena   .= "</$template> " if $template;
	$cadena   .= "</store:read> " if $template;
	return \$cadena;
}


sub DEXA_sql2xml {
	my ($templatename) = shift @_;
	my $dbname =  &DEXT::Attribute("db");
	my $table =  &DEXT::Attribute("table");
	my $field  =  &DEXT::Attribute("field");
	my $condition =  &DEXT::Attribute("condition");

	my $sqltag = &DEXT::Attribute("sqltag");
	my $rowtag = &DEXT::Attribute("rowtag");
	my $celltag = &DEXT::Attribute("celltag");

	MyLog::Log(3, "DynamicTemplate $templatename, dbname $dbname, table $table, condition $condition, sqltag [$sqltag], rowtag [$rowtag], celltag [$celltag]");

	my $template = "<sqlxml>";
	$template   .= "<$sqltag>" if $sqltag;
	$template   .= "\n";
	$template   .= &dexA::OutFromTable($dbname, $table, $field, $condition, $rowtag, $celltag);
	$template   .= "</$sqltag>" if $sqltag;
	$template   .= "\n";
	$template   .= "</sqlxml>";

	return \$template;
}

sub DEXA_source2xml {
	my ($templatename) = shift @_;

	# Buscamos parametro source...
	my $source =  &DEXT::Attribute("source");
	my $condition =  &DEXT::Attribute("condition");

	my $sqltag = &DEXT::Attribute("sqltag");
	my $rowtag = &DEXT::Attribute("rowtag");
	my $celltag = &DEXT::Attribute("celltag");

	MyLog::Log(3, "DynamicTemplate $templatename, source $source, condition $condition, sqltag [$sqltag], rowtag [$rowtag], celltag [$celltag]");

	my $template = "<sqlxml2>";
	$template   .= "<$sqltag>" if $sqltag;
	$template   .= "\n";
	$template   .= &dexA::OutFromSource($source, $condition, $rowtag, $celltag);
	$template   .= "</$sqltag>" if $sqltag;
	$template   .= "\n";
	$template   .= "</sqlxml2>";

	#OBSOLETO $template=DEXT::SubstituteAttributes($template, "%%%");
	return \$template;
}

sub UnknownAtt {
	my $attr = shift;
	my $value = undef;

	if ($attr =~ /DEXT_TAGID/) {
		$value = $DEXT::Deps::TAGCOUNTER;
		$value = "$NodeId-$value" if $NodeId;
	} elsif ($attr =~ /storeread-(\w+)/) {
		my $varname = $1;
		$value = $dexA::STORE{$varname} if $varname;
	} elsif ($attr =~ /DEXT_TIME/) {
		$value = time()-$DEXT::Deps::REALTIME;
	} elsif ($attr =~ /DEXT_CPU/) {
		$value = times();
	}

	return $value;
}


sub ApplyFilters {
	my $refcadout = shift ;
	if (defined($nodeLevel)) {
		my $cad = "../"x($nodeLevel);
		$$refcadout =~ s#DOTDOT/#$cad#gm;
		MyLog::Log(4,"DOTDOT filter applied.");
	}
	$$refcadout =~ s#_MAPGENcode_(\w+?)_#&$1;#gm;
	MyLog::Log(4,"Encoded entities translator applied.");

        my $com1 = qr/<!--.*?-->/mo;
        my $com2 = qr/\/\*.*?\*\//so;
        #my $blan = qr/^\s*\r*\n/mo; # redundant with next line
        my $blan = qr/^\s*\n/mo;

	$$refcadout =~ s/$com1//g;
	MyLog::Log(4,"Comment remover1 applied.");
	$$refcadout =~ s/$com2//g;
	MyLog::Log(4,"Comment remover2 applied.");
	$$refcadout =~ s/$blan//g;
	MyLog::Log(4,"Blank filter applied.");
}

# provides MyLog service as specific XIMDEX environment to DEXT.pm
package MyLog;
sub Log {
        my ($level, $sentence) = @_;
        my $mensaje = "dexTdin ($level): $sentence\n";

	if (defined($openlog) && ($level <= $openlog)) {
        	my $mensaje = "dexTdin:$$ $::fechahoy ($level) --> $sentence\n";
		print ::MYLOG $mensaje;
	}

        if ($level > 0) {
                print STDERR $mensaje;
        } else { # condición <=0
                if ($level < 0 && $continue) {
                        print STDERR "\nWARNING: $mensaje\n";
                } else {
                        print STDERR "\nERROR: $mensaje\n";
			if ($error2stdout) {
				print "CONFIGURATION ERROR -->\n$sentence\n";
			}
			close(::MYLOG) if defined($openlog);
			exit(20);
                }
        }
}

1;

