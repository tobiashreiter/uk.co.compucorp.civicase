<script type="text/javascript">
    {literal}
    CRM.$(function($) {
      CRM.$('#singular_label').insertAfter(CRM.$('.crm-admin-options-form-block-label'));
    });
    {/literal}
</script>

<table>
  <tr id="singular_label">
    <td class="label"> {$form.singular_label.label} </td>
    <td>
      {$form.singular_label.html}
      <br />
      <span class="description">
        {ts}A singular noun for the case category.{/ts}
      </span>
    </td>
  </tr>
</table>
