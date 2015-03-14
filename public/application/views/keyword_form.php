<form class="keyword-form" role="form" id="keyword_form">
<div class="row">
  <div class="col-lg-10 col-md-offset-1">
    <div class="input-group">
      <div class="input-group-btn">

        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Keywords <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <?php if(count($keyword)):?>
            <?php foreach($keyword as $id=>$word):?>
            <li><a class="keyword" keywordid="<?php echo $id; ?>"><?php echo $word; ?></a></li>
            <?php endforeach;?>

          <?php else:?>
            <li><a class="nokeyword">No keywords available</a></li>
          <?php endif;?>
        </ul>

      </div><!-- /btn-group -->
      <input type="text" class="form-control" id="keyword_input" placeholder="Enter New Keyword">
    </div><!-- /input-group -->
  </div><!-- /.col-lg-6 -->
  <div class="col-md-1">&nbsp;</div>
</div>
</form>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-10 col-md-offset-1">Key words: <div id="keyword_container">
    <?php if(isset($existing_keyword) && count($existing_keyword)):?>
    <?php foreach($existing_keyword as $id=>$word):?>
      <span class="label label-default selected_keyword" keywordid="<?php echo $id;?>"><?php echo $word;?></span>
    <?php endforeach;?>
    <?php endif;?>
  </div></div>
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>