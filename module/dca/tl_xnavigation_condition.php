<?php

$GLOBALS['TL_DCA']['tl_xnavigation_condition']['metapalettes']['metamodels_attribute extends default'] = array(
    '+condition' => array('mm_attribute', 'mm_operator', 'mm_value', 'invert'),
);

$GLOBALS['TL_DCA']['tl_xnavigation_condition']['metapalettes']['metamodels extends default'] = array(
    '+condition' => array('mm_metamodel', 'invert'),
);

$GLOBALS['TL_DCA']['tl_xnavigation_condition']['fields']['mm_operator'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_xnavigation_condition']['mm_operator'],
    'inputType' => 'select',
    'options'   => array(
        'eq'  => '=',
        'lte' => '<=',
        'gte' => '>=',
        'lt'  => '<',
        'gt'  => '>'
    ),
    'eval'      => array(
        'mandatory'          => true,
        'tl_class'           => 'w50',
        'includeBlankOption' => true,
    ),
    'sql'       => "char(4) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_xnavigation_condition']['fields']['mm_attribute'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_xnavigation_condition']['mm_attribute'],
    'inputType' => 'text',
    'eval'      => array(
        'mandatory' => true,
        'tl_class'  => 'w50',
    ),
    'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_xnavigation_condition']['fields']['mm_value'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_xnavigation_condition']['mm_value'],
    'inputType' => 'text',
    'eval'      => array(
        'tl_class'  => 'w50',
    ),
    'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_xnavigation_condition']['fields']['mm_metamodel'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_xnavigation_condition']['mm_metamodel'],
    'inputType' => 'select',
    'options_callback' => array(
        'Netzmacht\Contao\XNavigation\MetaModels\DataContainer\XNavigationConditionDataContainer',
        'getMetaModels',
    ),
    'eval'      => array(
        'tl_class'  => 'w50',
        'chosen'    => true,
        'mandatory' => true,
    ),
    'sql'       => "int(11) unsigned NOT NULL default '0'"
);