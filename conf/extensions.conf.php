<?php

use Ximdex\Runtime\App;

class Extensions {
    const JQUERY_PATH = "/public_xmd/vendors/jquery";
    const JQUERY      = "/public_xmd/vendors/jquery/jquery-1.8.3.min.js";
    const JQUERY_UI   = "/public_xmd/vendors/jquery/jquery-ui-1.9.1.custom.min.js";
    const SMARTY      = "/public_xmd/vendors/smarty/libs/Smarty.class.php";
    const PHPSECLIB   = "/extensions/phpseclib";
    const BOOTSTRAP   = "/public_xmd/vendors/bootstrap/dist";
     /**
        if function is called then we retrieve  UrlRoot + constant
        @param String $_func  attribute name. e.g: Extensions::smarty() -> smarty
        @param Array $_args ignored
        @return UrlRoot+attribute value
      */
      public static function __callStatic($_func, $_args) {
            $const = strtoupper($_func);
            $urlRoot = App::getValue('UrlRoot');
            $value = constant(sprintf('%s::%s', "self", $const));
            return $urlRoot . $value;
      }

}
