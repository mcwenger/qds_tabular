# Example Usage

Everything including and between {table:rows} and {/table:rows} is standardized,
there is no prefixing or variable changes that need to occur.

{table}
<table cellspacing="0" cellpadding="0" border="0" class="table-data">
  {table:rows}
  {if table:row_count == 1 && table:first_row_heading}
  <thead>
  {if:elseif table:row_count == 2 && table:first_row_heading OR ! table:first_row_heading}
  <tbody>
  {/if}
    <tr>
      {table:columns}
      {if table:row_count == 1 && table:first_row_heading OR table:first_col_heading && table:col_count == 1}
      <th>{table:value}</th>
      {if:else}
      <td>{table:value}</td>
      {/if}
      {/table:columns}
    </tr>
  {if table:row_count == 1 && table:first_row_heading}
  </thead>
  {if:elseif table:row_count == table:total_rows}
  <tbody>
  {/if}
  {/table:rows}
</table>
{/table}
