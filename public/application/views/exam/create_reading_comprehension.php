
<div class="row">
  <div class="col-md-5 col-md-offset-1">
    <button type="button" class="display_pdf btn btn-default" examid="<?php echo $exam_id;?>">Display as PDF</button>
  </div>
</div>
</div>

<div class="row">
  <div class="col-md-5 col-md-offset-1">&nbsp;</div>
</div>

<div id="question_container">

  <?php $question_number = 0;?>

  <?php if(count($questions)):?>
  <?php foreach($questions as $i=>$q):?>
    <?php $reading_material = $q['reading_material'];?>
    <?php $questions = $q['questions'];?>
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-6">
      <div class="panel panel-default"><div class="panel-heading">

        <?php foreach($reading_material as $x=>$lines):?>
          <?php $line_number = $x + 1?>
          <div class="row">
            <div class="col-md-1 text-right">
              <?php if($line_number%5 == 0):?>
                Line <?php echo $line_number;?>
              <?php endif;?>
            </div>
            <div class="col-md-6">
              <?php echo $lines;?>
            </div>
          </div>
        <?php endforeach;?>

      </div>
        <ul class="list-group">
          <?php foreach($questions as $question):?>
            <li class="list-group-item">
              <?php $question_number += 1;?>
              <p class="well"><?php echo ($question_number . '. ' . $question['question']);?></p>
              <?php $j = 1;?>
              <?php $ans = '';?>
              <?php foreach($question['choices'] as $c):?>
                <p><?php echo (chr(64 + $j) . '. ' . $c['text']);?></p>
                <?php if($c['answer'] == '1'):?>
                  <?php $ans = chr(64 + $j);?>
                <?php endif;?>
                <?php $j += 1;?>
              <?php endforeach;?>
              <p>ANSWER: <?php echo $ans;?></p>
            </li>
          <?php endforeach;?>
        </ul>
      </div>
      </div>
    </div>
  <?php endforeach;?>
  <?php endif;?>
</div>
