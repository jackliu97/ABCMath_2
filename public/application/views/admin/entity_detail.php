<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
<div class="col-md-5 col-md-offset-1">
<form role="form" id="add_entity_form">

  <div class="form-group">
    <label>Table Database ID</label>
    <?php
      $data = array(
          'class'=>'form-control',
          'type'=>'text',
          'name'=>'table_id',
          'id'=>'table_id',
          'disabled'=>'disabled',
          'value'=> (isset($entity) ? $entity->id : 'New Data')
        );
      echo form_input($data);
    ?>
  </div>

  <div class="form-group">
    <label>Table Name</label>
    <?php
      echo form_dropdown('table_name',
                          $table_options,
                          (isset($entity->table_name) ? $entity->table_name : ''),
                          ' id="table_name" class="form-control" ');
    ?>
  </div>


   <div class="form-group">
    <label>Display Name</label>
    <?php
      $data = array(
          'class'=>'form-control',
          'type'=>'text',
          'name'=>'display_name',
          'id'=>'display_name',
          'value'=> (isset($entity) ? $entity->display_name : '')
        );
      echo form_input($data);
    ?>
  </div>

  <div class="form-group">

    <label>Before Hook (program to call before save)</label>
    <?php
      $data = array(
          'class'=>'form-control',
          'type'=>'text',
          'name'=>'before_hook',
          'id'=>'before_hook',
          'value'=> (isset($entity->before_hook) ? $entity->before_hook : '')
        );
      echo form_input($data);
    ?>

  </div>

  <div class="form-group">

    <label>After Hook (program to call after save)</label>
    <?php
      $data = array(
          'class'=>'form-control',
          'type'=>'text',
          'name'=>'after_hook',
          'id'=>'after_hook',
          'value'=> (isset($entity->after_hook) ? $entity->after_hook : '')
        );
      echo form_input($data);
    ?>

  </div>

  <button type="submit" class="btn btn-default">Save</button>
  <a type="button" href="/admin" class="btn btn-default">Back</a>

</form>
</div>
<div class="col-md-5">
	<span id="parse_result">
	</span>
</div>
<div class="col-md-1">&nbsp;</div>
</div>