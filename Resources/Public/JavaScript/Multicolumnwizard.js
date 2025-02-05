import $ from "jquery";

class Multicolumnwizard {

    constructor() {
        this.initializeTrigger();
    }

    /**
     * Initialize triggers
     */
    initializeTrigger() {
      this.buildTable(document.querySelector('.ls-multicolumnwizard-wrapper'));

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

      // panel-groups for new elements and -collapse for closed panels
      const targetNodes = Array.from(document.querySelectorAll('.panel-collapse, .panel-group'));
      const config = { childList: true, subtree: false };
      targetNodes.forEach(targetNode => observer.observe(targetNode, config));
    }

    /**
     *
     * @param {Element} node
     */
    buildTable(node) {
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
            this.createNewRow(mcw, null, data, false, jsonLinkExplanation ? jsonLinkExplanation[index] : undefined);
          });
        }

        if (mcw.find('.ls-multicolumnwizard-row-container .ls-multicolumnwizard-row').length === 0) {
          this.createNewRow(mcw, null);
        }
      }
    }

    /**
     * Functions that should be bound to the trigger button
     *
     * @param {jQuery} mcw Multicolumnwizard element
     * @param {jQuery} _element
     * @param {Object} data
     * @param {boolean} callSetJson
     */
    createNewRow(mcw, _element, data = {}, callSetJson = true, jsonLinkExplanation = []) {
        let newRow = mcw.find('.ls-multicolumnwizard-row.row-copy').clone();
        let fieldId = Number.parseInt(mcw.attr('data-field-id')) + 1;
        mcw.attr({'data-field-id': fieldId});
        newRow.attr({'style': ''});
        newRow.removeClass('row-copy');
        newRow.find('.btn-add').click((e) => {
            e.preventDefault();
            this.createNewRow(mcw, $(e.target));
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
            $(this).attr({'id': $(this).attr('id').replace('____newid____', fieldId)});
        });
        newRow.find('[for $= "____newid____"]').each(function () {
            $(this).attr({'for': $(this).attr('for').replace('____newid____', fieldId)});
        });

        $.each(data, (index, value) => {
            if (newRow.find('input[type="text"][data-name="' + index + '"], select[data-name="' + index + '"], textarea[data-name="' + index + '"]').length) {
                newRow.find('[data-name="' + index + '"]').val(value);
            }
            if (newRow.find('input[type="checkbox"][data-name="' + index + '"]').length) {
                if (value == 1) {
                    newRow.find('input[type="checkbox"][data-name="' + index + '"]').attr({'checked': 'checked'});
                }
            }
        });

        $(newRow).find('[data-linkusefield]').each(function(){
            var $linkTextField = $(this).closest('.formengine-field-item').find('input[data-ignore]');
            var fieldName = $(this).attr('data-fieldname');
            var $linkHiddenField = $(this).closest('.form-control-wrap').find('input[type="text"][data-name]');
            var $linkIcon = $(this).closest('.form-control-wrap').find('.mwc-add-link-icon');
            var $linkEditLinkButton = $(this).closest('.form-control-wrap').find('.mwc-add-link-edit-link-button');
            var $linkClearLinkButton = null;
            var $triggerLinkHiddenField = $('input[name $="[' + $(this).attr('data-linkusefield') + ']"]');
            var $triggerLinkInput = $('a[data-item-name$="[' + $(this).attr('data-linkusefield') + ']"]');

            if(jsonLinkExplanation) {
                if(jsonLinkExplanation[fieldName] && jsonLinkExplanation[fieldName]['text']) {
                    $linkTextField.val(jsonLinkExplanation[fieldName]['text']);
                    $linkEditLinkButton.removeClass('disabled');
                    $linkEditLinkButton.removeAttr('disabled');
                    $linkTextField.attr('readonly','readonly');
                } else {
                    if($linkHiddenField.val() != '') {
                        $linkTextField.val($linkHiddenField.val());
                        $linkEditLinkButton.removeClass('disabled');
                        $linkEditLinkButton.removeAttr('disabled');
                        $linkTextField.attr('readonly','readonly');
                    }
                }
                if(jsonLinkExplanation[fieldName] && jsonLinkExplanation[fieldName]['icon']) {
                    $linkIcon.html(jsonLinkExplanation[fieldName]['icon']);
                }
            }

            $triggerLinkInput.closest('.form-section').hide();
            var _this = this;
            $linkTextField.keyup(function(){
                if(!$linkClearLinkButton) {
                    $linkTextField.after($linkTextField.closest('.input-group').find('.mwc-close'));
                    $linkClearLinkButton = $linkTextField.closest('.form-control-clearable-wrapper').find('.mwc-close');
                    $linkClearLinkButton.css('display', 'inline-block');
                    $linkClearLinkButton.click(function(e){
                        e.preventDefault();
                        $linkIcon.html('');
                        $linkHiddenField.val('');
                        $linkTextField.val('');
                        $linkTextField.removeAttr('readonly');
                        $linkClearLinkButton.css('visibility', 'hidden');
                        //$(this).closest('.form-control-wrap').addClass('has-change');
                        multicolumnwizard.setJsonData(mcw);
                    });
                }
                $linkClearLinkButton.css('visibility', 'visible');
                $linkHiddenField.val($(this).val());
                //$(this).closest('.form-control-wrap').addClass('has-change');
                multicolumnwizard.setJsonData(mcw);
            });

            $linkEditLinkButton.click(function(e){
                e.preventDefault();
                $linkTextField.removeAttr('readonly');
                if(!$linkClearLinkButton) {
                    $linkTextField.after($linkTextField.closest('.input-group').find('.mwc-close'));
                    $linkClearLinkButton = $linkTextField.closest('.form-control-clearable-wrapper').find('.mwc-close');
                    $linkClearLinkButton.css('display', 'inline-block');
                    $linkClearLinkButton.click(function(e){
                        e.preventDefault();
                        $linkIcon.html('');
                        $linkHiddenField.val('');
                        $linkTextField.val('');
                        $linkTextField.removeAttr('readonly');
                        $linkClearLinkButton.css('visibility', 'hidden');
                        //$(this).closest('.form-control-wrap').addClass('has-change');
                        multicolumnwizard.setJsonData(mcw);
                    });
                }
                $linkClearLinkButton.css('visibility', 'visible');
            });
            $(this).click(function(e){
                e.preventDefault();
                $triggerLinkHiddenField.val($linkHiddenField.val());
                $triggerLinkHiddenField.off('change');
                $triggerLinkHiddenField.on('change', function() {
                    $linkHiddenField.val($triggerLinkHiddenField.val());
                    $linkEditLinkButton.addClass('disabled');
                    $linkEditLinkButton.attr('disabled','disabled');
                    $linkIcon.html('');
                    //$linkTextField.val($triggerLinkHiddenField.val()).addClass('has-change');
                    if(!$linkClearLinkButton) {
                        $linkTextField.after($linkTextField.closest('.input-group').find('.mwc-close'));
                        $linkClearLinkButton = $linkTextField.closest('.form-control-clearable-wrapper').find('.mwc-close');
                        $linkClearLinkButton.css('display', 'inline-block');
                        $linkClearLinkButton.click(function(e){
                            e.preventDefault();
                            $linkIcon.html('');
                            $linkHiddenField.val('');
                            $linkTextField.val('');
                            $linkTextField.removeAttr('readonly');
                            $linkClearLinkButton.css('visibility', 'hidden');
                            //$(this).closest('.form-control-wrap').addClass('has-change');
                            multicolumnwizard.setJsonData(mcw);
                        });
                    }
                    //$(this).closest('.form-control-wrap').addClass('has-change');
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

    setJsonData(mcw) {
        var sessionData = [];
        mcw.find('.ls-multicolumnwizard-row-container .ls-multicolumnwizard-row').each(function(){
            var sessionRow = {};
            $(this).find('select, input, textarea').each(function(){
                if(!$(this).attr('data-ignore')) {
                    if($(this).attr('type') == 'checkbox') {
                        sessionRow[$(this).attr('data-name')] = ($(this).is(':checked')?1:0);
                    } else {
                        sessionRow[$(this).attr('data-name')] = $(this).val();
                    }
                }
            });
            sessionData.push(sessionRow);
        });
        mcw.find('.jsonfield').val(JSON.stringify(sessionData));
    }
}

const multicolumnwizard = new Multicolumnwizard;
export default multicolumnwizard
