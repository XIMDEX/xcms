<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 28/05/16
 * Time: 11:19
 */

namespace Ximdex\Setup\Step;


use Ximdex\Setup\Manager;

class Wellcome extends Base
{
    public function __construct( Manager $manager )
    {
       parent::__construct( $manager);
        $this->label = "Wellcome";
        $this->template = "wellcome.twig" ;
        $this->title = "Wellcome to Ximdex CMS" ;
        $this->vars['title'] = $this->title ;

    }

}