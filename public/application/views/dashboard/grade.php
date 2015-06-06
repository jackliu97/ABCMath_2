
<div class="row">
	<div class="col-md-10 col-md-offset-1 message" id="main_error"></div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<h4>
			Grading class <?php echo isset($class->external_id) ? $class->external_id : '';?>&nbsp;
		</h4>

		<input type="hidden" name="class_id" id="class_id" value="<?php echo $class->id;?>">
	</div> 
</div>
<div class="row">
	<div class="col-md-2 col-md-offset-1">
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
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="fixed-container"><?php echo $body_html; ?></div>
	</div>
</div>