<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
<div class="col-md-5 col-md-offset-1">
<form role="form" id="add_object_form">

  <div class="form-group">

    <label>Table Name</label>
    <p class="help-block"></p>
    <input type="hidden" value="<?php echo $entity_id;?>" id="entity_id" name="entity_id">
    <input type="hidden" value="<?php echo $object_id;?>" id="object_id" name="object_id">

  </div>
  <?php if (!isset($fields) || !count($fields)):?>
  <div>NOTHING TO SEE HERE</div>
  <?php else:?>

    <?php foreach($fields as $field):
      if($field->field_type_id == \ABCMath\Entity\EntityField::HIDDEN){
        continue;
      }
    ?>

    <?php
      echo $field->buildFieldHTML($object_data[$field->field_name]);
    ?>


    <?php endforeach;?>
  <?php endif;?>

  <button type="submit" class="btn btn-default">Save</button>
  <a type="button" href="/scaffolding/object_list/<?php echo $entity_id;?>" class="btn btn-default">Back</a>

</form>
</div>
<div class="col-md-5">
	<span id="parse_result">
	</span>
</div>
<div class="col-md-1">&nbsp;</div>
</div>