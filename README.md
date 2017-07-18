# Table Fieldtype for EE3 (QDS Tabular, Beta)

This is a fieldtype that can used as a stand-alone field, or within Grid or Bloqs
setups. It is intended to be used for data tables and does not support the integration
of other third-party fieldtypes in the way that something like Matrix would – only
text values and labels can be used.

Everything including and between {table:rows} and {/table:rows} is standardized,
there is no prefixing or variable changes that need to occur.

Example markup:

```
{table_field_name}
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
{/table_field_name}
```
