<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
<div class="col-md-5 col-md-offset-1">
<form role="form" id="add_entity_field_form">

  <div class="form-group">

    <label>Table Name</label>
    <p class="help-block"><?php echo $entity->table_name;?></p>
    <input type="hidden" value="<?php echo $entity->id;?>" id="entity_table_id" name="entity_table_id">

  </div>
  <div class="form-group">

    <label>Field Database Name</label>
    <?php
      if(isset($field->id)){
        echo '<p class="help-block">' . $field->field_name . '</p>';
        echo '<input type="hidden" value="' . $field->id . '" id="field_id" name="field_id">';
      }else{
        echo form_dropdown('field_name',
                          $field_options,
                          '',
                          ' id="field_name" class="form-control" ');

      }
    ?>

  </div>
  <div class="form-group">

    <label>Field Display Name</label>
    <?php
      $data = array(
          'name' => 'display_name',
          'id' => 'display_name',
          'value' => (isset($field->display_name) ? $field->display_name : ''),
          'class' => 'form-control',
      );
      echo form_input($data);
    ?>

  </div>
  <div class="form-group">

    <label>Field Types</label>
    <?php
        echo form_dropdown('field_type_id',
                          $field_type_options,
                          (isset($field->field_type_id) ? $field->field_type_id : ''),
                          ' id="field_type_id" class="form-control" ');
    ?>


  </div>

  <div class="form-group">

    <label>Joins to Table</label>
    <?php
        echo form_dropdown('join_table',
                          $join_table_options,
                          (isset($field->join_table) ? $field->join_table : ''),
                          ' id="join_table" class="form-control" ');
    ?>


  </div>

  <div class="form-group">

    <label>Join to column key</label>
    <?php
      $data = array(
          'name' => 'join_column',
          'id' => 'join_column',
          'value' => (isset($field->join_column) ? $field->join_column : ''),
          'class' => 'form-control',
      );
      echo form_input($data);
    ?>

  </div>

  <div class="form-group">

    <label>Join to column display</label>
    <?php
      $data = array(
          'name' => 'join_display',
          'id' => 'join_display',
          'value' => (isset($field->join_display) ? $field->join_display : ''),
          'class' => 'form-control',
      );
      echo form_input($data);
    ?>

  </div>

  <div class="checkbox">

    <label for="is_primary">Is Primary Key?</label>
    <?php
      $data = array(
          'name' => 'is_primary',
          'id' => 'is_primary',
          'value' => '1',
          'checked' => (isset($field->is_primary) ? $field->is_primary : ''),
      );
      echo form_checkbox($data);
    ?>
  </div>

  <div class="checkbox">

    <label for="display_in_list">Display In List View</label>
    <?php
      $data = array(
          'name' => 'display_in_list',
          'id' => 'display_in_list',
          'value' => '1',
          'checked' => (isset($field->display_in_list) ? $field->display_in_list : ''),
      );
      echo form_checkbox($data);
    ?>
  </div>

  <div class="form-group">

    <label>Extra Tags</label>
    <?php
        $data = array(
          'name' => 'extra_tags',
          'id' => 'extra_tags',
          'value' => (isset($field->extra_tags) ? $field->extra_tags : ''),
          'class' => 'form-control',
      );
      echo form_input($data);
    ?>


  </div>

  <div class="form-group">

    <label>Field Size (default 50)</label>
    <?php
        $data = array(
          'name' => 'field_size',
          'id' => 'field_size',
          'value' => (isset($field->field_size) ? $field->field_size : '50'),
          'class' => 'form-control',
      );
      echo form_input($data);
    ?>


  </div>

  <div class="form-group">

    <label>Field Display Order</label>
    <?php
        $data = array(
          'name' => 'field_order',
          'id' => 'field_order',
          'value' => (isset($field->field_order) ? $field->field_order : '0'),
          'class' => 'form-control',
      );
      echo form_input($data);
    ?>


  </div>

  <button type="submit" class="btn btn-default">Save</button>
  <a type="button" href="/admin/entity_fields/<?php echo $entity->id;?>" class="btn btn-default">Back</a>

</form>
</div>
<div class="col-md-5">
	<span id="parse_result">
	</span>
</div>
<div class="col-md-1">&nbsp;</div>
</div>