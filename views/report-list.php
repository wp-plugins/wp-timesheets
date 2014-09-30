<?php $cols = 9.5 + count($other_fields)?>
	<table class="widefat page fixed" cellspacing="0">
	  <thead>
	  <tr>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*2,2)?>%">User</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*2,2)?>%">Date</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*2,2)?>%">Job Name</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*0.5,2)?>%"><a class="post-com-count" title="Description"><span class="comment-count">&nbsp;</span></a></th>		
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%">From</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%">To</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%">Hours</th>
<?php foreach($other_fields as $other_field) : ?>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%"><?=$other_field?></th>
<?php endforeach; ?>		
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*2,2)?>%">User</th>	  
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*2,2)?>%">Date</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*2,2)?>%">Job Name</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols*0.5,2)?>%"><a class="post-com-count" title="Description"><span class="comment-count">&nbsp;</span></a></th>		
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%">From</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%">To</th>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%">Hours</th>
<?php foreach($other_fields as $other_field) : ?>
		<th scope="col" class="manage-column" style="width: <?=round(100/$cols,2)?>%"><?=$other_field?></th>
<?php endforeach; ?>		
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php foreach($timesheet as $i => $value) : ?>
		<tr class="iedit <?=($i&1 ? 'alternate' : '')?>">
			<td><?=$value['User']?></td>
			<td><?=$value['Date']?></td>
			<td><?=$value['Job']?>
				<div class="row-actions"><span class="edit"><a href="<?=wp_nonce_url('?page=wp_timesheets_user_manage&action=edit&id='.$value['ID'], 'Get_'.$value['ID'])?>">Edit</a> | </span><span class="view"><a href="<?=wp_nonce_url('?page=wp_timesheets_user_manage&action=view&id='.$value['ID'], 'Get_'.$value['ID'])?>">View</a></span></div>
			</td>	
			<td><?=(!empty($value['Description']) ? '<a class="post-com-count" title="'.$value['Description'].'"><span class="comment-count">&nbsp;</span></a>' : '')?></td>
			<td><?=$value['From']?></td>	
			<td><?=$value['To']?></td>				
			<td><?=$value['Hours']?></td>
<?php foreach($other_fields as $other_field) : ?>
			<td><?=(!empty($value[$other_field]) ? date('H:i',$value[$other_field]*3600) : '')?></td>
<?php endforeach; ?>			
		</tr>		  
<?php endforeach; ?>		
	  </tbody> 
	</table>