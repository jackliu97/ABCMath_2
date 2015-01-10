<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
   <div class="col-md-2">
    <h3>Hello <?php echo $teacher->first_name . ' ' . $teacher->last_name;?></h3>
  </div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-2">
  <input type="hidden" name="teacher_id" id="teacher_id" value="<?php echo $teacher->id;?>">
  <div class="well">
    <div class="form-group">
      <label>Current Class: </label>
      <?php 
        
        $options = array();
        foreach($teacher->classes as $class){
          $options[$class->id] = $class->description ? $class->description : $class->external_id;
        }

        echo form_dropdown('class_id', $options, '', 'id="class_id" class="form-control"');
      ?>
    </div>

    <div class="form-group">
      <label>Question Type: </label>
      <?php 
        
        $options = array(
          'vocabulary'=>'Vocabulary'
          );
        echo form_dropdown('question_type', $options, '', 'id="question_type" class="form-control"');
      ?>
    </div>

    <div class="form-group">
      <label>Number of Random Student: </label>
      <input type="text" class="form-control" id="num_students" name="num_students" value="10">
    </div>
    <button type="button" class="load_students btn btn-default">Load Students</button>
  </div>

  <div class="well">
    <h4>Students</h4>

    <div class="list-group" id="student_container">
    </div>

  </div>
</div>


<div class="col-md-8">
  <div class="well" id="question_container">
  </div>
</div>


<div class="col-md-1">&nbsp;</div>
</div>