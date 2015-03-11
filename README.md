
MetaModels integration for contao-xnavigation
=====

This extension integrates [MetaModels](now.metamodel.me) into the highly flexibile extended navigation extension 
[contao-xnavigation](https://github.com/bit3/contao-xnavigation/). 

It provides a new item provider and new conditions to work with MetaModels.

Installation
---------

* Install the composer plugin for Contao
* Install netzmacht/contao-xnavigation-metamodels


Usage
----------

* Create a render setting for the metamodel. It will be used to render the navigation link.
* **Important**: 
  * A jump to page is required to generate the url
  * Choose one of the template `metamodel_xnav_item` or `metamodel_xnav_item_plain`.
  They only render one item istead of an item list
* Create a new item provider for the metamodel
* Choose the parent page and the created render setting.
* Add the created provider to you menu
* Adjust the used item condition, so that items of type metamodels are included `item.type == metamodels`

That's it.
