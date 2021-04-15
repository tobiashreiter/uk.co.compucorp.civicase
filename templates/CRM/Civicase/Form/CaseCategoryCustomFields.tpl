<script type="text/javascript">
    {literal}
    CRM.$(function($) {
      CRM.$('#case_category_singular_label').insertAfter(CRM.$('.crm-admin-options-form-block-label'));
    });
    {/literal}
</script>

<table>
  <tr id="case_category_singular_label">
    <td class="label"> {$form.case_category_singular_label.label} </td>
    <td>
      {$form.case_category_singular_label.html}
      <br />
      <span class="description">
        {ts}A singular noun for the case category.{/ts}
      </span>
    </td>
  </tr>
</table>
