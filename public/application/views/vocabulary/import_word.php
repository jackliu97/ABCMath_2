<form role="form" id="group_form">
<div class="row">
  <div class="col-lg-10 col-md-offset-1">
    <div class="input-group">
      <div class="input-group-btn">

        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Vocabulary groups <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <?php if(isset($groups) && count($groups)):?>
            <?php foreach($groups as $group):?>
            <li><a class="group" groupid="<?php echo $group['id'];?>"><?php echo $group['name'];?></a></li>
            <?php endforeach;?>

          <?php else:?>
            <li><a class="nogroup">No groups available</a></li>
          <?php endif;?>
        </ul>

      </div><!-- /btn-group -->
      <input type="text" class="form-control" id="group_input" placeholder="Enter New Group">
    </div><!-- /input-group -->
  </div><!-- /.col-lg-6 -->
  <div class="col-md-1">&nbsp;</div>
</div>
</form>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-10 col-md-offset-1">Groups: <div id="group_container">
    <?php if(isset($existing_group) && count($existing_group)):?>
    <?php foreach($existing_group as $id=>$group):?>
      <span class="label label-default selected_group" groupid="<?php echo $id;?>"><?php echo $group;?></span>
    <?php endforeach;?>
    <?php endif;?>
  </div></div>
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
<div class="col-md-5 col-md-offset-1">
<form role="form" id="import_word_form">
  <div class="form-group">
    <label>Import CSV wordfile.</label>
    <input type="file">
  </div>
  <button type="submit" class="btn btn-default">Import Words</button>
</form>
</div>
<div class="col-md-1">&nbsp;</div>
</div>