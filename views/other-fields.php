			<tr>
				<th scope="row"><?=_e('Time spent in', 'wp-timesheets' )?></th>
				<td>
<?php foreach($data as $field) : 
	if($view === false): ?>
					<p><?=$field?> <input name="other_field[<?=$field?>]" type="number" step="0.5" id="other_field[<?=$field?>]" class="small-text" value="<?=$values[$field]?>" /> <?=_e('Hours', 'wp-timesheets' )?></p>
<?php elseif($view === true) : 
	if( empty($values[$field]) ) $values[$field] = '0'; ?>
					<p><?=$field?> <?=$values[$field]?> <?=_e('Hours', 'wp-timesheets' )?></p>
<?php endif; endforeach; ?>
				</td>
			</tr>

