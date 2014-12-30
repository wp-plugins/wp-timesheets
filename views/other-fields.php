			<tr>
				<th scope="row"><?php _e('Time spent in', 'wp-timesheets' )?></th>
				<td>
<?php foreach($data as $field) : 
	if($view === false): ?>
					<p><?php echo $field?> <input name="other_field[<?php echo $field?>]" type="number" step="0.5" id="other_field[<?php echo $field?>]" class="small-text" value="<?php echo $values[$field]?>" /> <?php _e('Hours', 'wp-timesheets' )?></p>
<?php elseif($view === true) : 
	if( empty($values[$field]) ) $values[$field] = '0'; ?>
					<p><?php echo $field?> <?php echo $values[$field]?> <?php _e('Hours', 'wp-timesheets' )?></p>
<?php endif; endforeach; ?>
				</td>
			</tr>

