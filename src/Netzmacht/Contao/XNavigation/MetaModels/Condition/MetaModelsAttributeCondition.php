<?php

namespace Netzmacht\Contao\XNavigation\MetaModels\Condition;


use Bit3\FlexiTree\Condition\ConditionInterface;
use Bit3\FlexiTree\ItemInterface;
use MetaModels\IItem;


class MetaModelsAttributeCondition implements ConditionInterface
{
    const OPERATOR_EQUALS = 'eq';
    const OPERATOR_GREATER_THAN = 'gt';
    const OPERATOR_LESSER_THAN = 'lt';
    const OPERATOR_EQUALS_OR_LESSER_THAN = 'lte';
    const OPERATOR_EQUALS_OR_GREATER_THAN = 'gte';

    /**
     * @var int
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $operator = '=';

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return int
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param int $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;

    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item)
    {
        if($item->getType() != 'metamodels') {
            return true;
        }

        /** @var IItem $model */
        $model     = $item->getExtra('model');
        $attribute = $model->getAttribute($this->attribute);

        // metamoel does not have this attribute, skip
        if(!$attribute) {
            return true;
        }

        $value = $model->get($this->attribute);

        switch($this->operator) {
            case static::OPERATOR_EQUALS:
                return $value == $this->value;
                break;

            case static::OPERATOR_EQUALS_OR_GREATER_THAN:
                return $value >= $this->value;
                break;

            case static::OPERATOR_EQUALS_OR_LESSER_THAN:
                return $value <= $this->value;
                break;

            case static::OPERATOR_GREATER_THAN:
                return $value > $this->value;
                break;

            case static::OPERATOR_LESSER_THAN:
                return $value < $this->value;
                break;
        }

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function describe()
    {
        $operations = array(
            static::OPERATOR_EQUALS => '==',
            static::OPERATOR_GREATER_THAN => '>',
            static::OPERATOR_LESSER_THAN => '<',
            static::OPERATOR_EQUALS_OR_GREATER_THAN => '>=',
            static::OPERATOR_EQUALS_OR_LESSER_THAN => '>=',
        );

        if (isset($operations[$this->operator])) {
            $operator = $operations[$this->operator];
        }
        else {
            $operator = $this->operator;
        }

        return sprintf('metamodels.attribute[%s] %s %s',
            $this->attribute,
            $operator,
            $this->value
        );
    }

} 