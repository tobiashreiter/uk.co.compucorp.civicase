<script type="text/javascript">
    {literal}
    CRM.$(function($) {
      CRM.$('#singular_label').insertAfter(CRM.$('.crm-admin-options-form-block-label'));
      CRM.$('.crm-admin-options-form-block-label .description').text(
        ts('Plural form. The primary label that is displayed to users.')
      );
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
        {ts}Singular form. The singular form of the primary label that is displayed to users.{/ts}
      </span>
    </td>
  </tr>
</table>
