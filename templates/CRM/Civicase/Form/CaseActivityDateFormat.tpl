<table class="form-layout-compressed">
<tr class="crm-date-form-block-_qf_civiCaseActivityDateformat">
   <td class="label">{$form._qf_civiCaseActivityDateformat.label}</td>
   <td>{$form._qf_civiCaseActivityDateformat.html}</td>
</tr>
</table>

{literal}
  <script>
    CRM.$('.crm-date-form-block-dateformatTime').last().after(CRM.$('.crm-date-form-block-_qf_civiCaseActivityDateformat'))
  </script>
{/literal}
