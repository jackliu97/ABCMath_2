<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
   <div class="col-md-10">
    <h3>Hello <?php echo $user_email;?></h3>
  </div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-10 student_panel">
  <div class="panel panel-success">
   <div class="panel-heading">
    <h3 class="panel-title">Student List</h3>
   </div>
   <div class="panel-body">
    <p>
      <button type="button" id="all_students" class="btn btn-default">All Students</button>
      <button type="button" id="absent_students" class="btn btn-default">Absent Students</button>
      <button type="button" id="late_students" class="btn btn-default">Late Students</button>
    </p>

    <p><div class="datatable_student_container table-responsive dataTable_fixed_10"></div></p>

   </div>
  </div>

</div>
</div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-10 class_panel">
  <div class="panel panel-info">
    <div class="panel-heading">
       <h3 class="panel-title">Classes</h3>
    </div>

    <div class="panel-body">
      <p>
        <button type="button" id="class_in_session" class="btn btn-default">Only Classes in session</button>
        <button type="button" id="all_classes" class="btn btn-default">Show All Classes</button>
      </p>
      <p>
        <button class='btn add_mode glyphicon glyphicon-list-alt' style='width:45px;'></button>
        Click to enter grade for this class.
      </p>
      <p>
        <button class='btn add_mode glyphicon glyphicon-time' style='width:45px;'></button>
        Click to take attendence for this class
      </p>
      <p><div class="datatable_class_container table-responsive dataTable_fixed_10"></div></p>
    </div>

  </div>
</div>
</div>