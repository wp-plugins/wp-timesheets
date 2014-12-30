<?php if($display === 'autocomplete'): ?>

<script type='text/javascript'>var jobList = <?php echo json_encode($data)?></script>
<input name="job_name" type="text" id="job_name" class="regular-text automplete-jobs" value="<?php echo $value?>" required>

<?php elseif($display === 'dropdown'): ?>

<select name="job_name" id="job_name">
<?php foreach($data as $job_name) : ?>
	<option value="<?php echo $job_name?>" <?php echo selected($value, $job_name)?>><?php echo $job_name?></option>	
<?php endforeach; ?>
</select>

<?php endif; ?>

