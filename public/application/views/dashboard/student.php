<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
   <div class="col-md-10">
    <h3>
      <?php echo $student->first_name . ' ' . $student->last_name;?>
      <?php if($editable):?>
      <a class="glyphicon glyphicon-pencil" href="/scaffolding/object_detail/1/<?php echo $student->id;?>"></a>
      <?php endif;?>

    </h3>

  </div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-10">
  <div class="panel panel-success">
   <div class="panel-heading">
    <h3 class="panel-title"><?php echo $student->first_name?>'s Information</h3>
   </div>
   <div class="panel-body">
    <p>
      <form role="form" id="student_name_form">
      <div class="input-group input-group-lg form-group">
        <span class="input-group-addon">Search</span>
        <input type="text" class="form-control" placeholder="Student Name" id="student_name">
        <input type="hidden" id="student_id" name="student_id" value="<?php echo $student->id;?>">
      </div>
      </form>
    </p>

    <div class="row">
       <div class="col-md-2">
        <label>First Name</label>
       </div>
        <div class="col-md-3">
          <span><?php echo $student->first_name;?></span>
        </div>
        <div class="col-md-2">
        <label>Last Name</label>
       </div>
        <div class="col-md-3">
          <span><?php echo $student->last_name;?></span>
        </div>
    </div>

    <div class="row">
       <div class="col-md-2">
        <label>Email 1</label>
       </div>
        <div class="col-md-3">
          <span><?php echo $student->email;?></span>
        </div>
        <div class="col-md-2">
        <label>Email 2</label>
       </div>
        <div class="col-md-3">
          <span><?php echo $student->email2;?></span>
        </div>
    </div>

    <div class="row">
       <div class="col-md-2">
        <label>Date of Birth</label>
       </div>
       <div class="col-md-3">
          <span><?php echo format_date($student->dob);?></span>
       </div>

      <div class="col-md-2">
        <label>Pickup Method</label>
       </div>
        <div class="col-md-3">
          <span><?php echo format_date($student->pickup_method);?></span>
        </div>
    </div>

    <div class="row">
      <div class="col-md-2">
        <label>Address</label>
      </div>
      <div class="col-md-5">
        <address>
          
          <?php 
          echo $student->address1 . '<br>';

          if($student->address2 ){
            echo $student->address2 . '<br>';
          }

          echo $student->city . ',&nbsp;' . $student->state . '&nbsp;' . $student->zip . '<br>';
          ?>

          <abbr title="Phone">Home Phone:</abbr> <?php echo $student->telephone;?></br>
          <abbr title="Phone">2nd Home Phone:</abbr> <?php echo $student->telephone2;?></br>
          <abbr title="Phone">Cell Phone:</abbr> <?php echo $student->cellphone;?>
        </address>
      </div>
    </div>



   </div>
  </div>

</div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-10">
  <div class="panel panel-info">
    <div class="panel-heading" id="class_collapsable">
       <h3 class="panel-title"><?php echo $student->first_name?>'s Classes</h3>
    </div>
    <div class="panel-body class_collapsable panel-collapse collapse in">
    <p>
        <button type="button" id="registered_classes" class="btn btn-default"><?php echo $student->first_name?>'s Classes Only</button>
        <button type="button" id="all_classes" class="btn btn-default">Show All Classes</button>
      </p>
    <p><div class="datatable_class_container table-responsive dataTable_fixed_10"></div></p>
  </div>
  </div>
</div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-10">
  <div class="panel panel-warning">
    <div class="panel-heading" id="notes_collapsable">
       <h3 class="panel-title"><?php echo $student->first_name?>'s Notes</h3>
    </div>
    <div class="panel-body notes_collapsable panel-collapse collapse in">
      <p>
        <button student_id="<?php echo $student->id?>" class="btn btn-default" id="add_note">Add New Note</button>
      </p>
      <p><div class="datatable_note_container table-responsive dataTable_fixed_10"></div></p>
  </div>
  </div>
</div>
</div>


<div class="modal fade" id="new_note_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">New note for <?php echo $student->first_name?></h4>
      </div>
      <form role="form" id="note_form">
      <div class="modal-body">
        <span id="notes_error"></span>
        <div class="form-group">
          <label for="notes">Note:</label>
          <textarea class="form-control" id="notes" rows="10"></textarea>
          <input type="hidden" id="note_id" value="">
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