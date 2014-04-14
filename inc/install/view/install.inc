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
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="favicon.ico" >
    <link rel="stylesheet" href="xmd/style/installer/normalize.css">
    <link rel="stylesheet" href="xmd/style/installer/main.css">
    <link rel="stylesheet" href="extensions/ladda/dist/ladda-themeless.min.css">
    <link rel="stylesheet" href="extensions/humane/flatty.css">

    <link href='http://fonts.googleapis.com/css?family=Coustard|Open+Sans:400,700,800,300,600' rel='stylesheet' type='text/css' />

    <title>Ximdex Installer</title>

    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>
<body ng-app="ximdexInstallerApp">

    <div class="aside">
        <h1><a href="http://www.ximdex.com">ximdexl</a></h1>
        <ul class="installer">
        <?php        
            foreach ($this->steps as $index => $step) {
                $extraStyle="";
                $extraStyle = ($index < $this->currentStep)? "installer_step-completed": $extraStyle;
                $extraStyle = ($index == $this->currentStep)? "installer_step-current" : $extraStyle;
                ?>
                   <li class='installer_step <?php echo $extraStyle; ?>'>
                    <button class="installer_step-launcher icon">
                        <?php
                         echo $step["description"];
                        ?>
                    </button>
                </li>    
                <?php
            }
        ?>            
        </ul>

        <ul class="social">
            <li class="icon_btn">
                <a href="https://twitter.com/ximdex" class="twitter_btn icon">Cuenta Twitter de Ximdex</a>
            </li>
            <li class="icon_btn">
                <a href="https://plus.google.com/+Ximdex/about" class="icon google_btn">Cuenta Google+ de Ximdex</a>
            </li>
            <li class="icon_btn">
                <a href="https://www.facebook.com/Ximdex" class="icon facebook_btn">Cuenta Facebook de Twitter</a>
            </li>
        </ul>

        <div class="footer">
            <a href="http://www.ximdex.com">www.ximdex.com</a>
        </div>
    </div><div class="content step-<?php echo $this->installManager->currentState; ?>">
        <?php 
            include ($includeTemplateStep);
        ?>       
    </div>
    <script type="text/javascript" src="extensions/angular/angular.min.js" ></script>
    <script type="text/javascript" src="extensions/angular/angular-animate.min.js" ></script>
    <script type="text/javascript" src="extensions/angular/angular-animate.min.js" ></script>
    <script type="text/javascript" src="extensions/ladda/dist/spin.min.js" ></script>
    <script type="text/javascript" src="extensions/ladda/dist/ladda.min.js" ></script>
    <script type="text/javascript" src="extensions/humane/humane.min.js" ></script>
    <script type="text/javascript" >
        var ximdexInstallerApp = angular.module('ximdexInstallerApp',[]);
    </script>
    <?
        foreach($js_files as $js_file){
        ?>
            <script type="text/javascript" src="<?php echo $js_file; ?>"></script>
        <?php
        }
    ?>    
</body>
</html>