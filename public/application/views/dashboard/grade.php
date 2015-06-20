
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

<div class="modal fade" id="assignment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add/Edit assignment</h4>
      </div>
      <form role="form" id="assignment_form">
      <div class="modal-body">
        <input type="hidden" id="assignment_id">
        <input type="hidden" id="assignment_lesson_id">
        <span id="assignment_error"></span>

        <div class="form-group">
          <label for="assignment_name">Name:</label>
          <input class="form-control" id="assignment_name" name="assignment_name">
        </div>

        <div class="form-group">
          <label for="assignment_description">Type:</label>
          <?php
            echo form_dropdown('assignment_type_id',
              $assignment_types,
              '',
              " id='assignment_type_id' class='form-control' ");
          ?>
        </div>

        <div class="form-group">
          <label for="maximum_score">Maximum Score:</label>
          <input class="form-control" id="maximum_score" name="maximum_score">
        </div>

        <div class="form-group">
          <label for="assignment_weight">Weight:</label>
          <input class="form-control" id="assignment_weight" name="assignment_weight">
        </div>

        <div class="checkbox apply_to_all">
          <label><input id="apply_to_all" name="apply_to_all" type="checkbox">Add to all lessons</label>
        </div>

        <div class="form-group">
          <label for="assignment_description">Description:</label>
          <textarea class="form-control" id="assignment_description" name="assignment_description" rows="10"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>