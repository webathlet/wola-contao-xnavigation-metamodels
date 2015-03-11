<?php

namespace Netzmacht\Contao\XNavigation\MetaModels\DataContainer;

use MetaModels\Factory;

class XNavigationProviderDataContainer
{

    /**
     * @var Factory
     */
    protected $metaModelsFactory;


    /**
     * Construct
     */
    public function __construct()
    {
        $this->metaModelsFactory = new Factory();
    }


    /**
     * Get all Metamodels table names
     *
     * @return array|\string[]
     */
    public function getMetaModels()
    {
        $values = array();
        $result = \Database::getInstance()->query('SELECT id,name FROM tl_metamodel ORDER BY name');

        while ($result->next()) {
            $values[$result->id] = $result->name;
        }

        return $values;    }


    /**
     * @param \DataContainer $dataContainer
     * @return array
     */
    public function getAttributeNames($dataContainer)
    {
        $options = array();

        if ($dataContainer->activeRecord->mm_metamodel) {
            $metaModel  = $this->metaModelsFactory->byId($dataContainer->activeRecord->mm_metamodel);

            if($metaModel) {
                $attributes = $metaModel->getAttributes();

                foreach($attributes as $name => $attribute) {
                    $options[$attribute->get('id')] = $attribute->getName();
                }
            }
        }

        return $options;
    }


    /**
     * @param \DataContainer $dataContainer
     * @return array
     */
    public function getFilterNames(\DataContainer $dataContainer)
    {
        $database       = \Database::getInstance();
        $values         = array();
        $filterSettings = $database
            ->prepare('SELECT * FROM tl_metamodel_filter WHERE pid=? ORDER BY name')
            ->execute($dataContainer->activeRecord->mm_metamodel);

        while ($filterSettings->next()) {
            $values[$filterSettings->id] = $filterSettings->name;
        }

        return $values;
    }


    /**
     * @param \DataContainer $dataContainer
     * @return array
     */
    public function getRenderSettings(\DataContainer $dataContainer)
    {
        $database       = \Database::getInstance();
        $values         = array();
        $filterSettings = $database
            ->prepare('SELECT * FROM tl_metamodel_rendersettings WHERE pid=? ORDER BY name')
            ->execute($dataContainer->activeRecord->mm_metamodel);

        while ($filterSettings->next()) {
            $values[$filterSettings->id] = $filterSettings->name;
        }

        return $values;
    }

} 
