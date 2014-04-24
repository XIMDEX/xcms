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
<h2>Welcome</h2>
<p>
	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
</p>
    <ul>
    <?php foreach ($checks as $check) {
    	?>
    	<li class="<?php echo $check["state"]?>">
	      <p class="title"> <?php echo $check["name"]?> <span ng-show="module.processed"><?php echo $check["state"]?></span>
		
	      </p> 	      
	      	<?php
	      		foreach ($check["messages"] as $message) {
	      			?>
	      			<p class="desc"><?php echo $message;  ?></p>
	      			<?php
	      		}
	      	?>	      

	    </li>
    <?php
    }
	?>	
		
	</ul>
</form>