<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Deps;

use Ximdex\Logger;
use Ximdex\Models\Node;

class LinksManager
{
    public function removePathTo(string $content)
    {
        return preg_replace("/@@@RMximdex\.pathto\(([^\)]+)\)@@@/i", "&#60;%=ControllerURL.resolveURL(\${1})%&#62;", $content);
    }

    function removeDotDot(string $content)
    {
        return preg_replace("/@@@RMximdex\.dotdot\(([^\)]*)\)@@@/i"
            , "&#60;%=ControllerURL.resolveURL(&#34;NULL&#34;, &#34;NULL&#34; ,&#34;\${1}&#34;)%&#62;", $content);
    }

    public function aenlaceid(int $nodeId = null)
    {
        if (is_null($nodeId)) {
            return '';
        }
        $linkNode = new Node($nodeId);
        if (! $linkNode->get('IdNode')) {
            Logger::info("Link to unexisting node $nodeId");
            return '#';
        }

        // Ximlink nodetypes
        if ($linkNode->nodeType->get('Name') == 'Link') {
            $name = $linkNode->class->GetUrl();
            $linkType = 'ximlink';
        } else {
            $name = $linkNode->get('Name');
            $linkType = '';
        }
        return "&#34;" . $linkNode->get('IdNode') . "&#34;, &#34;$name&#34;, &#34;" . $linkNode->GetPublishedPath() . "&#34;,&#34;$linkType&#34;";

    }

    public function url(int $nodeId)
    {
        if (is_null($nodeId)) {
            return '';
        }
        $linkNode = new Node($nodeId);
        if (! $linkNode->get('IdNode')) {
            Logger::info("Link to unexisting node $nodeId");
            return '#';
        }

        // Ximlink nodetypes
        if ($linkNode->nodeType->get('Name') == 'Link') {
            $name = $linkNode->class->GetUrl();
            $linkType = 'ximlink';
        } else {
            $name = $linkNode->get('Name');
            $linkType = '';
        }
        return "&#34;" . $linkNode->get('IdNode') . "&#34;, &#34;$name&#34;, &#34;" . $linkNode->GetPublishedPath() . "&#34;,&#34;$linkType&#34;";
    }
}
