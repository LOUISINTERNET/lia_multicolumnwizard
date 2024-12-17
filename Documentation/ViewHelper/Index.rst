.. _ViewHelper:
==========
ViewHelper
==========

H2 GetMultiColumnWizardValuesViewHelper
=======================================

This Viewhelper converts the json string to an array and returns it.

H3 Arguments
============

.. csv-table:: Arguments
  :header: "Argument", "Datatype" ,"Description" ,"Required" ,"Default"
  :widths: 20, 20, 20, 20, 20

  "json", "String", "The json string of the multicolumn field.", "false", "null"
  "associative", "Boolean", "If set to true you will get an associative array otherwise a stdclass object.", "false", "false"

H3 How to use
=============

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
