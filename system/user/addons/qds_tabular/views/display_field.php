<div class="ft-qds_tabular-container" id="qds_tabular_<?=$field_name?>" data-field="<?=$field_name?>" data-ee_version="<?=$ee_version?>">
  <table class="ft-qds_tabular" cellspacing="0" cellpadding="0" border="0">
    <thead>
			<tr>
				<th class="row-count"></th>

        <?php if (empty($field_data)): ?>

          <?php foreach(range(1,$starting_cols,1) as $ci): ?>
            <th class="col-count" data-index="<?=$ci-1?>">
    					<div class="count"><?=$ci?></div>
    					<a href="#" class="delete-col"><span aria-hidden="true">&times;</span></a>
    				</th>
          <?php endforeach; ?>

        <?php else: ?>

          <?php foreach(range(1,count($field_data[0]),1) as $ci): ?>
            <th class="col-count" data-index="<?=$ci-1?>">
    					<div class="count"><?=$ci?></div>
    					<a href="#" class="delete-col"><span aria-hidden="true">&times;</span></a>
    				</th>
          <?php endforeach; ?>

        <?php endif; ?>

			</tr>
		</thead>
    <tbody>

      <?php if (empty($field_data)): ?>

        <?php foreach(range(1,$starting_rows,1) as $ri): ?>
        <tr data-index="<?=$ri-1?>">
  				<th>
  					<div class="row-count"><?=$ri?></div>
            <a href="#" class="delete-row"><span aria-hidden="true">&times;</span></a>
  				</th>
          <?php foreach(range(1,$starting_cols,1) as $ci): ?>
  				<td class="cell">
  					<textarea name="<?=$field_name?>[<?=$ri-1?>][<?=$ci-1?>]" data-row="0" data-cell="0"></textarea>
  				</td>
          <?php endforeach; ?>
  			</tr>
        <?php endforeach; ?>

      <?php else: ?>

        <?php foreach($field_data as $ri => $row): ?>
        <tr data-index="<?=$ri?>">
  				<th>
  					<div class="row-count"><?=$ri+1?></div>
  					<a href="#" class="delete-row"><span aria-hidden="true">&times;</span></a>
  				</th>
          <?php foreach($row as $ci => $coldata): ?>
  				<td class="cell">
  					<textarea name="<?=$field_name?>[<?=$ri?>][<?=$ci?>]" data-row="<?=$ri?>" data-cell="<?=$ci?>"><?=$coldata?></textarea>
  				</td>
          <?php endforeach; ?>
  			</tr>
        <?php endforeach; ?>

      <?php endif; ?>

    </tbody>
  </table>
  <div class="btn-container">
    <a class="btn btn-add-row"><span>+</span> Add Row</a>
    <a class="btn btn-add-col"><span>+</span> Add Column</a>
  </div>
  <div class="settings-container">
    <label>
      <input type="hidden" name="<?=$field_name?>[settings][first_row_heading]" value="0">
      <input type="checkbox" name="<?=$field_name?>[settings][first_row_heading]" value="1"<?php if ($first_row_heading) echo ' checked'; ?>> First Row as Headings</label>
    <label>
      <input type="hidden" name="<?=$field_name?>[settings][first_col_heading]" value="0">
      <input type="checkbox" name="<?=$field_name?>[settings][first_col_heading]" value="1"<?php if ($first_col_heading) echo ' checked'; ?>> First Column as Headings</label>
  </div>
</div>
