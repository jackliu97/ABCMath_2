<div class="row">
	<div class="col-md-11">&nbsp;</div>
</div>


<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10 message" id="main_error"></div>
</div>

<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10">
		<h4>
			Grading class <?php echo isset($class->external_id) ? $class->external_id : '';?>&nbsp;
			<?php if(isset($class->external_id)): ?>
			<a type="button" class="save btn btn-primary">Save</a>
			<?php endif;?>
		</h4>

		<input type="hidden" name="class_id" id="class_id" value="<?php echo $class->id;?>">
	</div> 
</div>
<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-2">
		<div class="form-group">
			<?php
				echo form_dropdown(
					'class_id', 
					$class_options, 
					$class->id, 
					'id="class_id" class="form-control class_dropdown"');
			?>
		</div>
</div>
<div class="row">
	<div class="col-md-10">
		&nbsp;
	</div>
</div>

<div class="row">
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-10">
		<div class="fixed-container"><?php echo $body_html; ?></div>
	</div>
</div>