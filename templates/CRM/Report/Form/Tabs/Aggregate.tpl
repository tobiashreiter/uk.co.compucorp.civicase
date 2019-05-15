<div id="report-tab-set-aggregate" class="civireport-criteria">
  <table class="report-layout">
    <tr class="crm-report crm-report-criteria-aggregate">
      <td>
        <div id='crm-custom_fields'>
          <label>{ts}Select Row Fields{/ts}</label>
          {$form.aggregate_row_headers.html}
        </div>
      </td>
      <td>
        <label>{ts}Select Column Header{/ts}</label>
        {$form.aggregate_column_headers.html}
      </td>
      <td>
      <div id="column_header_fields">
        <label>Group Date by</label>
        {$form.aggregate_column_date_grouping.html}
      </div>
      </td>
    </tr>
  </table>

  <script type="text/javascript">
    {literal}
    CRM.$(function($) {
      toogleDateGroupingField();
      cj('#aggregate_column_headers').on('change', function() {
        toogleDateGroupingField();
      });
    });

    /**
     * Toggles the visibility of the date grouping field based on the
     * value of the aggregate column header field
     */
    function toogleDateGroupingField() {
      var dateFields = {/literal} {$aggregateDateFields}{literal}
      var column_header_value = cj('#aggregate_column_headers').val();

      if (column_header_value in dateFields) {
        cj('#column_header_fields').show();
      }
      else {
        cj('#column_header_fields').hide();
      }
    }

    {/literal}
  </script>
</div>
