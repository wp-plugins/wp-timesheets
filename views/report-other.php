	<table class="widefat page fixed" cellspacing="0">
	  <thead>
	  <tr>
		<th scope="col" class="manage-column"><?php echo key($timesheet[0])?></th>
		<th scope="col" class="manage-column" style="width:30%"><?php _e('Hours', 'wp-timesheets' )?></th>								
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" class="manage-column"><?php echo key($timesheet[0])?></th>	  
		<th scope="col" class="manage-column" style="width:30%"><?php _e('Hours', 'wp-timesheets' )?></th>
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php foreach($timesheet as $i => $value) : ?>
		<tr class="iedit <?php echo ($i&1 ? 'alternate' : '')?>">
			<td><?php echo $value[key($timesheet[0])]?></td>
			<td><?php echo $value['Hours']?></td>
		</tr>		  
<?php endforeach; ?>		  
	  </tbody> 
	</table>