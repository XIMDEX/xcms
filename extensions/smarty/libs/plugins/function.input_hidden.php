<?php
# Plugin for input type="hidden"
# JMGG

function smarty_function_input_hidden($params, &$smarty)
{ 

  $name = trim(isset($params['name']) ? $params['name'] : '');
  $value = trim(isset($params['value']) ? $params['value'] : '');


  echo sprintf('<input type="hidden" name="%s" value="%s" />');
}
?>