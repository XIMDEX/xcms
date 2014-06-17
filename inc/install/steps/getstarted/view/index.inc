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
?>

<form method="post" action=".">
    <input type="hidden" name="method" value="<?php echo $goMethod ?>">
	<h2>Installation finished</h2>
	<p>That's it! You've succesfully installed Ximdex CMS on your server!<br/>Log in and discover a different way to manage your content.</p>
        <p>Enable decoupled publishing adding these lines to your crontab</p>
        <pre>* * * * * (php /var/www/git/ximdex/modules/ximNEWS/actions/generatecolector/automatic.php) >> /var/www/my_x35/logs/automatic.log 2>&1<br/>
            * * * * * (php /var/www/git/ximdex/modules/ximSYNC/scripts/scheduler/scheduler.php) >> /var/www/my_x35/logs/scheduler.log 2>&1
        </pre>
	<button class="launch_ximdex action_launcher" id="submitButton" onclick="document.forms[0].submit(); return false;">
          Get started
      </button>
</form>
