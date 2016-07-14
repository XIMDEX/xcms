<?php

namespace Ximdex\NodeTypes;

use Illuminate\Support\Collection;
use Ximdex\Runtime\App;

class Helper
{
    /**
     * @var Collection
     */
    private static $nodeTypesList;

    /**
     * @return Collection
     * @throws \Exception
     */
    public static function getNodeTypes()
    {
        if (!isset(self::$nodeTypesList)) {

            $stm = App::Db()->prepare('select IdNodeType as id, Name as name  from NodeTypes');
            $stm->execute([]);
            self::$nodeTypesList = (new Collection($stm->fetchAll()))
                ->map(function ($row) {
                    // casting
                    $row['id'] = intval($row['id']);
                    return $row;
                }
                );

        }
        return self::$nodeTypesList;
    }

    /**
     * @param $name
     * @return String/null
     */
    public static function getIdByName($name)
    {

        return self::getNodeTypes()->where('name', $name)
            ->flatMap(function ($values) {
                return $values;
            })
            ->get('id', null);

    }

    /**
     * @param $id
     * @return Int/Null
     */
    public static function getNameById($id)
    {

        return self::getNodeTypes()->where('id', $id)
            ->flatMap(function ($values) {
                return $values;
            })
            ->get('name', null);
    }
}