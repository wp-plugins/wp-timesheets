<?php if($display === 'autocomplete'): ?>

<script type='text/javascript'>var jobList = <?=json_encode($data)?></script>
<input name="job_name" type="text" id="job_name" class="regular-text automplete-jobs" value="<?=$value?>" required>

<?php elseif($display === 'dropdown'): ?>

<select name="job_name" id="job_name">
<?php foreach($data as $job_name) : ?>
	<option value="<?=$job_name?>" <?=selected($value, $job_name)?>><?=$job_name?></option>	
<?php endforeach; ?>
</select>

<?php endif; ?>

