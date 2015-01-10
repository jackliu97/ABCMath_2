
<div class="row pad-top">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-5">
<form role="form" id="new_group_form">
  <div class="row">
  <div class="col-md-3 form-group">
    <label>Add New Group</label>
  </div>
  </div>

  <div class="row form-group">
  <div class="col-md-4">
    <label>Group Name</label>
  </div>
  <div class="col-md-4">
    <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Name">
  </div>
  </div>

  <?php foreach($permissions as $permission):?>
  <div class="checkbox">

    <label for="<?php echo $permission['rule'];?>"><?php echo ucwords($permission['name']);?></label>
    <?php
      $data = array(
          'name' => $permission['rule'],
          'id' => $permission['rule'],
          'value' => '1',
          'checked' => '',
          'class' => 'permission'
      );
      echo form_checkbox($data);
    ?>
  </div>
  <?php endforeach;?>

  
  <button type="submit" class="btn btn-default">Save</button>
  <button class="cancel_group btn btn-default">Cancel</button>
</form>
</div>
<div class="col-md-1">&nbsp;</div>
</div>