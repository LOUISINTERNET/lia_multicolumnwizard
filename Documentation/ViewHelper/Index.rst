.. _ViewHelper:

==========
ViewHelper
==========

GetMultiColumnWizardValuesViewHelper
=======================================

This Viewhelper converts the json string to an array and returns it.

Arguments
---------

.. csv-table:: Arguments
  :header: "Argument", "Datatype" ,"Description" ,"Required" ,"Default"
  :widths: 20, 20, 20, 20, 20

  "json", "String", "The json string of the multicolumn field.", "false", "null"
  "associative", "Boolean", "If set to true you will get an associative array otherwise a stdclass object.", "false", "false"

How to use
----------

You have to save the return of this viewhelper into a varible. Make sure that the namespace is loaded
in the Tempalte where you want to use the viewhelpers of this extension.

.. code-block:: html
  :caption: Register namespace

  <div  xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
        xmlns:mcw="http://typo3.org/ns/LIA/LiaMulticolumnwizard/ViewHelpers"
        data-namespace-typo3-fluid="true"
  >

.. code-block:: html
  :caption: Convert the multicolumnwizard json strint into an array

  <f:variable name="mcwArray" value="{mcw:getMultiColumnWizardValues(json: '{yourData.yourField}', associative: true)}" />

GetSelectValuesViewHelper
========================

This ViewHelper call a function with given parameter.

.. csv-table:: Arguments
  :header: "Argument", "Datatype" ,"Description" ,"Required" ,"Default"
  :widths: 20, 20, 20, 20, 20

  "configuration", "String", "The json string of the multicolumn field.", "false", "null"
  "optionsFunction", "Array", "This array has to contain the full classname, the method to call and all the parameter that are needed to call this method.", "false", "null"

IsEmptyViewHelper
=================

This ViewHelper check if the given json string contains only empty entries and returns a true if every item is empty and false if just one of the items is not empty.


.. csv-table:: Arguments
  :header: "Argument", "Datatype" ,"Description" ,"Required" ,"Default"
  :widths: 20, 20, 20, 20, 20

  "json", "String", "The json string generated from the multicolumnwizard.", "true", ""
