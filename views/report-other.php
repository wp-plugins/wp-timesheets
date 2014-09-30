	<table class="widefat page fixed" cellspacing="0">
	  <thead>
	  <tr>
		<th scope="col" class="manage-column"><?=key($timesheet[0])?></th>
		<th scope="col" class="manage-column" style="width:30%">Hours</th>								
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" class="manage-column"><?=key($timesheet[0])?></th>	  
		<th scope="col" class="manage-column" style="width:30%">Hours</th>
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php foreach($timesheet as $i => $value) : ?>
		<tr class="iedit <?=($i&1 ? 'alternate' : '')?>">
			<td><?=$value[key($timesheet[0])]?></td>
			<td><?=$value['Hours']?></td>
		</tr>		  
<?php endforeach; ?>		  
	  </tbody> 
	</table>