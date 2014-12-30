<?php $cols = 9.5 + count($other_fields)?>
	<table class="widefat page fixed" cellspacing="0">
	  <thead>
	  <tr>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*2,2)?>%"><?php _e('User', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*2,2)?>%"><?php _e('Date', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*2,2)?>%"><?php _e('Job Name', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*0.5,2)?>%"><a class="post-com-count" title="Description"><span class="comment-count">&nbsp;</span></a></th>		
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php _e('From', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php _e('To', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php _e('Hours', 'wp-timesheets' )?></th>
<?php foreach($other_fields as $other_field) : ?>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php echo $other_field?></th>
<?php endforeach; ?>		
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*2,2)?>%"><?php _e('User', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*2,2)?>%"><?php _e('Date', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*2,2)?>%"><?php _e('Job Name', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols*0.5,2)?>%"><a class="post-com-count" title="Description"><span class="comment-count">&nbsp;</span></a></th>		
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php _e('From', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php _e('To', 'wp-timesheets' )?></th>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php _e('Hours', 'wp-timesheets' )?></th>
<?php foreach($other_fields as $other_field) : ?>
		<th scope="col" class="manage-column" style="width: <?php echo round(100/$cols,2)?>%"><?php echo $other_field?></th>
<?php endforeach; ?>		
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php foreach($timesheet as $i => $value) : ?>
		<tr class="iedit <?php echo ($i&1 ? 'alternate' : '')?>">
			<td><?php echo $value['User']?></td>
			<td><?php echo $value['Date']?></td>
			<td><?php echo $value['Job']?>
				<div class="row-actions"><span class="edit"><a href="<?php echo wp_nonce_url('?page=wp_timesheets_user_manage&action=edit&id='.$value['ID'], 'Get_'.$value['ID'])?>"><?php _e('Edit', 'wp-timesheets' )?></a> | </span><span class="view"><a href="<?php echo wp_nonce_url('?page=wp_timesheets_user_manage&action=view&id='.$value['ID'], 'Get_'.$value['ID'])?>"><?php _e('View', 'wp-timesheets' )?></a></span></div>
			</td>	
			<td><?php echo (!empty($value['Description']) ? '<a class="post-com-count" title="'.$value['Description'].'"><span class="comment-count">&nbsp;</span></a>' : '')?></td>
			<td><?php echo $value['From']?></td>	
			<td><?php echo $value['To']?></td>				
			<td><?php echo $value['Hours']?></td>
<?php foreach($other_fields as $other_field) : ?>
			<td><?php echo (!empty($value[$other_field]) ? date('H:i',$value[$other_field]*3600) : '')?></td>
<?php endforeach; ?>			
		</tr>		  
<?php endforeach; ?>		
	  </tbody> 
	</table>