(function ($, _) {
  window.waitForElement = function ($, elementPath, callBack) {
    (new window.MutationObserver(function () {
      callBack($, $(elementPath));
    })).observe(document.querySelector(elementPath), {
      attributes: true
    });
  };

  $(document).one('crmLoad', function () {
    const entityRefCustomFields = Object.values(CRM.vars.civicase.entityRefCustomFields ?? {});

    /* eslint-disable no-undef */
    waitForElement($, '#customData', function ($, elem) {
      entityRefCustomFields.forEach(field => {
        $(`[name^=${field.name}_]`)
          .attr('placeholder', field.placeholder)
          .attr('disabled', false)
          .crmEntityRef({
            entity: field.entity,
            create: false
          });

        if (field.value) {
          $(`[name^=${field.name}_]`).val(field.value).trigger('change');
        }
      });
    });
  });
})(CRM.$, CRM._);
