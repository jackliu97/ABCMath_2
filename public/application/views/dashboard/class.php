
<div>

<div class="col-sm-10">
  <div class="row">
    <div class="col-sm-1">&nbsp;</div>
  </div>

  <div class="row">
    <div class="col-sm-1">&nbsp;</div>
     <div class="col-sm-10">
      <h3>
        <?php echo $class->external_id;?>
        <?php if($editable):?>
        <a class="glyphicon glyphicon-pencil" href="/scaffolding/object_detail/6/<?php echo $class->id;?>"></a>
        <?php endif;?>
        <input type="hidden" id="class_id" value="<?php echo $class->id;?>">
      </h3>

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
  <div class="col-sm-1">&nbsp;</div>
  <div class="col-sm-10">
    <div class="panel panel-success">
     <div class="panel-heading">
      <h3 class="panel-title"><?php echo $class->external_id?>'s Information</h3>
     </div>
     <div class="panel-body">

      <div class="row">
         <div class="col-sm-2">
          <label>Class Description</label>
         </div>
          <div class="col-sm-3">
            <span><?php echo $class->description;?></span>
          </div>
      </div>

      <div class="row">
         <div class="col-sm-2">
          <label>Teacher</label>
         </div>
          <div class="col-sm-3">
            <span><?php echo isset($teacher->last_name) ? $teacher->last_name : 'N/A';?></span>
          </div>
      </div>

      <div class="row">
         <div class="col-sm-2">
          <label>Subject</label>
         </div>
          <div class="col-sm-3">
            <span><?php echo isset($subject->description) ? $subject->description : 'N/A';?></span>
          </div>
      </div>

      <div class="row">
        <div class="col-sm-2">
          <label>Days of week</label>
        </div>
        <div class="col-sm-5">
         <span>
            <?php echo ($class->mon == '1' ? '<span class="label label-primary">Monday</span>' : ''); ;?>
            <?php echo ($class->tue == '1' ? '<span class="label label-primary">Tuesday</span>' : ''); ;?>
            <?php echo ($class->wed == '1' ? '<span class="label label-primary">Wednesday</span>' : ''); ;?>
            <?php echo ($class->thu == '1' ? '<span class="label label-primary">Thursday</span>' : ''); ;?>
            <?php echo ($class->fri == '1' ? '<span class="label label-primary">Friday</span>' : ''); ;?>
            <?php echo ($class->sat == '1' ? '<span class="label label-primary">Saturday</span>' : ''); ;?>
            <?php echo ($class->sun == '1' ? '<span class="label label-primary">Sunday</span>' : ''); ;?>
         </span>
        </div>
      </div>

      <div class="row">
         <div class="col-sm-2">
          <label>Start Time</label>
         </div>
         <div class="col-sm-3">
            <span><?php echo $start_time->format('g:i A');?></span>
         </div>

        <div class="col-sm-2">
          <label>End Time</label>
         </div>
          <div class="col-sm-3">
            <span><?php echo $end_time->format('g:i A');?></span>
          </div>
      </div>

     </div>
    </div>

  </div>
  </div>

<div class="row">
  <div class="col-sm-1">&nbsp;</div>
  <div class="col-sm-10">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><?php echo $class->external_id?>'s Students</h3>
      </div>
      <div class="panel-body student_panel">
        <p>
          <a type="button" id="registered_students" class="btn btn-default">Registered Students</a>
          <a type="button" id="add_students" class="btn btn-default">Add Students To Class</a>
        </p>
        <p><div class="datatable_student_container table-responsive"></div></p>
       </div>

    </div>
  </div>
</div>

  <div class="row">
  <div class="col-sm-1">&nbsp;</div>
  <div class="col-sm-10">
    <div class="panel panel-info">
     <div class="panel-heading">
      <h3 class="panel-title"><?php echo $class->external_id?>'s Details</h3>
     </div>
     <div class="panel-body">
      <ul class="nav nav-pills class_detail_tab" role="tablist">
        <li><a action="show_attendance" class="pointer attendance_tab is_tab" class_id="<?php echo $class->id;?>">Attendance</a></li>
        <li><a action="print_attendance" class="pointer print_attendance_tab is_tab" class_id="<?php echo $class->id;?>">Print Attendance</a></li>
        <li><a class="pointer report_cards" class_id="<?php echo $class->id;?>">Print Report Cards</a></li>
        <li><a action="show_assignment" class="pointer assignment_tab is_tab" class_id="<?php echo $class->id;?>">Assignments</a></li>
        <li><a action="show_attachment" class="pointer attachment_tab is_tab" class_id="<?php echo $class->id;?>">Attachments</a></li>
        <li><a class="pointer grade_tab" class_id="<?php echo $class->id;?>">Grade</a></li>
      </ul>

      <div><p>

  <div class="row">
    <div class="col-sm-3">
      <div class="list-group lessons_list">
        <?php foreach($lessons as $lesson): ?>
        <?php
          $active = ($lesson->id == $lesson_id) ? 'active' : '';
        ?>
          <a lesson_id="<?php echo $lesson->id?>" class="lesson_sidebar list-group-item pointer nowrap_column">
            Lesson <?php echo $lesson->lesson_number;?>
          </a>

        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-sm-8"><p><div id="detail_body"></div></p></div>
  </div>

      </p></div>

     </div>
   </div>
  </div>
  </div>

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

<div class="modal fade" id="attachment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add/Edit attachment</h4>
      </div>
      <form role="form" id="attachment_form">
      <div class="modal-body">
        <span id="attachment_error"></span>
        <div class="form-group attachment-form-group"></div>
        <div class="form-group">
          <label for="attachment_description">Attachment Description:</label>
          <textarea class="form-control" id="attachment_description" name="attachment_description" rows="10"></textarea>
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

