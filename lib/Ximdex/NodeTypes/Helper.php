<?php

namespace Ximdex\NodeTypes;

use Illuminate\Support\Collection;
use PDO;
use Ximdex\Runtime\App;

class Helper
{
    /**
     * @var Collection
     */
    private static $nodetypesList  ;

    /**
     * @return Collection
     * @throws \Exception
     */
    public static function getNodeTypes()
    {
        if (!isset(self::$nodetypesList)) {
            $stm  = App::Db()->prepare( 'select * from NodeTypes');
            $stm->execute([]);
            self::$nodetypesList = new Collection( $stm->fetchAll( PDO::FETCH_ASSOC ));
        }
        return self::$nodetypesList ;
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getIdByName( $name ) {

        return self::getNodeTypes()->whereLoose( 'Name', $name )
            ->flatMap(function ($values) {
                return  $values ;
            })
            ->get( 'IdNodeType', null ) ;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getNameById( $id  ) {

        return self::getNodeTypes()->whereLoose( 'IdNodeType', $id  )
            ->flatMap(function ($values) {
                return  $values ;
            })
            ->get( 'Name', null ) ;
    }
}