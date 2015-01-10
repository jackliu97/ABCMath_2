
<div class="row">
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-5">
    <button type="button" class="display_text btn btn-default">Display as Text</button>
    <button type="button" class="display_pdf btn btn-default" examid="<?php echo $exam_id;?>">Display as PDF</button>
  </div>
</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-5">
    &nbsp;
  </div>
  <div class="col-md-1">&nbsp;</div>
</div>

<div id="text_question_container" style="display:none;">
  <div class="row">
      <div class="col-md-1">&nbsp;</div><div class="col-md-8">
        <textarea id="question_textarea" cols="200" rows="50"></textarea>
      </div>
    </div>
</div>

<div id="question_container">
  <?php if(count($questions)):?>
  <?php foreach($questions as $i=>$q):?>
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-8">
      <div class="panel panel-default"><div class="panel-heading"><?php echo $q['header'];?></div>
        <ul class="list-group">
          <?php foreach($q['answer'] as $ans):?>
            <li class="list-group-item line"><span class="line_choice" num="<?php echo $ans['choice'];?>"><?php echo $ans['choice'];?></span>.&nbsp;<span class="line_text"><?php echo $ans['line'];?></span></li>
          <?php endforeach;?>
          <li class="list-group-item ans"> ANS: <?php echo  implode(', ', $q['solution_alpa']);?></li>
          <input class="int_ans" type="hidden" value="<?php echo  implode(',', $q['solution']);?>">
        </ul>
      </div>
      </div>
    </div>
  <?php endforeach;?>
  <?php endif;?>
</div>
