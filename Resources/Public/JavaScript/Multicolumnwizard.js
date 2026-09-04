import $ from "jquery";

class Multicolumnwizard {

  constructor() {
    this.initializeTrigger();
  }

  /**
   * Initialize triggers
   */
  initializeTrigger() {
    document.querySelectorAll('.ls-multicolumnwizard-wrapper').forEach(wrapper => {
      this.buildTable(wrapper);
    });

    const observer = new MutationObserver((mutationsList) => {
      mutationsList.forEach((mutation) => {
        if (mutation.type !== 'childList') return;

        mutation.addedNodes.forEach((node) => {
          if (node.nodeType !== Node.ELEMENT_NODE) return;

          const wrapper = node.querySelector('.ls-multicolumnwizard-wrapper');
          if (wrapper) this.buildTable(wrapper);
        });
      });
    });

    // panel-groups for new elements, -collapse for closed panels and the IRRE record
    // containers, so a child record added after page load initializes its wizards too
    const targetNodes = Array.from(document.querySelectorAll('.panel-collapse, .panel-group, [id $= "_records"]'));
    const config = { childList: true, subtree: false };
    targetNodes.forEach(targetNode => observer.observe(targetNode, config));

    // FormEngineValidation.initialize() drops every has-error class of the form and may
    // run after this module, so the wizards are re-validated whenever FormEngine has
    // validated a field of its own. validate() dispatches no such event itself.
    document.addEventListener('t3-formengine-postfieldvalidation', () => {
      document.querySelectorAll('.ls-multicolumnwizard-wrapper').forEach(wrapper => {
        this.validate($(wrapper));
      });
    });
  }

  /**
   *
   * @param {Element} node
   */
  buildTable(node) {
    const tableId = window.crypto.randomUUID();

    if (node instanceof HTMLElement && node.classList.contains('ls-multicolumnwizard-wrapper')) {
      const mcw = $(node);

      let _val = mcw.find('.jsonfield').val();
      let json = null;

      if (typeof _val === 'string') {
        try {
          json = JSON.parse(_val);
        } catch (error) {
          // Handle parse error if needed
        }
      }

      let _valLinkExplanation = mcw.find('.linkExplanation').val();
      let jsonLinkExplanation = null;

      if (typeof _valLinkExplanation === 'string') {
        try {
          jsonLinkExplanation = JSON.parse(_valLinkExplanation);
        } catch (error) {
          // Handle parse error if needed
        }
      }

      if (json && mcw.find('.ls-multicolumnwizard-row-container .ls-multicolumnwizard-row').length === 0) {
        $.each(json, (index, data) => {
          this.createNewRow(mcw, tableId, null, data, false, jsonLinkExplanation ? jsonLinkExplanation[index] : undefined);
        });
      }

      if (mcw.find('.ls-multicolumnwizard-row-container .ls-multicolumnwizard-row').length === 0) {
        this.createNewRow(mcw, tableId, null);
      }

      this.validate(mcw);
    }
  }

  /**
   * Functions that should be bound to the trigger button
   *
   * @param {jQuery} mcw Multicolumnwizard element
   * @param {string} tableID Unique identifier for the table
   * @param {jQuery} _element
   * @param {Object} data
   * @param {boolean} callSetJson
   */
  createNewRow(mcw, tableID, _element, data = {}, callSetJson = true, jsonLinkExplanation = []) {
    let newRow = mcw.find('.ls-multicolumnwizard-row.row-copy').clone();
    let fieldId = Number.parseInt(mcw.attr('data-field-id')) + 1;
    mcw.attr({ 'data-field-id': fieldId });
    newRow.attr({ 'style': '' });
    newRow.removeClass('row-copy');
    newRow.find('.btn-add').click((e) => {
      e.preventDefault();
      if (this.isAtMaxItems(mcw)) {
        return;
      }
      this.createNewRow(mcw, tableID, $(e.target));
    });
    newRow.find('.btn-delete').click((e) => {
      e.preventDefault();
      $(e.target).closest('.ls-multicolumnwizard-row').remove();
      this.setJsonData(mcw);
    });
    newRow.find('.btn-down').click((e) => {
      e.preventDefault();
      $(e.target).closest('.ls-multicolumnwizard-row').insertAfter($(e.target).closest('.ls-multicolumnwizard-row').next());
      this.setJsonData(mcw);
    });
    newRow.find('.bnt-up').click((e) => {
      e.preventDefault();
      $(e.target).closest('.ls-multicolumnwizard-row').insertBefore($(e.target).closest('.ls-multicolumnwizard-row').prev());
      this.setJsonData(mcw);
    });
    newRow.find('input[type="text"], textarea').blur(() => {
      this.setJsonData(mcw);
    });
    newRow.find('input[type="checkbox"], select').change(() => {
      this.setJsonData(mcw);
    });
    newRow.find('[id $= "____newid____"]').each(function () {
      $(this).attr({ 'id': $(this).attr('id').replace('____newid____', fieldId).replace('tableID', tableID) });
    });
    newRow.find('[for $= "____newid____"]').each(function () {
      $(this).attr({ 'for': $(this).attr('for').replace('____newid____', fieldId).replace('tableID', tableID) });
    });

    $.each(data, (index, value) => {
      if (newRow.find('input[type="text"][data-name="' + index + '"], input[type="hidden"][data-name="' + index + '"], select[data-name="' + index + '"], textarea[data-name="' + index + '"]').length) {
        newRow.find('[data-name="' + index + '"]').val(value);
      }
      if (newRow.find('input[type="checkbox"][data-name="' + index + '"]').length) {
        if (value == 1) {
          newRow.find('input[type="checkbox"][data-name="' + index + '"]').attr({ 'checked': 'checked' });
        }
      }
    });

    // The link wizard writes into a proxy field of the same record. Scope the lookup to
    // that record: an IRRE collection holds one proxy field per child, and an unscoped
    // selector would always resolve to the first child's field. mcw is used as the
    // anchor because newRow is still detached from the document at this point.
    const recordScope = mcw.closest('.form-irre-object');
    const proxyScope = recordScope.length ? recordScope : $(document);

    $(newRow).find('[data-linkusefield]').each(function () {
      var $linkTextField = $(this).closest('.formengine-field-item').find('input.t3js-form-field-link-input[data-ignore]');
      var fieldName = $(this).attr('data-fieldname');
      var $linkHiddenField = $(this).closest('.form-control-wrap').find('input[data-name]');
      var $originalLinkTextField = $(this).closest('.form-control-wrap').find('input[type="text"][data-formengine-input-initialized]');
      var $linkIcon = $(this).closest('.form-control-wrap').find('.mwc-add-link-icon');
      var $linkEditLinkButton = $(this).closest('.form-control-wrap').find('.mwc-add-link-edit-link-button');
      var $linkClearLinkButton = null;
      var $triggerLinkHiddenField = proxyScope.find('input[name $="[' + $(this).attr('data-linkusefield') + ']"]');
      var $triggerLinkInput = proxyScope.find('a[data-item-name$="[' + $(this).attr('data-linkusefield') + ']"]');

      if (jsonLinkExplanation) {
        if (jsonLinkExplanation[fieldName] && jsonLinkExplanation[fieldName]['text']) {
          $linkTextField.val(jsonLinkExplanation[fieldName]['text']);
          $linkEditLinkButton.removeClass('disabled');
          $linkEditLinkButton.removeAttr('disabled');
          $linkTextField.attr('readonly', 'readonly');
        } else {
          if ($linkHiddenField.val() != '') {
            $linkTextField.val($linkHiddenField.val());
            $linkEditLinkButton.removeClass('disabled');
            $linkEditLinkButton.removeAttr('disabled');
            $linkTextField.attr('readonly', 'readonly');
          }
        }
        if (jsonLinkExplanation[fieldName] && jsonLinkExplanation[fieldName]['icon']) {
          $linkIcon.html(jsonLinkExplanation[fieldName]['icon']);
        }
      }

      $triggerLinkInput.closest('.form-section').hide();
      var _this = this;
      $linkTextField.keyup(function () {
        if (!$linkClearLinkButton) {
          $linkTextField.after($linkTextField.closest('.input-group').find('.mwc-close'));
          $linkClearLinkButton = $linkTextField.closest('.form-control-clearable-wrapper').find('.mwc-close');
          $linkClearLinkButton.css('display', 'inline-block');
          $linkClearLinkButton.click(function (e) {
            e.preventDefault();
            $linkIcon.html('');
            $linkHiddenField.val('');
            $linkTextField.val('');
            $linkTextField.removeAttr('readonly');
            $linkClearLinkButton.css('visibility', 'hidden');
            $(this).closest('.form-control-wrap').addClass('has-change');
            multicolumnwizard.setJsonData(mcw);
          });
        }
        $linkClearLinkButton.css('visibility', 'visible');
        $linkHiddenField.val($(this).val());
        $(this).closest('.form-control-wrap').addClass('has-change');
        multicolumnwizard.setJsonData(mcw);
      });

      $linkEditLinkButton.click(function (e) {
        e.preventDefault();
        $linkTextField.removeAttr('readonly');
        if (!$linkClearLinkButton) {
          $linkTextField.after($linkTextField.closest('.input-group').find('.mwc-close'));
          $linkClearLinkButton = $linkTextField.closest('.form-control-clearable-wrapper').find('.mwc-close');
          $linkClearLinkButton.css('display', 'inline-block');
          $linkClearLinkButton.click(function (e) {
            e.preventDefault();
            $linkIcon.html('');
            $linkHiddenField.val('');
            $linkTextField.val('');
            $linkTextField.removeAttr('readonly');
            $linkClearLinkButton.css('visibility', 'hidden');
            $(this).closest('.form-control-wrap').addClass('has-change');
            multicolumnwizard.setJsonData(mcw);
          });
        }
        $linkClearLinkButton.css('visibility', 'visible');

        // Set original link text field value
        $originalLinkTextField.val($linkHiddenField.val());

        // Hide linkTextField and show originalLinkTextField
        $linkTextField.hide();
        $originalLinkTextField.show();
      });
      $(this).click(function (e) {
        e.preventDefault();
        if (!$triggerLinkInput.length) {
          console.warn('[MCW] Link proxy field not found for linkUseField "' + $(this).attr('data-linkusefield') + '". Ensure the field is in showitem.');
          return;
        }
        $triggerLinkHiddenField.val($linkHiddenField.val());
        $triggerLinkHiddenField.off('change');
        $triggerLinkHiddenField.on('change', function () {
          $linkHiddenField.val($triggerLinkHiddenField.val());
          $linkEditLinkButton.addClass('disabled');
          $linkEditLinkButton.attr('disabled', 'disabled');
          $linkIcon.html('');
          $linkTextField.val($triggerLinkHiddenField.val()).addClass('has-change');
          if (!$linkClearLinkButton) {
            $linkTextField.after($linkTextField.closest('.input-group').find('.mwc-close'));
            $linkClearLinkButton = $linkTextField.closest('.form-control-clearable-wrapper').find('.mwc-close');
            $linkClearLinkButton.css('display', 'inline-block');
            $linkClearLinkButton.click(function (e) {
              e.preventDefault();
              $linkIcon.html('');
              $linkHiddenField.val('');
              $linkTextField.val('');
              $linkTextField.removeAttr('readonly');
              $linkClearLinkButton.css('visibility', 'hidden');
              $(this).closest('.form-control-wrap').addClass('has-change');
              multicolumnwizard.setJsonData(mcw);
            });
          }
          $(this).closest('.form-control-wrap').addClass('has-change');
          $linkClearLinkButton.css('visibility', 'visible');
          $linkTextField.removeAttr('readonly');
          $triggerLinkHiddenField.off('change');
          $triggerLinkHiddenField.val('');
          multicolumnwizard.setJsonData(mcw);
        });
        $triggerLinkInput[0].click();
      });
    });

    if (_element == null) {
      mcw.find('.ls-multicolumnwizard-row-container').append(newRow);
    } else {
      _element.closest('.ls-multicolumnwizard-row').after(newRow);
    }
    if (callSetJson) {
      this.setJsonData(mcw);
    }
  }

  /**
   * All rows currently held by the wizard.
   *
   * @param {jQuery} mcw Multicolumnwizard element
   * @returns {jQuery} The row elements
   */
  getRows(mcw) {
    return mcw.find('.ls-multicolumnwizard-row-container .ls-multicolumnwizard-row');
  }

  /**
   * The minitems/maxitems limits configured in TCA. 0 means "no limit".
   *
   * @param {jQuery} mcw Multicolumnwizard element
   * @returns {{min: number, max: number}} The limits
   */
  getLimits(mcw) {
    return {
      min: Number.parseInt(mcw.attr('data-minitems'), 10) || 0,
      max: Number.parseInt(mcw.attr('data-maxitems'), 10) || 0,
    };
  }

  /**
   * @param {jQuery} mcw Multicolumnwizard element
   * @returns {boolean} Whether another row would exceed maxitems
   */
  isAtMaxItems(mcw) {
    const max = this.getLimits(mcw).max;
    return max > 0 && this.getRows(mcw).length >= max;
  }

  /**
   * Counts the rows that hold at least one value.
   *
   * The wizard always keeps one row on screen, so a single untouched row means
   * "no items" - which is why minitems is measured against filled rows only.
   *
   * @param {jQuery} mcw Multicolumnwizard element
   * @returns {number} The number of filled rows
   */
  countFilledRows(mcw) {
    let filledRows = 0;
    this.getRows(mcw).each(function () {
      const isFilled = $(this).find('select, input, textarea').toArray().some((field) => {
        const $field = $(field);
        if ($field.attr('data-ignore')) {
          return false;
        }
        if ($field.attr('type') === 'checkbox') {
          return $field.is(':checked');
        }
        return String($field.val() ?? '').trim() !== '';
      });
      if (isFilled) {
        filledRows += 1;
      }
    });

    return filledRows;
  }

  /**
   * Flags fields that stay below their configured minlength.
   *
   * FormEngine submits the form from JavaScript, so the browser never runs its own
   * minlength check - it is folded into the same has-error gate as the row limits.
   *
   * @param {jQuery} mcw Multicolumnwizard element
   * @returns {boolean} Whether any field violates its minlength
   */
  validateFieldLengths(mcw) {
    let violated = false;
    this.getRows(mcw).find('input[minlength], textarea[minlength]').each(function () {
      const minLength = Number.parseInt($(this).attr('minlength'), 10) || 0;
      const value = String($(this).val() ?? '');
      const isInvalid = value !== '' && value.length < minLength;
      $(this).toggleClass('has-error', isInvalid);
      violated = violated || isInvalid;
    });

    return violated;
  }

  /**
   * Enforces minitems, maxitems and the column length options.
   *
   * The add button is disabled at maxitems, and violations mark the json field with
   * has-error - the class FormEngineValidation.isValid() looks for, which blocks the
   * save and raises the standard "fields missing" modal.
   *
   * @param {jQuery} mcw Multicolumnwizard element
   */
  validate(mcw) {
    const limits = this.getLimits(mcw);
    const isAtMaxItems = this.isAtMaxItems(mcw);

    this.getRows(mcw).find('.btn-add')
      .toggleClass('disabled', isAtMaxItems)
      .attr('aria-disabled', isAtMaxItems ? 'true' : null);

    const hasTooManyRows = limits.max > 0 && this.getRows(mcw).length > limits.max;
    const hasTooFewRows = limits.min > 0 && this.countFilledRows(mcw) < limits.min;
    const isInvalid = hasTooManyRows || hasTooFewRows || this.validateFieldLengths(mcw);

    mcw.find('.jsonfield')
      .toggleClass('has-error', isInvalid)
      .attr('aria-invalid', String(isInvalid));
    mcw.closest('.t3js-formengine-validation-marker')
      .find('.t3js-formengine-label')
      .toggleClass('has-error', isInvalid);
  }

  setJsonData(mcw) {
    var sessionData = [];
    mcw.find('.ls-multicolumnwizard-row-container .ls-multicolumnwizard-row').each(function () {
      var sessionRow = {};
      $(this).find('select, input, textarea').each(function () {
        if (!$(this).attr('data-ignore')) {
          if ($(this).attr('type') == 'checkbox') {
            sessionRow[$(this).attr('data-name')] = ($(this).is(':checked') ? 1 : 0);
          } else {
            sessionRow[$(this).attr('data-name')] = $(this).val();
          }
        }
      });
      sessionData.push(sessionRow);
    });
    mcw.find('.jsonfield').val(JSON.stringify(sessionData));
    this.validate(mcw);
  }
}

const multicolumnwizard = new Multicolumnwizard;
export default multicolumnwizard
