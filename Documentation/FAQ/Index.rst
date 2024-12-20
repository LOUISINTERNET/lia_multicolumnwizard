.. _FAQ:
.. index:: Extension manuals; FAQ
.. _faq-for-extension-authors:

===
FAQ
===

ViewHelper
========

.. rst-class:: panel panel-default

The ViewHelper doesn't work / can't be found
============================================

In order to use the ViewHelpers of the MultiColumnWizard you need to import the namespace.

..  tabs::
    ..  group-tab:: Tag-Attribute
        Import the namespace in the begining html-Tag of your Template like this:

        .. code-block:: html

            <div  xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
                    xmlns:mcw="http://typo3.org/ns/LIA/LiaMulticolumnwizard/ViewHelpers"
                    data-namespace-typo3-fluid="true"
            >

    ..  group-tab:: Inline
        Use this line in your template before using any MulticolumnWizard-ViewHelper:

        .. code-block:: typoscript

          {namespace mcw=LIA/LiaMulticolumnwizard/ViewHelpers}

See also `Import ViewHelper namespaces<https://docs.typo3.org/permalink/t3coreapi:fluid-syntax-viewhelpers-import-namespaces>`__
