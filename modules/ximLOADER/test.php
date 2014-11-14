<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




ModulesManager::file('/BuildParser.class.php', 'ximLOADER');


$content = dirname(__FILE__) . '/projects/the_hobbit/build.xml';
$b = new BuildParser($content);

echo "\n--- Projects ---\n";
$p = $b->getProject();
var_dump($p->name);
var_dump($p->Transformer);
var_dump($p->foobar);
var_dump('PATH: ' . $p->getPath());

echo "\n\n--- Servers PVD ---\n";
foreach ($p->getPVD() as $f) {
	var_dump($f->nodetypename);
	var_dump($f->basepath);
}

echo "\n\n--- Servers PTD ---\n";
foreach ($p->getPTD('XSL') as $f) {
	var_dump($f->nodetypename);
	var_dump($f->basepath);
}

echo "\n\n--- Servers ---\n";
$s = $p->getServers();
$s = $s[0];
var_dump($s->name);
var_dump($s->protocol);
var_dump($s->host);
var_dump('PATH: ' . $s->getPath());

echo "\n\n--- Servers images ---\n";
foreach ($s->getImages() as $img) {
	var_dump($img->nodetypename);
	var_dump($img->basepath);
}

echo "\n\n--- documents ---\n";
$x = $s->getXimdocs();
$x = $x[0];
var_dump($x->name);
var_dump($x->nodetypename);
var_dump($x->templatename);
var_dump('PATH: ' . $x->getPath());

echo "\n\n--- Sections ---\n";
$s = $s->getSections();
$s = $s[0];
var_dump($s->name);
var_dump($s->nodetypename);
var_dump('PATH: ' . $s->getPath());

echo "\n\n--- documents ---\n";
$x = $s->getXimdocs();
$x = $x[0];
var_dump($x->name);
var_dump($x->nodetypename);
var_dump($x->templatename);
var_dump('PATH: ' . $x->getPath());

?>