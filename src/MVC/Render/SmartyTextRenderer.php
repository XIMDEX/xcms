<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 29/1/16
 * Time: 12:23
 */
namespace Ximdex\MVC\Render;
include_once (XIMDEX_ROOT_PATH . '/public_xmd/vendors/smarty/libs/Smarty.class.php');
use Smarty;

/** ****************** SMARTY_STRING_RENDERER ************************** */
class SmartyTextRenderer
{

    static function render($_text, $_parameters = NULL)
    {

        $smarty = new Smarty();
        $smarty->setTemplateDir([\APP_ROOT_PATH, \XIMDEX_ROOT_PATH ] );
        $smarty->setCompileDir(SMARTY_TMP_PATH . '/templates_c');
        $smarty->setCacheDir(SMARTY_TMP_PATH . '/cache');
        $smarty->setConfigDir(SMARTY_TMP_PATH . '/configs');


        $smarty->config_vars = get_defined_constants();

        /*	$smarty->register->resource('text', array(this,
                 'smarty_resource_text_get_template',
                 'smarty_resource_text_get_timestamp',
                 'smarty_resource_text_get_secure',
                 'smarty_resource_text_get_trusted')
            ); */

        //default num_nodes: "1"
        $smarty->assign("num_nodes", 1);
        //default theme: "ximdex_theme"
        $smarty->assign("theme", "ximdex_theme");

        if (!empty($_parameters)) {
            foreach ($_parameters as $key => $value) {
                $smarty->assign($key, $value);
            }
        }

        return $smarty->fetch("string:" . $_text);
    }

}