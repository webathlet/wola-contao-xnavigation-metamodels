<?php

namespace Netzmacht\Contao\XNavigation\MetaModels\Condition;


use Bit3\FlexiTree\Condition\ConditionInterface;
use Bit3\FlexiTree\ItemInterface;
use MetaModels\IItem;
use MetaModels\IMetaModel;

class MetaModelsCondition implements ConditionInterface
{
    /**
     * @var IMetaModel
     */
    private $metaModel;

    /**
     * @return IMetaModel
     */
    public function getMetaModel()
    {
        return $this->metaModel;
    }

    /**
     * @param IMetaModel $metaModel
     * @return $this
     */
    public function setMetaModel(IMetaModel $metaModel)
    {
        $this->metaModel = $metaModel;

        return $this;
    }

    /**
     * Determine if the condition match on the item.
     *
     * @param ItemInterface $item
     *
     * @return bool
     */
    public function matchItem(ItemInterface $item)
    {
        if($item->getType() != 'metamodels') {
            return true;
        }

        /** @var IItem $model */
        $model = $item->getExtra('model');

        return ($model->getMetaModel() == $this->metaModel);
    }


    /**
     * Return a string that describe the condition in a human readable way.
     *
     * @return string
     */
    public function describe()
    {
        if($this->metaModel) {
            return 'metamodel.table == ' . $this->metaModel->getTableName();
        }
        else {
            return 'metamodel.table == ?';
        }
    }
}