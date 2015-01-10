<div class="row pad-top">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-5">
<form role="form" id="save_permission">
  <div class="row">
  <div class="col-md-10 form-group">
    <label>Edit User Group</label>
    <input type="hidden" class="form-control permission" value="<?php echo $user_id;?>" id="user_id" name="user_id">
  </div>
  </div>

  <?php foreach($groups as $id=>$name):?>

  <div class="checkbox">

    <label for="<?php echo $name;?>"><?php echo $name;?></label>
    <?php
      $data = array(
          'name' => $name,
          'id' => $name,
          'value' => $id,
          'class' => 'permission',
          'checked' => (array_key_exists($id, $existing_group) ? '1' : ''),
      );
      echo form_checkbox($data);
    ?>
  </div>
  <?php endforeach;?>
  <button type="submit" class="btn btn-default">Save</button>
  <button class="cancel_user btn btn-default">Cancel</button>
</form>
</div>
<div class="col-md-1">&nbsp;</div>
</div>