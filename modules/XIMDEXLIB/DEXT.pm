 
 
# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
#
# # Framework: ximDEX v2.5
# #
# # Module: dexT,  version: 2.51
# # Author: Juan A. Prieto, Josh Carter
# # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
#

# DEXT module
#
# (C) 2002 Juan A. Prieto, juanpri@ximdex.com
#
# dexT module is an enhaced version of GXML by Josh Carter
#
# GXML module: generic, template-based XML transformation tool
# Copyright (C) 1999-2001 Josh Carter <josh@multipart-mixed.com>
# All rights reserved.
#
# This program is free software; you can redistribute it and/or modify
# it under the same terms as Perl itself.

package DEXT;

# 'strict' turned off for release, but stays on during development.
# use strict;
use Cwd;
use XML::Parser;

# NAMESPACE DECLARATION!
$dext_NSlabel = "dext";

# Most of these vars are used as locals during parsing.
use vars ('$VERSION','@attrStack','$output','$baseTag','$rPreserve','$self');

$VERSION = 2.50; # started on GXML module 2.2
$DEXT_version = "dev. 1.9 [namespace='$dext_NSlabel']";

$DT_dext_xmlFILE = "";
%DT_dext_templatepath = ();

$ELSE_param = undef;

my $debugMode = undef;

#######################################################################
# new, destroy, other initialization and attributes
#######################################################################

sub new
{
	my ($pkg, $rParams) = @_;

	$debugMode = $rParams->{'debugMode'} unless defined($debugMode);
	DEXT::Util::Log("DEXTmod: DEXT MODULE $DEXT_version LOG STARTING ...");
	my $dex_localizacion = `pwd`; chomp($dex_localizacion);

	DEXT::Util::Log("DEXTmod: Node-->$dex_localizacion");

	my $dex_fecha = `date`; chomp($dex_fecha);
	DEXT::Util::Log("DEXTmod: Date-->$dex_fecha");

	my $templateDir = ($rParams->{'templateDir'} || 'templates');
	# juanpri 20020627
	my $commonDir = ($rParams->{'commonDir'} || 'common');
	my $templateMgr = new DEXT::TemplateManager($templateDir, 
							$rParams->{'addlTemplates'},
							$rParams->{'addlTemplate'},
							$rParams->{'addlTempExists'}, $commonDir);

	
	# Create the new beast
	my $self = bless
	{
		_templateMgr => $templateMgr,
		_remappings  => ($rParams->{'remappings'}  || { }),
		_htmlMode    => ($rParams->{'html'}        || 0),
		_xhtmlMode   => ($rParams->{'xhtml'}       || 0),
		_dashConvert => ($rParams->{'dashConvert'} || 0),
		_addlAttrs   => ($rParams->{'addlAttrs'}   || undef),
	}, $pkg;

	$self->AddCallbacks($rParams->{'callbacks'});

	return $self;
}

sub DESTROY
{
	# nothing needed for now
}

#
# AddCallbacks
#
# Callbacks allow you to be notified at the start or end of a given
# tag. Pass in a hash of tag names to subroutine refs. Tag names
# should be prefixed with "start:" or "end:" to specify where the
# callback should take place. See docs for more info on using
# callbacks.
#
sub AddCallbacks
{
	my ($self, $rCallbacks) = @_;
	my (%start, %end);

	# add our default commands
	%start = (	"${dext_NSlabel}:foreach"  	=> \&ForEachStart,);
	%end = (	"${dext_NSlabel}:ifexists" 	=> \&ExistsCommand,
			"${dext_NSlabel}:ifcondition" 	=> \&ConditionCommand,
			"${dext_NSlabel}:else" 		=> \&ElseCommand,
			"${dext_NSlabel}:import" 	=> \&IncludeCommandEnd,
			"${dext_NSlabel}:runme" 	=> \&RunmeCommandEnd,
			"${dext_NSlabel}:ifcontains" 	=> \&ContainsCommandEnd,
			"${dext_NSlabel}:ifequals" 	=> \&EqualsCommand,
			"${dext_NSlabel}:foreach"  	=> \&ForEachEnd,
		);

	# and add the stuff passed in, if anything
	foreach my $callback (keys %{$rCallbacks})
	{
		if ($callback =~ /^start:(.*)/)
		{
			$start{$1} = $rCallbacks->{$callback};
			DEXT::Util::Log("adding start callback $1");
		}
		elsif ($callback =~ /^end:(.*)/)
		{
			$end{$1} = $rCallbacks->{$callback};
			DEXT::Util::Log("adding end callback $1");
		}
		else
		{
			DEXT::Util::Log("unknown callback type $callback");
		}
	}

	$self->{'_cb-start'} = \%start;
	$self->{'_cb-end'}   = \%end;
}

#######################################################################
# Process, ProcessFile
#######################################################################

#
# Process
#
# Processes a given XML string. Returns the output as a scalar.
#
sub Process()
{
	my ($selfParam, $stuff) = @_;
	
	# Set up these pseudo-global vars
	local (@attrStack, $output, $baseTag, $rPreserve);

	# Also create this so XML::Parser handlers can see it
	local $self = $selfParam;

	# See note in LoadTemplate about this
	$stuff =~ s/%%%/::VAR::/g;

	$DEXT::DT_dext_xmlFILE = "ONLINE";
#	DEXT::Deps::AlmacenaDependenciaXML($DEXT::DT_dext_xmlFILE, "nodeid_docxap");

	# Process the beastie
	my $xp = new XML::Parser(ErrorContext => 2);
	$xp->setHandlers(Char		=> \&HandleChar,
					 Start		=> \&HandleStart,
					 End		=> \&HandleEnd,
					 Comment	=> \&HandleComment,
					 Default	=> \&HandleDefault);

	$xp->parse($stuff);

	return $output;
}

#
# ProcessFile
#
# Processes a given XML file. If an output file name is provided, the
# result will be dumped into there. Otherwise it will return the
# output as a scalar.
#
sub ProcessFile()
{
	my ($selfParam, $source, $dest) = @_;
	my $fileName;
	my $baseDir = getcwd();
	
	# Set up these pseudo-global vars
	local (@attrStack, $output, $baseTag, $rPreserve);

	# Also create this so XML::Parser handlers can see it
	local $self = $selfParam;

	#
	# Open and parse the input file.
	#
	$fileName = DEXT::Util::ChangeToDirectory($source);
	$DEXT::DT_dext_xmlFILE = $dest;
	DEXT::Deps::AlmacenaDependenciaXML($DEXT::DT_dext_xmlFILE, $source);
	
	open(IN, $fileName) || MyLog::Log(0, "open input $fileName: $!");

	# Slurp everything
	local $/;
	undef $/;	# turn on slurp mode
	my $file = <IN>;
	
	close(IN);
	chdir($baseDir);

	# See note in LoadTemplate about this
	$file =~ s/%%%/::VAR::/g;

	# Process the beastie
	my $xp = new XML::Parser(ErrorContext => 2);
	$xp->setHandlers(Char		=> \&HandleChar,
					 Start		=> \&HandleStart,
					 End		=> \&HandleEnd,
					 Comment	=> \&HandleComment,
					 Default	=> \&HandleDefault);

	$xp->parse($file);

	return $output unless ($dest);

	#
	# Find and open the output file.
	#
	chdir($baseDir);
	$fileName = DEXT::Util::ChangeToDirectory($dest);

	open(OUT, ">$fileName") || MyLog::Log(0, "open output $fileName: $!");
	
	# Ensure the permissions are correct on the output file.
	# juanpri 20020708
	#my $cnt = chmod 0745, $fileName;
	my $cnt = chmod 0660, $fileName;
	warn "chmod failed on $fileName: $!" unless $cnt;

	# Print the results
	print OUT $output;

	close(OUT);
	chdir($baseDir);
}

#######################################################################
# XML parser callbacks
#######################################################################

#
# HandleStart
#
# Create a new attribute frame for this entity and fill it with the
# entity's attributes, if any. Nothing is printed to $output just yet;
# that comes in HandleEnd.
#
sub HandleStart()
{
	my ($xp, $element, %attrs) = @_;
	my ($key, %cbParams);

	# First entity in the document is always the base
	$baseTag = $element unless defined($baseTag);
	
	$DEXT::Deps::TAGCOUNTER++;

	DEXT::Util::Log("start: $element");

	foreach $key (keys %attrs)
	{
		my $val = $attrs{$key};

		# Compact whitespace, strip leading and trailing ws.
		$val =~ s/\s+/ /g;
		$val =~ s/^\s*(.*?)\s*$/$1/;

		# Make variable substitutions in each key.
		$val = SubstituteAttributes($val);

		# Stick newly molested $val back into $attrs if we've still
		# got something left, or delete the attribute if it's empty
		# (could have been made empty after substitition).
		if (length($val))
			{ $attrs{$key} = $val; }
		else
			{ delete $attrs{$key}; }

		DEXT::Util::Log("\t$key: $val");
	}

	# Save our tag name in the attrs, too.
	$attrs{_TAG_} = $element;

	# Add these attributes to the master tree.
	AddAttributeNode($element, \%attrs);

	# Call registered callback, if one exists
	if ($self->{'_cb-start'}->{$element})
	{
		&{$self->{'_cb-start'}->{$element}}(\%cbParams);
	}
}

#
# HandleEnd
#
# By now we should have stuff in the current attribute frame's _BODY_
# special attribute, assuming there was char data. We need to either
# run a substitution for the tag, or just echo the _BODY_ framed by
# opening and closing tags. If there was no char data and this isn't a
# templated entity, just echo <tag> (HTML syntax).
#
sub HandleEnd()
{
	my ($xp, $element) = @_;
	my $orig = $element;
	my $html = ($self->{_htmlMode} ne 0);
	my $xhtml = ($self->{_xhtmlMode} ne 0);

	my ($rActions, $discard, $repeat, $strip, $import, $runme);
	my %cbParams;

	DEXT::Util::Log("end: $element");

	# Get the attribute frame for this entity, and also the next one up.
	my $attrsRef  = $attrStack[-1];
	my $nextFrame = $attrStack[-2];
	my $destRef   = undef;

	# Our entity's tag may be a variable. Substitute now.
	$element = SubstituteAttributes($element);

	#
	# If the element should be remapped into something else, do that
	# now. NOTE: this means that an untemplatted tag can be remapped
	# into a templatted one, and the template *will* be applied.
	#
	if (exists $self->{'_remappings'}->{$element})
	{
		$element = $self->{'_remappings'}->{$element};
	}

	# Bail if the tag was substituted/remapped to nothing
	if (!length($element))
	{
		DEXT::Util::Log("discarding $orig because it's remapped to nil");
		LeaveAttributeNode();
		return;
	}

repeat:

	# Call callback if needed
	if ($self->{'_cb-end'}->{$element})
	{
		$rActions = &{$self->{'_cb-end'}->{$element}}(\%cbParams);

		# Make sure these are clear, since a previous 'repeat' may
		# have changed them.
		undef $runme; undef $import; undef $discard; undef $repeat; undef $strip;

		if (defined($rActions) && ref($rActions) eq 'ARRAY')
		{
			foreach my $action (@$rActions)
			{
				if    ($action eq 'discard')  { $discard = 1; }
				elsif ($action eq 'repeat')   { $repeat  = 1; }
				elsif ($action eq 'striptag') { $strip   = 1; }
				elsif ($action eq 'import')   { $import  = 1; }
				elsif ($action eq 'runme')    { $runme   = 1; }
			}
		}
	}

	if ($discard)
	{
		DEXT::Util::Log("discarding $orig because callback told me to");
		LeaveAttributeNode();
		return;
	}

	if ($repeat)
	{
		#
		# This requires some explanation. The gxml:foreach start
		# callback (assuming we're repeating because of foreach) would
		# have set aside its 'expr' variable as something HandleChar
		# shouldn't substitute. If that happened, each iteration would
		# have the same expr value -- it would just sub the first
		# value in there, and then the variable wouldn't exist
		# anymore. Thus it must be saved until here and substituted.
		#
		# First step: fetch the original body which has the SAVE
		# marker preserved. If this is the first pass through, grab it
		# from the attrs.
		#
		my $body = $cbParams{'body'};

		if (!defined($body))
		{
			$body = $attrsRef->{_BODY_};
			$cbParams{"body"} = $body;
		}

		# Figure out what we were saving
		my $var = $cbParams{'expr'} || Attribute('expr');

		# Now sub back the VAR marker and sub in the attribute
		$body =~ s/::SAVE::(${var}:?.*?)::SAVE::/::VAR::$1::VAR::/g;
		$body = SubstituteAttributes($body);

		# Finally, refresh this for later code
		$attrsRef->{_BODY_} = $body;
		#DEXT::Util::Log("gxml:import body $body ");

	}

	#
	# If there's a frame above us and we're not the document's base
	# entity, we want to proceed normally. All output should go into
	# the _BODY_ attribute of the frame above us. Otherwise we want to
	# dump the current _BODY_ to $output and return.
	#
	if ((defined $nextFrame) && ($baseTag ne $element))
	{
		$destRef = \$nextFrame->{_BODY_};
		#DEXT::Util::Log("DT_dext_BUG pos0 $element");
	}
	else
	{
		# Special case for the very top-level entity: if the beast has
		# a template, substitute it and dump the output directory into
		# $output, since there's no upper-level _BODY_ for it.
		if (!defined $nextFrame && $self->TemplateExists($element))
		{
			$output .= $self->SubstituteTemplate($element);
			DEXT::Deps::AlmacenaDependenciaXML($DEXT::DT_dext_xmlFILE, "$DEXT::DT_dext_templatepath{$element}/$element.xml");
		}
		elsif (defined ($attrsRef->{_BODY_}))
		{
			# Otherwise just dump to $output. NOTE: this case also
			# applies to the base tag of templates.

			$output .= $attrsRef->{_BODY_};
		}
		
		# 20040118 it removes tags with template names...
		if (!$html and !$xhtml)
		{
			$output = "<$element>$output</$element>";
		}

		LeaveAttributeNode();
		return;
	}

	if ($self->TemplateExists($element))
	{
		#
		# There's a template for this entity, so we need to 
		# substitute it in.
		#
		DEXT::Util::Log("found template for $element");
		 
		# Almacenamos dependencia del template (posiblemente redundante)
		DEXT::Deps::AlmacenaDependenciaXML($DEXT::DT_dext_xmlFILE, "$DEXT::DT_dext_templatepath{$element}/$element.xml");

		my $substitution = $self->SubstituteTemplate($element);
		$$destRef .= $substitution if defined($substitution);
		
		# Update our _BODY_ to reflect the new substitution.
		$attrsRef->{_BODY_} = $substitution;
	}
	elsif ($strip)
	{
		#
		# If a callback said to strip its tag off the output, just
		# echo our body without a tag wrapped around it.
		#
		$$destRef .= $attrsRef->{_BODY_} if defined($attrsRef->{_BODY_});
		#DEXT::Util::Log("gxml:import destRef3 $$destRef");
	}
	elsif ($import) {
		my $import_error = 0;
		DEXT::Util::Log("gxml:import element: $element");
		my $attrsRef = $attrStack[-1];
		#DEXT::Util::Log("gxml:import file $$attrsRef{file}");
		if ($element eq "${dext_NSlabel}:import" && $$attrsRef{file}) {
		  my $myincfile = $$attrsRef{file};
		  MyLog::Log(0, "gxml:import --> file $myincfile is a directory") if -d $myincfile;

		  DEXT::Util::Log("gxml:import including file $myincfile");

		  # Sustitucion usando eval perl
		  my $plsubs = $$attrsRef{evalexp};
		  DEXT::Util::Log("gxml:import evalexp definition: $plsubs") if $plsubs;
		  # Sustitucion usando par de variable valor 
		  my $dovar = $$attrsRef{replace};
		  my ($v1, $v2) = (undef, undef);
		  if ($dovar) {
		    DEXT::Util::Log("gxml:import replace definition: $dovar");
		    ($v1, $v2) = split(/=/, $dovar);
	  	  }

		  if (open(MYINCF, $myincfile)) {
			foreach (<MYINCF>) {
			  $plsubs && eval($plsubs) && 
			    DEXT::Util::Log("gxml:import applied evalexp $plsubs");
			  $dovar && $v1 && defined($v2) && s/$v1/$v2/g && 
			    DEXT::Util::Log("gxml:import applied replace $v1=$v2");
	  	    	  $$destRef .= $_;
		        }
			close(MYINCF);
			DEXT::Util::Log("gxml:import --> $myincfile file included.");
			# Almacenamos dependencia del import
			DEXT::Deps::AlmacenaDependenciaXML($DEXT::DT_dext_xmlFILE, $myincfile);
		  } else {
			DEXT::Util::Log("gxml:file --> couldn't open gxml:import file '$$attrsRef{file}' --> $!");
			$import_error = 1;
		  }
		} else {
		  DEXT::Util::Log("gxml:import --> No reference");
		  $import_error = 1;
		}
		
		#DEXT::Util::Log("gxml:import destRef9 $$destRef");
		MyLog::Log(0, "gxml:import --> file '$$attrsRef{file}': $!") if $import_error;

	} elsif ($runme) {
		my $attrsRef = $attrStack[-1];
		if ($element eq "${dext_NSlabel}:runme" && $$attrsRef{command}) {
		  my $mycommand = $$attrsRef{command};
		  my @mycommandwords = split(/\s+/, $mycommand);

		  MyLog::Log(0, "gxml:runme --> $mycommandwords[0] can not be executed by you (remember to include the full path)") unless -X $mycommandwords[0];
		  DEXT::Util::Log("gxml:runme executing $mycommand");
	  	  $$destRef .= `$mycommand`;
		} else {
		  MyLog::Log(0, "gxml:runme --> No reference: $!");
		}
	} else {
		#
		# No template, so just echo the tag and relevant _BODY_ in XML
		# syntax (i.e. <tag/> single-tag entity syntax), unless $html
		# is set, in which case we want it in HTML syntax.
		#

		# Grab a reference to _only_ the attributes in our
		# current frame. 
		my $attrsRef = $attrStack[-1];

		# If the tag has an explicit 'html:' namespace prefix, strip
		# that if we're in HTML mode.
		# 20040118 xhtml mode also removes html label
		$element =~ s/^html:// if ($html or $xhtml);
		
		# Print the tag.
		$$destRef .= '<' . $element;

		# Print the attibute list for this (and only this) entity.
		foreach my $key (keys %$attrsRef)
		{
			next if $key =~ /^_[-_A-Z]+_$/; # skip special variables

			my $cleankey = $key;
			$cleankey =~ s/^html:// if ($html or $xhtml);
			
			$$destRef .= " $cleankey=\"" . $attrsRef->{$key} . "\"";
		}

		#
		# If there's character data (i.e. this is not a single-tag
		# entity), print that data and a closing tag.
		#
		if (defined($attrsRef->{_BODY_}) && length($attrsRef->{_BODY_}) > 0) # josh fixed without >0 at 2.4 version!!!
		{
			# Close the opening tag
			$$destRef .= '>';

			$$destRef .= $attrsRef->{_BODY_};

			$$destRef .= '</' . $element . '>';
			#DEXT::Util::Log("DT_dext_BUG pos1 $element");
			#DEXT::Util::Log("gxml:import destRef2 $$destRef");
		}
		# 20040118-c in html mode <a href="pe" /> can not exist
		elsif ($html)
		{
			if (defined($attrsRef->{_BODY_})) {
				# juanpri 20020604 BUG2 
				# Close the opening tag
				$$destRef .= '>';
				$$destRef .= $attrsRef->{_BODY_};
				$$destRef .= '</' . $element . '>';
			} else {
				#DEXT::Util::Log("DT_dext_BUG pos2 $element");
				# Single-tag entity, but in HTML <tag> mode.
				$$destRef .= '>';
				
				# juanpri 20020702 BUG3
				# BUG BODYs vacios en tag dobles html
				if ($element eq "a") {
				  $$destRef .= '</' . $element . '>';
				}
			}
		}
		else
		{
			# Single-tag entity, and we're just doing a generic
			# XML->XML conversion, so preserve <tag/> syntax.
			# 20050302 JAP
			# XHTML needs a blank space before closing
			$$destRef .= ' />';
		}
	}

	if ($repeat)
	{
		# Callback will be called again at top of this loop.
		goto repeat;
	}

	LeaveAttributeNode();
}

#
# HandleChar
#
# Substitute any attributes which show up in our input string, and
# append the resulting string to the last attr frame's _BODY_ attr.
#
sub HandleChar()
{
	my ($xp, $string) = @_;

	# Achtung! We must process the original string, not the one
	# munged by Expat into UTF-8, which will automatically remap
	# things like "&lt;" into "<". If the author wrote &lt; in their
	# XML document, that's probably because they wanted it in their
	# HTML document, too.
	$string = $xp->original_string;

	# Change the marker for variables which we don't want substituted.
	foreach my $var (@$rPreserve)
	{
		$string =~ s/::VAR::(${var}:?.*?)::VAR::/::SAVE::$1::SAVE::/g;
	}

	# Make variable substitutions.
	$string = SubstituteAttributes($string);

	# Convert m-dashes if needed.
	$string =~ s/--/&\#8212;/g if $self->{_dashConvert};

	# Append the body text to the _BODY_ attribute of the last
	# attribute frame on the stack (i.e. that of the most immediately
	# enclosing entity).
	$attrStack[-1]->{_BODY_} .= $string;
}

#
# HandleComment
#
# Stick comments in the _BODY_ attr, too. Also supports attribute
# substitution.
#
sub HandleComment()
{
	my ($xp, $string) = @_;

	# Make variable substitutions.
	$string = SubstituteAttributes($string);

	# Append the text to the _BODY_ attribute of the last attribute
	# frame on the stack. Remember to put the comment markers back in!
	$attrStack[-1]->{_BODY_} .= '<!--' . $string . '-->';
}

#
# HandleDefault
#
# Discard all the other stuff which we may encounter.
#
sub HandleDefault()
{
	my ($xp, $string) = @_;

	# Discard stuff for now.
}



#######################################################################
# Attribute tree maintenance
#######################################################################



#
# AddAttributeNode
#
# Add a node to the document tree. The node's contents are the
# attributes of that element, both in the tag and the body (via the
# _BODY_ attr). This should be called in HandleStart, and paired with
# LeaveAttributeNode in HandleEnd.
#
sub AddAttributeNode
{
	my ($tag, $attrsRef) = @_;
	my ($parent);

	# Get our parent if there is one. This will be the last thing
	# on the stack, as we haven't added ourself yet.
	if (defined $attrStack[-1])
	{ 
		$parent = $attrStack[-1]; 
	}
	else
	{
		# No parent means we're the top-level element, so just add
		# ourself to the stack and return.
		push(@attrStack, $attrsRef);
		return;
	}

	# If our parent doesn't have any children yet, it does now.
	unless (exists $parent->{_CHILDREN_})
	{
		$parent->{_CHILDREN_} = { };
	}
	
	# If our parent has children with our tag name, add ourself to
	# that list. Otherwise create a new list with ourself in it.
	if (exists $parent->{_CHILDREN_}->{$tag})
	{
		push(@{$parent->{_CHILDREN_}->{$tag}}, $attrsRef);
	}
	else
	{
		$parent->{_CHILDREN_}->{$tag} = [ $attrsRef ];
	}

	# Finally, put ourself on the stack.
	#MyLog::Log(1, "DT_dext_DEBUG --> CREADO STACK NODE $attrsRef PARA $tag");
	#foreach my $japkey (keys %$attrsRef) {
		#	MyLog::Log(1, "  DT_dext_DEBUG --> conteniendo $japkey");
		#}
	push(@attrStack, $attrsRef);
}

#
# LeaveAttributeNode
#
# Keep the attribute stack intact.
#
sub LeaveAttributeNode
{
	pop(@attrStack);
}

#
# Attribute
#
# Find a given attribute and return its value. If there were multiple
# values, only return the first. (Use RotateAttribute to get others.)
#
sub Attribute
{
	my ($key) = @_;

	my $attr = FindAttribute($key);
	my $ref  = ref($attr);
	
	if ($ref eq 'ARRAY')
	{ 
		$attr = @{$attr}[0]->{_BODY_};
	}

	#MyLog::Log(1, "DT_dext_DEBUG --> FIND OF ATT RETURNS $attr");
	return $attr;
}

sub AddAttribute
{
	my ($key, $val, $recurse) = @_;

	# Add to last frame on stack; bail if no frames there.
	return unless (defined $attrStack[-1]);

	DEXT::Util::Log("addattr: marking " . $attrStack[-1]->{_TAG_} ." ". $val);
	$attrStack[-1]->{$key} = $val;

	if ($recurse =~ /^parents/)
	{
		foreach my $frame (reverse @attrStack)
		{
			# skip if value already defined and weak recurse
			next if (($recurse eq 'parents-weak') &&
					 defined($frame->{$key}));

			DEXT::Util::Log("addattr: marking " . $frame->{_TAG_} ." ". $val);
			$frame->{$key} = $val;
		}
	}
	#MyLog::Log(1, "DT_dext_DEBUG --> ADD ATT to $frame, tag". $frame->{_TAG_} ." ". $val);
}

#
# RotateAttribute
#
# For an attribute which has multiple values, take the first one and
# stick it on the end. A subsequent call to Attribute() will then
# return the new first element.
#
sub RotateAttribute
{
	my ($key) = @_;

	my $attr = FindAttribute($key);
	my $ref  = ref($attr);
	
	if ($ref eq 'ARRAY')
	{ 
		my $front = shift @{$attr};
		push(@{$attr}, $front);
	}
	else
	{
		DEXT::Util::Log("tried to rotate attribute $key which wasn't a list");
	}
}

#
# NumAttributes
#
# Return the number of values an attribute has.
#
sub NumAttributes
{
	my ($key) = @_;

	my $attr = FindAttribute($key);
	my $ref  = ref($attr);
	my $num;
	
	if (!defined($attr))
		{ return 0; }
	elsif ($ref eq 'ARRAY')
		{ $num = int @{$attr}; return $num; }
	elsif (!defined($ref))
		{ return 1; }
}

#
# FindAttribute
#
# Scan backwards through the attribute stack looking for the first
# attribute match. If nothing is found, look for "key-default." If we
# find a child element acting as an attribute, return that list. If it
# was declared in the start tag, just return the text. Callers should
# check ref() on the return value to figure out what it is.
#
sub FindAttribute
{
	my ($key, @stack) = @_;
	my ($frame, $parent, $return, $subkeys);
	my $origkey = $key;

	@stack = reverse @attrStack unless int(@stack);
	#MyLog::Log(1, "DT_dext_DEBUG --> LOOKING FOR $key IN @stack");

	if ($key =~ /^([^:]+):(.*)$/)
	{
		$key = $1;
		$subkeys = $2;
	}

	# Scan backwards through attribute stack trying to find the
	# requested key.
	foreach $frame (@stack)
	{
		#MyLog::Log(1, "DT_dext_DEBUG --> LOOKING IN FRAME $frame of ".$frame ->{_TAG_});
		# First check this level for immediate children whose tag
		# matches what we're looking for.
		if (exists $frame->{_CHILDREN_} &&
			exists $frame->{_CHILDREN_}->{$key})
		{
			$return = $frame->{_CHILDREN_}->{$key};
			goto found;
		}
		
		# Otherwise check entity params embedded in the tag.
		return $$frame{$key} if (exists $$frame{$key});
	}

	# Call additional attribute method passed to new(), if any.
	if (defined $self->{_addlAttrs})
	{
		my $val = &{$self->{_addlAttrs}}($origkey);

		return $val if defined ($val);
	}

	# Hmm, I guess that didn't work. Now search for the same key with
	# "-default" tacked on the end.
	$key .= "-default";

	# Second verse same as the first...
	foreach $frame (@stack)
	{
		if (exists $frame->{_CHILDREN_} &&
			exists $frame->{_CHILDREN_}->{$key})
		{
			$return = $frame->{_CHILDREN_}->{$key};
			goto found;
		}
		
		return $$frame{$key} if (exists $$frame{$key});
	}

	DEXT::Util::Log("couldn't find a value for $key, dude.");
	#MyLog::Log(1, "DT_dext_DEBUG --> ATT NOT FOUND");
	return undef;

  found:

	if (defined($subkeys))
	{
		return FindAttribute($subkeys, @$return);
	}
	else
	{
		return $return;
	}
}

#
# SubstituteAttributes
#
# Dig through a string looking for variables and replace them with
# attributes in the current scope.
#
sub SubstituteAttributes
{
	my ($string, $marker) = @_;

	# Hack: see note in LoadTemplates about this.
	$marker = "::VAR::" unless defined($marker);

	# Special case!!! If someone requests the _BODY_ attribute, we
	# must scan upwards in the attribute stack and grab the body text
	# of the entity immediately above the current template's base tag.
	# This will give us the text which is enclosed by the template's
	# tags (i.e. the character data of the template entity).
	if ($string =~ /${marker}\s*?_BODY_[\w\-:]*?\s*?${marker}/)
	{
		# Get index of template entity's attr frame minus one more.
		my $index = -1;
		while ($attrStack[$index--]->{_TAG_} ne $baseTag) { }

		# Attribute stack dump is sometimes helpful in debugging.
		if ($debugMode && 0)
		{
			print "_BODY_ sub; index is $index, stack size is " .
				scalar(@attrStack) . ", matching tag is " .
					$attrStack[$index]->{_TAG_} . "\n";
			print "lenth of body in each frame:\n";
			foreach my $frame (@attrStack)
			{
				print "  " . $frame->{_TAG_} . ":" . length($frame->{_BODY_});
			}
			print "\n";
		}
		
		# ...and substitute that.
		$string =~ s/${marker}\s*?(_BODY_[\w\-:]*?)\s*?${marker}/
			MungeAttributeSubstitition($1, $attrStack[$index]->{_BODY_}) /eg;
	}

	# Substitute other attributes as required. Start 
	# with plain %%%thing%%% ones first.
	$string =~ s/${marker}\s*?([\w\-:]+?)\s*?${marker}/
		MungeAttributeSubstitition($1) /eg;
	
	# Now do %%%(thing)%%% ones, which may have contained plain
	# %%%thing%%% ones that were just sub'd in the line above.
	$string =~ s/${marker}\(\s*?([\w\-:]+?)\s*?\)${marker}/
		MungeAttributeSubstitition($1) /eg;

	return $string;
}

#
# MungeAttributeSubstitition
#
# Attributes can have post-processors on them. This scans for
# processors and applies them as needed (a.k.a. munging), returning
# the munged attr. The format of a variable which should be processed
# is attr-PROCESSOR, where the attribute is "attr" and the processor
# name is "PROCESSOR". Processors can be chained, too.
#
sub MungeAttributeSubstitition
{
	my ($attribute, $substitute) = @_;

	my %processors = ("URLENCODED" => \&URLEncode,
					  "FILTER_UNDERLINE"  => \&FilterUnderline,
					  "FILTER_SPACE"  => \&FilterSpace,
					  "BASE"  => \&BaseLink,
					  "LOWERCASE"  => \&Lowercase,
					  "UPPERCASE"  => \&Uppercase,);

	# Split the attribute name across dashes, with each chunk being a
	# potential processor
	my @attrchunks = split("-", $attribute);
	my ($chunk, $processor, @processors);

	# Now scan backwards over our chunks, popping off ones which
	# match known processors. Stop at the first unknown chunk, which
	# is part of the attribute name.
	while (defined ($processor = $processors{$chunk = pop @attrchunks}))
	{
		DEXT::Util::Log("found processor $processor for $attribute");
		push(@processors, $processor);
	}
	push (@attrchunks, $chunk); # push last one back on

	# Now restore the attribute name (which may have had dashes in
	# it), minus the processors chained on the end.
	$attribute = join("-", @attrchunks);
	
	# Use the restored attr name to get the substitute.
	$substitute = Attribute($attribute) 
		unless (defined $substitute || $attribute eq "_BODY_");

	# print "final attr $attribute = $substitute\n";

	# Now apply each processor to the substitute.
	while ($processor = pop @processors)
	{
		$substitute = &$processor($substitute);
	}

	# Return an empty string if $substitute is undef.
	$substitute = '' unless defined($substitute);

	return $substitute;
}



#######################################################################
# gxml:x commands
#######################################################################


#
# ExistsCommand
#
# Returns 'discard' to HandleEnd unless the attribute 'expr' is true.
# 'expr' may be an attribute name, or some combination of attribute
# names with logical operators, e.g. 'name AND NOT age'.
#
sub ExistsCommand
{
	my ($rParams) = @_;
	$ELSE_param = undef;

	my $element = Attribute('expr');


	unless (length($element))
	{
		DEXT::Util::Log("couldn't find element for gxml:ifexists command");
		return;
	}

	#
	# Sub in perl logical operators in place of English...
	#
	$element =~ s/\band\b/\&\&/ig;
	$element =~ s/\bor\b/\|\|/ig;
	$element =~ s/\bnot\b/!/ig;
	$element =~ s/(\w+)/length(Attribute("$1"))/g;
	
	# ...and then eval() it. I love Perl.
	DEXT::Util::Log("DT_dext_BUR evaluating $element !!!!");
	unless (eval($element))
	{
		# discard if expr not true
	DEXT::Util::Log("DT_dext_BUR discarding ifexists !!!!");
		$ELSE_param = 1;
		return ['discard'];
	}

	# Be sure to discard the gxml:ifexists tag
	DEXT::Util::Log("DT_dext_BUR accepting ifexists !!!!");
	$ELSE_param = 0;
	return ['striptag'];
}

sub ConditionCommand
{
	my ($rParams) = @_;
	$ELSE_param = undef;

	my $element = Attribute('expr');


	unless (length($element))
	{
		DEXT::Util::Log("couldn't find element for gxml:ifcondition command");
		return;
	}

	#
	# Sub in perl logical operators in place of English...
	#
	$element =~ s/\band\b/\&\&/ig;
	$element =~ s/\bor\b/\|\|/ig;
	$element =~ s/\bnot\b/!/ig;
	#$element =~ s/(\w+)/(Attribute("$1"))/g;
	$element =~ s/%%%(\w+)%%%/(Attribute("$1"))/g;
	$element =~ s/(\w+)/$1/g;

	# ...and then eval() it. I love Perl.
	DEXT::Util::Log("DT_dext_COND evaluating $element !!!!");
	unless (eval($element))
	{
		# discard if expr not true
	DEXT::Util::Log("DT_dext_COND discarding ifcondition (else_param true)!!!!");
		$ELSE_param = 1;
		return ['discard'];
	}

	# Be sure to discard the gxml:ifcondition tag
	DEXT::Util::Log("DT_dext_COND accepting ifcondition !!!!");
	$ELSE_param = 0;
	return ['striptag'];
}

sub ElseCommand
{
	if (defined($ELSE_param)) {
		DEXT::Util::Log("DT_dext_ELSE else_param=$ELSE_param !!!!");
	} else {
		DEXT::Util::Log("DT_dext_ELSE ELSE no aplicable !!!!");
	}

	if ($ELSE_param)
	{
		DEXT::Util::Log("DT_dext_ELSE accepted !!!!");
		$ELSE_param = undef;
		return ['striptag'];
	}

	$ELSE_param = undef;
	return ['discard'];
}


#
# EqualsCommand
#
# Returns 'discard' to HandleEnd unless the attribute 'expr' is
# present and equal to 'equalto'.
# 
#
sub EqualsCommand
{
	my ($rParams) = @_;
	$ELSE_param = undef;

	my $element = Attribute('expr');
	my $equalto = Attribute('equalto');

	unless (length($element))
	{
		DEXT::Util::Log("couldn't find element for gxml:equals command");
		return;
	}
	my $notequalto = Attribute('notequalto') unless (length($equalto));

    DEXT::Util::Log("equals: expr is $element, equalto is $equalto, notequalto is $notequalto");

	$ELSE_param = 1;
        if (length($notequalto) && (Attribute($element) eq $notequalto)) {
		return ['discard'];
	}
	if (length($equalto) && (Attribute($element) ne $equalto))
	{
		# discard if expr not equal to equalto
		return ['discard'];
	}
	if ( !length($equalto) && !length($notequalto) ) {
		# to prevent incorrect definitions
		return ['discard'];
	}

	# Be sure to discard the gxml:ifequal tag
	$ELSE_param = 0;
	return ['striptag'];
}



#juanpri
sub IncludeCommandEnd {
	my ($rParams) = @_;
	my $japfile = Attribute('file');
	unless (length($japfile)) {
		DEXT::Util::Log("couldn't find element for gxml:import command");
		#		return;
		MyLog::Log(0, "gxml:import --> File not specified with parameter 'file' or blank name");#juanpri
	}

	DEXT::Util::Log("gxml:import file is '$japfile'");

	# Be sure to discard the gxml:import tag
	return ['import'];
}
#
#juanpri
sub RunmeCommandEnd {
	my ($rParams) = @_;
	my $japcommand = Attribute('command');
	unless (length($japcommand)) {
		DEXT::Util::Log("couldn't find element for gxml:runme command");
		MyLog::Log(0, "gxml:runme --> Command not specified with parameter 'command' or blank name");#juanpri
	}

	DEXT::Util::Log("gxml:runme command is '$japcommand'");

	# Be sure to discard the gxml:runme tag
	return ['runme'];
}

sub ContainsCommandEnd {
	my ($rParams) = @_;
	$ELSE_param = undef;

	my $element = Attribute('expr');
	my $containstr = Attribute('string');

	unless (length($element) and length($containstr))
	{
		DEXT::Util::Log("couldn't find expr or string for gxml:contains command");
		return;
	}

	DEXT::Util::Log("ifcontains: expr is $element, string is $containstr");

	my $tmpcad = Attribute($element);
	if (!$tmpcad or $tmpcad !~ /$containstr/) {
		# discard if expr not equal to containstr
		DEXT::Util::Log("ifcontains: $element doesn't exist or $element not contains $containstr");
		$ELSE_param = 1;
		return ['discard'];
	} else {
		DEXT::Util::Log("ifcontains: $element contains $containstr");
	}

	# Be sure to discard the gxml:ifcontains tag
	$ELSE_param = 0;
	return ['striptag'];
}

#
# ForEachStart
#
# gxml:foreach will repeat a block for each value of its 'expr' param.
# Each iteration will contain a new value of expr, in the order they
# appear in the XML source. In this start handler we'll need to set up
# the special $rPreserve list with our expr so HandleChar will know to
# not mess with it.
#
sub ForEachStart
{
	my $element = Attribute('expr');

	unless (length($element))
	{
		DEXT::Util::Log("couldn't find element for gxml:foreach command");
		return;
	}

	$rPreserve = [] unless (defined($rPreserve));

	push(@$rPreserve, $element);
}

# ForEachEnd
#
# Counts the number of times we've interated, and rotates the 'expr'
# attribute to catch each value.
#
sub ForEachEnd
{
	my ($rParams) = @_;
	my $element = $rParams->{'expr'} || Attribute('expr');
	my $repeats = $rParams->{'repeats'};
	my $max     = $rParams->{'max'};

	unless (length($element))
	{
		DEXT::Util::Log("couldn't find element for gxml:foreach command");
		return;
	}

	if ($repeats)
	{
		# We've been through before, so just increment and rotate.
		$rParams->{'repeats'} = $repeats + 1;

		RotateAttribute($element);
	}
	else
	{
		# First time through. Set up our saved params hash.
		$repeats = 1;
		$max     = NumAttributes($element);

		# Don't need HandleChar to worry about us anymore.
		pop(@$rPreserve);

		$rParams->{'repeats'} = 1;
		$rParams->{'max'}     = $max;
		$rParams->{'expr'}    = $element;

		# Repeat and strip the gxml:foreach tag.
		return ['striptag', 'repeat'];
	}

	# We've rotated back to the start, so discard and stop looping.
	return ['discard'] if ($repeats >= $max);

	# We still need to loop. Repeat and strip the gxml:foreach tag.
	return ['striptag', 'repeat'];
}


#######################################################################
# Attribute post-processors
#######################################################################


#
# URLEncode
#
# Simple URL form encoder. Certainly not per-spec, but should work
# okay for now.
#
sub URLEncode
{
	my ($string) = @_;

	$string =~ s/^\s*(.*?)\s*$/$1/; # strip leading/trailing ws
	$string =~ s/\&/\%26/g;
	$string =~ s/\=/\%3d/g;
	$string =~ s/\?/\%3f/g;
	$string =~ s/ /\+/g;

	return $string;
}

# Lowercase: does what you'd expect it to.
sub Lowercase
{
	my ($string) = @_;
	
	$string =~ tr/A-Z/a-z/;
	
	return $string;
}

# Uppercase: ditto.
sub Uppercase
{
	my ($string) = @_;
	
	$string =~ tr/a-z/A-Z/;
	
	return $string;
}

# Base de un enlace para cambio de dups previo a extensión
# juanpri 20030529
sub BaseLink
{
	my ($string) = @_;
	$string =~ s/.html$//;
	return $string;
}

# Filtros de variables para eliminar underlines y espacios 
# juanpri 20030612
sub FilterUnderline {
	my ($string) = @_;
	$string =~ s/_/ /g;
	return $string;
}

sub FilterSpace {
	my ($string) = @_;
	$string =~ s/\s+//g;
	return $string;
}

#######################################################################
# GXML class template management
#######################################################################


#
# TemplateMgr
#
# Returns a reference to the template manager.
#
sub TemplateMgr
{
	my $self = shift;

	return $self->{_templateMgr};
}

#
# TemplateExists
#
# Helper method; returns TemplateExists() from the template manager.
#
sub TemplateExists
{
	my ($self, $name) = @_;

	return $self->{_templateMgr}->TemplateExists($name);
}

#
# SubstituteTemplate
#
# Copy the template and parse it as a separate XML blob, but retain
# the existing attribute stack. Returns the resulting text.
#
sub SubstituteTemplate
{
	my ($self, $templateName) = @_;

	# Make our own copy of the template so we can parse and substitute
	# our attributes into it.
	my $template = ${$self->TemplateMgr()->Template($templateName)};
	
	# Create our own aliai of relevant globals
	local ($output, $baseTag);

	#
	# Now create a new parser and parse the template. This will, of
	# course, recurse as necessary.
	#
	my $xp = new XML::Parser(ErrorContext => 2);
	$xp->setHandlers(Char		=> \&HandleChar,
					 Start		=> \&HandleStart,
					 End		=> \&HandleEnd,
					 Comment	=> \&HandleComment,
					 Default	=> \&HandleDefault);
	
	$xp->parse($template);
	
	return $output;
}


#######################################################################
# DEXT::TemplateManager
#######################################################################


package DEXT::TemplateManager;

use Cwd;

sub new
{
	my ($pkg, $templateDir, $addlTemplates, 
		$addlTemplate, $addlTempExists, $commonDir) = @_;
	my $baseDir = getcwd();

	# Create the new beast
	my $self = bless
	{
		_templateDir =>   $templateDir,
		# juanpri 20020627
		_commonDir =>   $commonDir,
	}, $pkg;

	$self->{_addlTemplates}  = $addlTemplates  if defined($addlTemplates);
	$self->{_addlTemplate}   = $addlTemplate   if defined($addlTemplate);
	$self->{_addlTempExists} = $addlTempExists if defined($addlTempExists);

	# Assemble the list of files in the templates directory
	chdir($templateDir);
	my $templateListRef = DEXT::Util::GetFileList();
	chdir($baseDir);

	#DEXT::Util::Log("DEXTmod: MAKING OF ListRef--> @$templateListRef");


	foreach my $filename (@$templateListRef)
	{
		# Only grab .xml files
		next unless $filename =~ /\.xml$/;
		
		# Strip ".xml" for saving in template hash; these will be
		# referenced sans .xml extension
		$filename =~ s/\.xml$//;

		# Store blank placeholder
		DEXT::Util::Log("DEXTmod: precaching '$filename' template in $templateDir directory");
		$DEXT::DT_dext_templatepath{$filename} = $templateDir;
		$self->{$filename} = '';
	}
	# juanpri 20020627 
	# Assemble the list of files in the common directory
	chdir($commonDir);
	my $commonTemplateListRef = DEXT::Util::GetFileList();
	chdir($baseDir);
	foreach my $filename (@$commonTemplateListRef)
	{
		# Only grab .xml files
		next unless $filename =~ /\.xml$/;
		
		# Strip ".xml" for saving in template hash; these will be
		# referenced sans .xml extension
		$filename =~ s/\.xml$//;

		# Store blank placeholder
		if ( exists($self->{$filename}) ) {
			DEXT::Util::Log("DEXTmod: $filename template just precached from $templateDir directory... skipping new precaching!");
	  	} else {
  		  $self->{$filename} = '';
		  $DEXT::DT_dext_templatepath{$filename} = $commonDir;
		  DEXT::Util::Log("DEXTmod: precaching '$filename' template in $commonDir directory");
		}
	}

	return $self;
}

sub DESTROY
{
	# nothing needed for now
}

#
# LoadTemplate
#
# Loads a given template name into the cache.
#
sub LoadTemplate
{
	my ($self, $name) = @_;
	my $baseDir = getcwd();
	my $dex_commonDir = $self->{_commonDir};
	my $dex_templateDir = $self->{_templateDir};

	DEXT::Util::Log("DEXTmod: Trying to load template $name in templates '$dex_templateDir' directory");

	my $filename = DEXT::Util::ChangeToDirectory(
					File::Spec->catfile($self->{_templateDir}, 
										$name . '.xml'));

	my $dex_opened = "templates";
	my $finaltemplatedirectory = $dex_templateDir;
	unless (open(TEMPLATE, $filename))
	{
		# juanpri 20020627
		$dex_opened = "common";
		$finaltemplatedirectory = $dex_commonDir;
		chdir($baseDir);
		DEXT::Util::Log("DEXTmod: couldn't open template $name in templates '$dex_templateDir' directory. Trying in common directory.");
		DEXT::Util::Log("DEXTmod: Trying to load template $name in common '$dex_commonDir' directory");
		$filename = DEXT::Util::ChangeToDirectory(File::Spec->catfile($self->{_commonDir}, $name . '.xml'));

		unless (open(TEMPLATE, $filename)) {
			DEXT::Util::Log("DEXTmod: couldn't open template $name in common directory '$dex_commonDir': $!");
			$dex_opened = "NONE";
			$finaltemplatedirectory = "NONE";
			chdir($baseDir);
			return;
	        }
	}

	
	# slurp everything
	local $/;
	undef $/;	# turn on slurp mode
	my $file = <TEMPLATE>;
	close(TEMPLATE);

	# JAP 20070618, pvd dependance control
	my $nodevid = undef;
	my $ptdname = "$finaltemplatedirectory/$name.xml";
	if ($file =~ s#^\s*<ximptd\s+(.*?)\s*>\s*##m) {
		my $texto = $1;
		$nodevid = $2 if ($texto =~ /nodevid\s*=\s*(['|\"]{1})\s*(\d+)\s*\1/);
		MyLog::Log(7, "GOT OPEN ximptd with nodevid='$nodevid' for $ptdname");
		$DEXT::Deps::PTDVID{$ptdname} = $nodevid if $nodevid;
	}
	MyLog::Log(7, "GOT CLOSE ximptd") if ($file =~ s#</ximptd.*$##m);

	DEXT::Util::Log("DEXTmod: Loaded $name template from the $dex_opened directory '$finaltemplatedirectory'");

	# Almacenamos dependencia del template
	DEXT::Deps::AlmacenaDependenciaXML($DEXT::DT_dext_xmlFILE, "$finaltemplatedirectory/$name.xml");

	chdir($baseDir);

	# A quick bit of haquage: change the variable markers
	# %%%blah%%% to something which is still weird (i.e. not
	# likely to conflict with legit content) but which can also be
	# a valid in an entity name, which %%%blah%%% is not. I'm
	# picking "::VAR::blah::VAR::". Make sure this matches the
	# default $marker var in SubstituteAttribute().
	$file =~ s/%%%/::VAR::/g;

	# ...and finally save a reference to the stuff we slurped.
	$self->{$name} = \$file;
	#my $dex_location = "_mapLocationDir_$name";
	#$self->{"_mapLocationDir_$name"} = $dex_opened;
}

#
# Template
#
# Returns the text of a template. Loads the template into memory if it
# isn't already cached.
#
sub Template()
{
	my ($self, $name) = @_;

	# Valid template name?
	unless (exists $self->{$name})
	{
		# Check addl hash if provided
		if (defined ($self->{_addlTemplates})
			&& defined($self->{_addlTemplates}->{$name}))
		{
			return &{$self->{_addlTemplates}->{$name}}($name);
		}
		# Check old-style addl function if provided
		elsif (defined ($self->{_addlTemplate}))
		{
			return &{$self->{_addlTemplate}}($name);
		}
	}

	# If we don't have it cached already, load it.
	unless (length($self->{$name}))
	{
		$self->LoadTemplate($name);
	}
	
	# MUST fetch from hash, as LoadTemplate may have updated the hash
	# with a filled-in entry.
	return $self->{$name};
}

#
# TemplateExists
#
# Does a given name match a template?
#
sub TemplateExists()
{
	my ($self, $name) = @_;

	# Valid template name?
	if (exists $self->{$name})
	{
		return 1;
	}
	# Check new-stile addl hash
	elsif (defined ($self->{_addlTemplates})
		   && defined($self->{_addlTemplates}->{$name}))
	{
		return 1;
	}
	# How about the (old style) addl method, if there is one?
	elsif (defined ($self->{_addlTempExists}))
	{
		return &{$self->{_addlTempExists}}($name);
	}

	# Guess not.
	return 0;
}

#
# CheckModified
#
# Checks to see if any templates have been modified since the last
# call to UpdateModified(). Returns true if so.
#
#sub CheckModified()
#{
#	my $self    = shift;
#	my $baseDir = getcwd();
#	my %templateModtimes;
#	my $templatesChanged = 0;
#
#	# Load the saved modification times for the templates. If any
#	# templates have changed, return 1.
#
#	my $modtimeFile = File::Spec->catfile($self->{_templateDir}, '.modtimes');
#	DEXT::Util::LoadModtimes($modtimeFile, \%templateModtimes);
#
#	foreach my $template (keys %$self)
#	{
#		next if $template =~ /^_/; # skip private variables
#
#		my $dex_locator = "_templateDir";
#		my $dex_file_locator = "$self->{$dex_locator}/".$template.'.xml';
#		# juanpri 20020627
#		unless (-e $dex_file_locator) {
#		  $dex_locator = "_commonDir";
#		} 
#
#		$dex_locator_label = ($dex_locator eq "_commonDir") ? "common" : "templates"; 
#
#                $template = DEXT::Util::ChangeToDirectory(File::Spec->catfile($self->{$dex_locator}, $template . '.xml')); 
#	
#		# Check the mod time
#		my $modtime = (stat $template)[9];
#		if (!defined($templateModtimes{$template}) ||
#			!defined($modtime)                     ||
#			$modtime != $templateModtimes{$template})
#		{
#			DEXT::Util::Log("template '$template' in '$dex_locator_label' directory was modified");
#			$templateModtimes{$template} = $modtime;
#			$templatesChanged = 1;
#		}
#
#		chdir($baseDir);
#	}
#
#	# Save this if needed for UpdateModified.
#	$self->{_modtimes} = \%templateModtimes;
#
#	return $templatesChanged;
#}

#
# UpdateModified
#
# Updates the template mod times file to reflect current reality.
#
#sub UpdateModified
#{
#	my $self = shift;
#
#	# Update our modtimes if client didn't do that before
#	unless (exists $self->{_modtimes})
#	{
#		$self->CheckModified();
#	}
#
#	my $modtimeFile = File::Spec->catfile($self->{_templateDir}, '.modtimes');
#
#	DEXT::Util::SaveModtimes($modtimeFile, $self->{_modtimes});
#}


#######################################################################
# DEXT::AttributeCollector
#######################################################################


package DEXT::AttributeCollector;

use Cwd;

# These vars are used as locals during parsing.
use vars ('@attrStack', '$baseTag', '$self');

sub new
{
	my ($pkg, $element, $key, $tocollect) = @_;

	my $self = bless
	{
		_element => $element,
		_collect => $tocollect,
		_key     => $key,
	}, $pkg;

	return $self;
}

sub DESTROY
{
	# nothing needed for now
}

sub Collect
{
	my ($selfParam, $stuff) = @_;

	# Set up these pseudo-global vars
	local (@attrStack, $baseTag);

	# Also create this so XML::Parser handlers can see it
	local $self = $selfParam;

	# Process the beastie
	my $xp = new XML::Parser(ErrorContext => 2);
	$xp->setHandlers(Char		=> \&CollectorChar,
					 Start		=> \&CollectorStart,
					 End		=> \&CollectorEnd,);

	$xp->parse($stuff);
}

sub CollectFromFile
{
	my ($selfParam, $file) = @_;
	my $baseDir = getcwd();

	# Set up these pseudo-global vars
	local (@DEXT::attrStack, $baseTag);

	# Also create this so XML::Parser handlers can see it
	local $self = $selfParam;

	my $fileName = DEXT::Util::ChangeToDirectory($file);
	
	open(IN, $fileName) || MyLog::Log(0, "open input $fileName: $!");

	# Process the beastie
	my $xp = new XML::Parser(ErrorContext => 2);
	$xp->setHandlers(Char		=> \&CollectorChar,
					 Start		=> \&CollectorStart,
					 End		=> \&CollectorEnd,);

	$xp->parse(*IN);

	close(IN);
	chdir($baseDir);
}

sub Clear
{
	my $self = shift;

	foreach my $item (keys %$self)
	{
		next if $item =~ /^_/; # preserve private vars

		delete $self->{$item};
	}
}

sub CollectorStart()
{
	my ($xp, $element, %attrs) = @_;
	my ($key);

	# First entity in the document is always the base
	$baseTag = $element unless defined($baseTag);
	
	foreach $key (keys %attrs)
	{
		my $val = $attrs{$key};

		# Compact whitespace, strip leading and trailing ws.
		$val =~ s/\s+/ /g;
		$val =~ s/^\s*(.*?)\s*$/$1/;

		# Stick newly molested $val back into $attrs if we've still
		# got something left, or delete the attribute if it's empty.
		if (length($val))
			{ $attrs{$key} = $val; }
		else
			{ delete $attrs{$key}; }
	}

	# Save our tag name in the attrs, too.
	$attrs{_TAG_} = $element;

	# Add these attributes to the master tree.
	DEXT::AddAttributeNode($element, \%attrs);
}

sub CollectorEnd
{
	my ($xp, $element) = @_;
	my %values;

	# We can bail quick if this isn't an element we're looking for
	if ($element ne $self->{_element})
	{
		DEXT::LeaveAttributeNode();
		return;
	}

	foreach my $attr (@{$self->{_collect}})
	{
		$values{$attr} = DEXT::Attribute($attr);
	}

	my $key = DEXT::Attribute($self->{_key});
	$self->{$key} = \%values;

	DEXT::LeaveAttributeNode();
}

sub CollectorChar()
{
	my ($xp, $string) = @_;

	# Achtung! We must process the original string, not the one
	# munged by Expat into UTF-8, which will automatically remap
	# things like "&lt;" into "<". If the author wrote &lt; in their
	# XML document, that's probably because they wanted it in their
	# HTML document, too.
	$string = $xp->original_string;

	# Append the body text to the _BODY_ attribute of the last
	# attribute frame on the stack (i.e. that of the most immediately
	# enclosing entity).
	$DEXT::attrStack[-1]->{_BODY_} .= $string;
}


#######################################################################
# Utilities
#######################################################################


package DEXT::Util;

use Cwd;
use File::Basename;
use File::Path;
use File::Spec;

#
# ChangeToDirectory
#
# Given a path to a file, change into the directory for that file,
# creating the directories along the way if they don't already exist.
# Returns the file name sans all directory stuff.
#
sub ChangeToDirectory
{
	my ($fullName) = @_;

	my ($name, $path) = fileparse($fullName);

	# strip trailing / if necessary
	$path =~ s/\/$//;

	mkpath($path);
	chdir($path);
	
	return $name;
}

#
# GetFileList
#
# Scans the current directory and returns all the filenames therein,
# recursing through directories as needed. This should probably be
# replaced with something super-easy-to-use from an existing Perl
# module. (Real Soon Now.)
#
sub GetFileList
{
	my ($prefix) = @_;
	my (@files, $entry, $name);
	my $currentDir = getcwd();
	local *DIR;

	opendir(DIR, $currentDir) or MyLog::Log(0, "can't opendir startdir: $!");
	
	while(defined($entry = readdir(DIR)))
	{
		next if $entry =~ /^\.\.?$/; # skip '.' and '..'
		next if $entry =~ /^\.modtimes$/; # skip our mod times file
		next if $entry =~ /^CVS$/;   # skip CVS directories
		next if $entry =~ /^\.AppleDouble$/;	# ditto
		next if $entry =~ /~$/;		 # emacs temp files
		next if $entry =~ /^\#/;	 # ditto

		if (defined($prefix))
			{ $name = File::Spec->catdir($prefix, $entry); }
		else
			{ $name = $entry; }

		if (chdir($entry))
		{
			my $subfilesRef = GetFileList($name);
			push(@files, @$subfilesRef);
			
			chdir($currentDir);
		}
		else
		{
			push(@files, $name);
		}
	}
	
	closedir(DIR);
	
	return \@files;
}

#
# LoadModtimes
#
# Loads a list of modification times into a hash. Format of the mod
# time file is "filename\tmodtime", one filename per line.
#
sub LoadModtimes
{
	my ($modfile, $modtimesRef) = @_;

	open(MODTIMES, $modfile) or return;

	while (my $line = <MODTIMES>)
	{
		chomp($line);
		my ($filename, $modtime) = split("\t", $line);
		$modtimesRef->{$filename} = $modtime;
	}
	
	close(MODTIMES);
}

#
# SaveModtimes
#
# Dumps a hash of mod times into a file which LoadModtimes can read.
#
sub SaveModtimes
{
	my ($modfile, $modtimesRef) = @_;

	open (MODTIMES, '>' . $modfile);

	foreach my $filename (keys %$modtimesRef)
	{
		print MODTIMES $filename . "\t" . $modtimesRef->{$filename} . "\n";
	}

	close (MODTIMES);
}


#
# Log
#
# Prints stuff to STDERR if $debugMode is turned on.
#
sub Log
{
	if ($debugMode)
	{
		my $message = shift;
		#		print STDERR "**log** $message\n";
		MyLog::Log(8, "log --> $message");
	}
} 

# sucessful package load
1;

package DEXT::Deps;

sub AlmacenaDependenciaXML {
	my ($archivoxml, $archivoinc) = @_;

	DEXT::Util::Log("DT_dext_dep inserting dependance for $archivoxml: $archivoinc");
	my $refhash = $DEXT::DT_dext_dependances{$archivoxml} || {};
	if ($DEXT::DT_dext_dependances_erase{$archivoxml}) {
		DEXT::Util::Log("DT_dext_dep removing previous info for $archivoxml ");
		$refhash = {};
		$DEXT::DT_dext_dependances_erase{$archivoxml} = 0;
	}
	#push @$refhash, $archivoinc;
	$$refhash{$archivoinc}++;
	$DEXT::DT_dext_dependances{$archivoxml} = $refhash;
	#my @vacio = keys(%$refhash);
	#DEXT::Util::Log("DT_dext_dep known dependances for $archivoxml --> @vacio");
}


sub ImprimeDependenciaXML {
	my ($archivoxml) = @_;
	
	my $refhash = $DEXT::DT_dext_dependances{$archivoxml};
	if ($refhash) {
		#my @vacio = keys(%$refhash);
		#DEXT::Util::Log("DT_dext_dep dependance list for $archivoxml --> @vacio");
		my @ptdvid = undef;
		my @ptdname = undef;
		while (my($ptdname,$ptdnum)=each %$refhash) {
			my $ptdvid = $DEXT::Deps::PTDVID{$ptdname};
			push(@ptdvid, $ptdvid) if $ptdvid;
			push(@ptdname, $ptdname . "($ptdnum)") 
                }
		DEXT::Util::Log("DT_dext_dep dependances for $archivoxml as names --> @ptdname");
		MyLog::Log(1,"PTD dependances for $archivoxml --> @ptdvid");
		print "PTD dependances for $archivoxml --> @ptdvid";
	}
}

1;
__END__
# Metodos compatibles standalone
sub SalvaDependencias {
	my $fileInNode = shift @_;
	my $archivo = "node.dep";

	open(DT_dext_DFIL, ">$archivo") || MyLog::Log(0, "open input $archivo for writing: $!");
	foreach my $key (keys(%DEXT::DT_dext_dependances)) {

		# si estamos con --file debemos ver el archivo específico
		# si lleva extensión .xml, y el específico + los -id??? si
		# no lleva la extensíon
		my $condMustSeenIt = 1;
	        if ($fileInNode) {
			my $fileinnode = $fileInNode; # parámetro del --file
			
			my $archi = $key; # archivo del node.dep
			@archi = split(/\//, $archi);
			$archi = pop @archi;
			$archi =~ s/\.html$//;

			if ($fileinnode =~ s/\.xml$//) {
				# Sólo es el archivo específico a comparar
				$condMustSeenIt = ($fileinnode eq $archi); 
			} else {
				# no lleva la extensión, comparamos sin -id...
				$archi =~ s/(-id...)*$//;
				$condMustSeenIt = ($fileinnode eq $archi); 
			}	
		}

		if ($DEXT::DT_dext_HTMLseen{$key} || !$condMustSeenIt) {
			my $refhash = $DEXT::DT_dext_dependances{$key};
			my @vacio = keys(%$refhash);
			print DT_dext_DFIL "$key: @vacio\n\n";
			DEXT::Util::Log("DT_dext_dep saving file $archivo with dependances $key --> @vacio");
			if (!$condMustSeenIt) {
				MyLog::Log(4, "+Dependence stored for $key because not related");
			} else {
				MyLog::Log(4, "xDependence stored for $key because processed");
			}
		} else {
			DEXT::Util::Log("DT_dext_dep source xml file for $key does not exist ... skipped $archivo reference and erasing $key");
			if (-e $key) {
				if (unlink $key) {
					MyLog::Log(1, "No XML source for file '$key'... dependance and html removed!");
					push @::XIMDEX_outputgen, "No XML source for file '$key'... dependance and html removed!\n";
				} else {
					DEXT::Util::Log("DT_dext_dep File $key can not be removed!");
				}
			}
		}
	}
	close(DT_dext_DFIL);
}

sub CargaDependencias {
	%DEXT::DT_dext_dependances = ();
	%DEXT::DT_dext_dependances_erase = ();
        my $archivo = "node.dep";
	my $status = 0;
	if (open(DT_dext_DFIL, "$archivo")) {
		while (my $linea = <DT_dext_DFIL>) {
			if ($linea =~ /(.+)\s*:(.*)/) {
				#DEXT::Util::Log("DT_dext_XOR key $1 valor $2");
				my @lista = split(/\s+/, $2);
				my $refhash = {};
				foreach my $archivoinc (@lista) {
					$$refhash{$archivoinc}++ if $archivoinc;
					$status++;
				}
				DEXT::Util::Log("DT_dext_dep load dependances from $archivo for $1 --> @lista");
				$DEXT::DT_dext_dependances{$1} = $refhash;
				$DEXT::DT_dext_dependances_erase{$1} = 1;
			}
		}
	}
	return($status);
}


sub VerificaTiemposDependencias {
	my $htmlarchi = shift  @_;
	my $changed = 1;
	my $maxmod = 0;
	
	# Vemos si existe el archivo
	unless (-f $htmlarchi) {
		DEXT::Util::Log("DT_dext_CHECK $htmlarchi doesn't exist");
		return(1);
	}

	my $htmlmodt = (stat $htmlarchi)[9];
	DEXT::Util::Log("DT_dext_CHECK modtime $htmlarchi --> $htmlmodt");

	# Vemos si existe info de dependencias
	my $ref = $DEXT::DT_dext_dependances{$htmlarchi}; 

	if ($ref) {
		my @dependencias = keys(%$ref);
		DEXT::Util::Log("DT_dext_CHECK dependances for $htmlarchi: @dependencias");

		foreach my $archi (@dependencias) {
			my $modt = $DT_dext_modtimes{$archi} || (stat $archi)[9];
			$DT_dext_modtimes{$archi} = $modt;
			$maxmod = $modt if ($maxmod<$modt);
			DEXT::Util::Log("DT_dext_CHECK  modtime '$archi' --> $modt, tohtml=".($htmlmodt-$modt). " max=$maxmod");
			MyLog::Log(5,  "  INFO DEPs NEWER '$archi' --> $modt, tohtml=".($htmlmodt-$modt)) if $modt > $htmlmodt;
		}
	} else {
		$maxmod = $htmlmodt;
		DEXT::Util::Log("DT_dext_CHECK no dependances for file '$htmlarchi'... creating it!"); 
	}

	$changed = 0 if ($maxmod < $htmlmodt);
	return $changed;
}

1;

__END__

=head1 NAME

DEXT - Perl extension for XML transformation, XML->HTML conversion

=head1 SYNOPSIS

  use DEXT;
  
  my $gxml = new DEXT();
  
  # Take a scalar, return a scalar
  my $xml = '<basetag>hi there</basetag>';
  my $new = $gxml->Process($xml);
  
  # Take a file, return a scalar
  print $gxml->ProcessFile('source.xml');
  
  # Take a file, output to another file
  $gxml->ProcessFile('source.xml', 'dest.xml');

=head1 DESCRIPTION

GXML is a perl module for transforming XML. It may be put to a variety
of tasks; in scope it is similar to XSL, but less ambitious and much
easier to use. In addition to XML transformations, GXML is well-suited
to translating XML into HTML. Please see the documentation with your
distribution of GXML, or visit its web site at:

  http://multipart-mixed.com/xml/

=head1 SUMMARY OF PARAMETERS

These are the options for creating a new GXML object. All options are
passed in via a hash reference, as such:

  # Turn on HTML mode and provide callbacks hash
  my $gxml = new DEXT({'html'      => 'on', 
                            'callbacks' => \%callbacks});

Here's the complete list of options. Keys are provided first, with
their values following:

  html:           'on' or 1 will format output as HTML (see docs).
  templateDir:    directory to look for templates.
  remappings:     hashref mapping tag names to remap to their remapped
                  names.
  dashConvert:    'on' or 1 will convert '--' to unicode dash.
  addlAttrs:      reference to subroutine that gets called on lookup
                  for dynamic attributes.
  addlTemplates:  hashref mapping from dynamic template name to
                  subroutine that will create that template. (Use this
                  instead of the following 2 params.)
  addlTempExists: (outdated -- use addlTemplates instead.)
  addlTemplate:   (outdated -- use addlTemplates instead.)

=head1 AUTHOR

Josh Carter, josh@multipart-mixed.com

=head1 SEE ALSO

perl(1).

=cut
