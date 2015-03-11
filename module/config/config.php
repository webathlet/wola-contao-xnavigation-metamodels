<?php

/*
 * Event listener
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Contao\XNavigation\MetaModels\MetaModelsProviderFactory';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Contao\XNavigation\MetaModels\MetaModelsConditionFactory';

/*
 * XNavigation provider
 */
$GLOBALS['XNAVIGATION_PROVIDER']['metamodels'] = 'Netzmacht\Contao\XNavigation\MetaModels\Provider\MetaModelsProvider';

/*
 * XNavigation condition
 */
$GLOBALS['XNAVIGATION_CONDITION']['metamodels'] = 'Netzmacht\Contao\XNavigation\MetaModels\Condition\MetaModelsCondition';
$GLOBALS['XNAVIGATION_CONDITION']['metamodels_attribute'] = 'Netzmacht\Contao\XNavigation\MetaModels\Condition\MetaModelsAttributeCondition';

