<?php


namespace Netzmacht\Contao\XNavigation\MetaModels\Provider;

use Bit3\Contao\XNavigation\XNavigationEvents;
use Bit3\FlexiTree\Event\CollectItemsEvent;
use Bit3\FlexiTree\Event\CreateItemEvent;
use Bit3\FlexiTree\ItemInterface;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\Setting\ICollection as MetaModelsFilterCollection;
use MetaModels\Filter\Setting\Factory as MetaModelsFilterFactory;
use MetaModels\IItem;
use MetaModels\IMetaModel;
use MetaModels\Render\Setting\ICollection as MetaModelsRenderSetting;
use MetaModels\Render\Template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MetaModelsProvider extends \Controller implements EventSubscriberInterface
{
    const ATTRIBUTE_TYPE_LABEL = 'label';
    const ATTRIBUTE_TYPE_LINK  = 'link';
    const ATTRIBUTE_TYPE_ITEM  = 'text';

    /**
     * @var IMetaModel
     */
    protected $metaModel;

    /**
     * @var MetaModelsFilterCollection
     */
    protected $filter;

    /**
     * @var string
     */
    protected $sortBy;

    /**
     * @var string
     */
    protected $parentType;

    /**
     * @var int|string
     */
    protected $parentName;

    /**
     * @var string
     */
    protected $sortDirection = 'ASC';

    /**
     * @var array
     */
    protected $filterParams = array();

    /**
     * @var MetaModelsRenderSetting
     */
    protected $renderSetting;

    /**
     * @var int
     */
    protected $providerId;


    /**
     * @var array
     */
    protected $attributeMapping = array(
        MetaModelsProvider::ATTRIBUTE_TYPE_ITEM  => array(),
        MetaModelsProvider::ATTRIBUTE_TYPE_LINK  => array(),
        MetaModelsProvider::ATTRIBUTE_TYPE_LABEL => array(),
    );

    /**
     * @var array|IItem[]
     */
    protected static $cache = array();


    /**
     * @param IMetaModel $metaModel
     * @param $providerId
     */
    public function __construct(IMetaModel $metaModel, $providerId)
    {
        parent::__construct();

        $this->metaModel  = $metaModel;
        $this->providerId = $providerId;
    }

    /**
     * @param IMetaModel $metaModel
     * @param $providerId
     * @return MetaModelsProvider
     */
    public static function create(IMetaModel $metaModel, $providerId)
    {
        $provider = new static($metaModel, $providerId);

        return $provider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            XNavigationEvents::CREATE_ITEM   => 'createItem',
            XNavigationEvents::COLLECT_ITEMS => array('collectItems', 100),
        );
    }

    /**
     * @return MetaModelsFilterCollection
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param MetaModelsFilterCollection $filter
     * @param $params
     * @return $this
     */
    public function setFilter(MetaModelsFilterCollection $filter, $params=array())
    {
        $this->filter       = $filter;
        $this->filterParams = $params;

        return $this;
    }

    /**
     * @return IMetaModel
     */
    public function getMetaModel()
    {
        return $this->metaModel;
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param $sortBy
     * @param string $sortDirection
     * @return $this
     */
    public function setSorting($sortBy, $sortDirection='ASC')
    {
        $this->sortBy        = $sortBy;
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @param int|string $type
     * @param $name
     * @return $this
     */
    public function setParent($type, $name)
    {
        $this->parentType = $type;
        $this->parentName = $name;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     * @return string
     */
    public function getParentType()
    {
        return $this->parentType;
    }

    /**
     * @return MetaModelsRenderSetting
     */
    public function getRenderSetting()
    {
        return $this->renderSetting;
    }

    /**
     * @param MetaModelsRenderSetting $renderSetting
     * @return $this
     */
    public function setRenderSetting(MetaModelsRenderSetting $renderSetting)
    {
        $this->renderSetting = $renderSetting;

        return $this;
    }

    /**
     * @param $attributeId
     * @param $htmlAttribute
     * @param string $attributeType
     * @param string $outputFormat
     *
     * @return $this
     */
    public function addAttributeMapping(
        $attributeId,
        $htmlAttribute,
        $attributeType = MetaModelsProvider::ATTRIBUTE_TYPE_ITEM,
        $outputFormat = 'text'
    ) {
        $attribute = $this->metaModel->getAttributeById($attributeId);

        if($attribute) {
            $this->attributeMapping[$attributeType][$htmlAttribute] = array(
                'attribute' => $attribute,
                'format'    => $outputFormat
            );
        }

        return $this;
    }

    /**
     * Collect all metamodels items and create navigation items.
     * @param CollectItemsEvent $event
     */
    public function collectItems(CollectItemsEvent $event)
    {
        $item = $event->getParentItem();

        // match pointing point
        if($item->getType() != $this->getParentType() || $item->getName() != $this->getParentName()) {
            return;
        }

        $collection = $this->fetchMetaModelsItems();
        $factory    = $event->getFactory();

        foreach($collection as $model) {
            $table = $model->getMetaModel()->getTableName();
            $name  = sprintf('%s::%s::%s', $table, $model->get('id'), $this->providerId);
            static::$cache[$name] = $model;
            
            $factory->createItem('metamodels', $name, $item);
        }
    }

    /**
     * Create a navigation item for a metamodel item
     * @param CreateItemEvent $event
     */
    public function createItem(CreateItemEvent $event)
    {
        $item = $event->getItem();
        $name = $item->getName();

        if($item->getType() != 'metamodels') {
            return;
        }

        $model = $this->loadModel($name);

        if(!$model) {
            return;
        }

        $value = $model->parseValue('text', $this->renderSetting);
        $uri   = $value['jumpTo']['url'];

        $item
            ->setLabel($this->generateLabel($model))
            ->setExtra('model', $model)
            ->setExtra('value', $value)
            ->setExtra('provider', $this->providerId)
            ->setUri($uri);

        if($value['class']) {
            $item->setLabelAttribute('class', $value['class']);
        }

        $this->renderAttributeMapping($item, $value);

        if($uri == \Environment::get('request')) {
            $item->setCurrent(true);
            $item->getParent()
                ->setTrail(true)
                ->setCurrent(false);
        }
    }


    /**
     * @return \MetaModels\IItems
     */
    public function fetchMetaModelsItems()
    {
        $filter = $this->metaModel->getEmptyFilter();

        if ($this->filter) {
            $this->filter->addRules($filter, array());
        }

        $sortBy = '';

        if($this->sortBy) {
            $attribute = $this->metaModel->getAttributeById($this->sortBy);

            if($attribute) {
                $sortBy = $attribute->getColName();
            }
        }

        // TODO: Should we limit the attributes? What about translated ones? Con: Conditions need access to other attributes
        return $this->metaModel->findByFilter($filter, $sortBy, 0, 0, $this->sortDirection /*, $this->getAttributeNames()*/);
    }


    /**
     * @param $model
     * @return string
     */
    protected function generateLabel(IItem $model)
    {
        $templateName = $this->renderSetting->get('template');
        $format       = $this->getOutputFormat();

        $data         = array(
            'settings' => $this->renderSetting,
            'item'     => $model->parseValue($format, $this->renderSetting),
        );

        return Template::render($templateName, $format, $data);
    }

    /**
     * @param $name
     * @return IItem|null
     */
    protected function loadModel($name)
    {
        list($table, $id, $providerId) = explode('::', $name, 3);

        if ($providerId != $this->providerId) {
            // prevent that item for which this provider is not responsible will handle the model
            return null;
        }

        if (isset(static::$cache[$name])) {
            return static::$cache[$name];
        }

        if ($table != $this->metaModel->getTableName()) {
            return null;
        }

        return $this->metaModel->findById($id);
    }

    /**
     * @return mixed|null|string
     */
    protected function getOutputFormat()
    {
        $format = $this->renderSetting->get('format');

        if ($format) {
            return $format;
        }

        if (TL_MODE == 'FE' && is_object($GLOBALS['objPage']) && $GLOBALS['objPage']->outputFormat) {
            return $GLOBALS['objPage']->outputFormat;
        }

        return 'text';
    }
    
    /**
     * @param ItemInterface $item
     * @param array $values
     */
    protected function renderAttributeMapping(ItemInterface $item, array $values)
    {
        foreach ($this->attributeMapping[static::ATTRIBUTE_TYPE_LABEL] as $name => $mapping) {
            /** @var IAttribute $attribute */
            $attribute = $mapping['attribute'];
            $colName   = $attribute->getColName();

            $item->setLabelAttribute($name, specialchars($values[$mapping['format']][$colName]));
        }

        foreach ($this->attributeMapping[static::ATTRIBUTE_TYPE_LINK] as $name => $mapping) {
            /** @var IAttribute $attribute */
            $attribute = $mapping['attribute'];
            $colName   = $attribute->getColName();

            $item->setLinkAttribute($name, specialchars($values[$mapping['format']][$colName]));
        }

        foreach ($this->attributeMapping[static::ATTRIBUTE_TYPE_ITEM] as $name => $mapping) {
            /** @var IAttribute $attribute */
            $attribute = $mapping['attribute'];
            $colName   = $attribute->getColName();

            $item->setAttribute($name, specialchars($values[$mapping['format']][$colName]));
        }
    }

    /**
     * Return all attributes that shall be fetched from the MetaModel.
     *
     * In this base implementation, this only includes the attributes mentioned in the render setting.
     *
     * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
     * @copyright  The MetaModels team.
     * @see        MetaModels::getAttributeNames
     * @return     string[] the names of the attributes to be fetched.
     */
    protected function getAttributeNames()
    {
        $arrAttributes = $this->renderSetting->getSettingNames();

        // Get the right jumpTo.
        $desiredLanguage  = $this->getMetaModel()->getActiveLanguage();
        $strFallbackLanguage = $this->getMetaModel()->getFallbackLanguage();

        $filterSetting = 0;

        foreach ((array)$this->renderSetting->get('jumpTo') as $jumpTo) {
            // If either desired language or fallback, keep the result.
            if (!$this->getMetaModel()->isTranslated()
                || $jumpTo['langcode'] == $desiredLanguage
                || $jumpTo['langcode'] == $strFallbackLanguage) {
                $filterSetting = $jumpTo['filter'];
                // If the desired language, break. Otherwise try to get the desired one until all have been evaluated.
                if ($desiredLanguage == $jumpTo['langcode']) {
                    break;
                }
            }
        }

        if ($filterSetting) {
            $objFilterSettings = MetaModelsFilterFactory::byId($filterSetting);
            $arrAttributes     = array_merge($objFilterSettings->getReferencedAttributes(), $arrAttributes);
        }

        return $arrAttributes;
    }
} 