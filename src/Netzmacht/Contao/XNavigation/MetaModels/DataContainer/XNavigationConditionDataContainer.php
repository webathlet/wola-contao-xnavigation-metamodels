<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 29.09.14
 * Time: 10:51
 */

namespace Netzmacht\Contao\XNavigation\MetaModels\DataContainer;


class XNavigationConditionDataContainer
{
    public function getMetaModels()
    {
        $values = array();
        $result = \Database::getInstance()->query('SELECT id,name FROM tl_metamodel ORDER BY name');

        while ($result->next()) {
            $values[$result->id] = $result->name;
        }

        return $values;
    }

} 
