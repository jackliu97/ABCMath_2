<div class="row">
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-10">
  <div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
      Filter by keyword&nbsp;
      <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
      <?php if(count($keyword)):?>
              <?php foreach($keyword as $id=>$word):?>
              <li><a class="keyword" keywordid="<?php echo $id; ?>"><?php echo $word; ?></a></li>
              <?php endforeach;?>

            <?php else:?>
              <li><a class="nokeyword">No keywords available</a></li>
            <?php endif;?>
    </ul>
  </div>
  </div>
  <div class="col-md-1">&nbsp;</div>
</div>


<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-10">Key words: <div id="keyword_container">
  </div></div>
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>