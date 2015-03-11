<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 29.09.14
 * Time: 10:13
 */

namespace Netzmacht\Contao\XNavigation\MetaModels;


use Bit3\Contao\XNavigation\Event\CreateConditionEvent;
use Bit3\FlexiTree\Condition\NotCondition;
use MetaModels\Factory;
use Netzmacht\Contao\XNavigation\MetaModels\Condition\MetaModelsAttributeCondition;
use Netzmacht\Contao\XNavigation\MetaModels\Condition\MetaModelsCondition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MetaModelsConditionFactory implements EventSubscriberInterface
{
    /**
     * @{inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            EVENT_XNAVIGATION_CREATE_CONDITION => array('createCondition', -100)
        );
    }


    public function createCondition(CreateConditionEvent $event)
    {
        $model = $event->getConditionModel();

        if ($model->type == 'metamodels_attribute') {
            $condition = new MetaModelsAttributeCondition();
            $condition
                ->setAttribute($model->mm_attribute)
                ->setOperator($model->mm_operator)
                ->setValue($model->mm_value);
        }
        elseif ($model->type == 'metamodels') {
            $condition = new MetaModelsCondition();

            $metaModel = Factory::byId($model->mm_metamodel);

            if ($metaModel) {
               $condition->setMetaModel($metaModel);
            }
        }

        if (isset($condition)) {
            if($model->invert) {
                $condition = new NotCondition($condition);
            }

            $event->setCondition($condition);
        }
    }


} 