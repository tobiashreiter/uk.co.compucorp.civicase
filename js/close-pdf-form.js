(function ($, civicaseBase) {
  if (!civicaseBase) {
    return;
  }
  civicaseBase.shouldClosePDFPopup = true;

  civicaseBase.closePDFPopup = function (forceClosePDFPopup) {
    var $form = CRM.$('.CRM_Contact_Form_Task_PDF');
    var popup = $form.closest('.ui-dialog-content');

    if (!forceClosePDFPopup && !civicaseBase.shouldClosePDFPopup) {
      return;
    }

    setTimeout(function () {
      popup.trigger('crmPopupFormSuccess');
      popup.dialog('close');
    });
  };

  $(document).one('crmLoad', function () {
    var $form = CRM.$('.CRM_Contact_Form_Task_PDF');

    $('body')
      .off('submit', $form)
      .on('submit', $form, function (event) {
        if (event.originalEvent.submitter.id === '_qf_PDF_upload-bottom') {
          CRM['civicase-base'].closePDFPopup();
        }
      });
  });
})(CRM.$, CRM['civicase-base']);
