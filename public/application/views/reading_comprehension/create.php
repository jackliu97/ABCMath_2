

<form role="form" id="reading_comprehension_form">
<div class="row">
<div class="col-md-5 col-md-offset-1">
  <div class="row">
    <div class="col-md-5">
      <p><h4>Article</h4></p>
    </div>
  </div>
  <div class="form-group">
    <label></label>
    <input id="reading_comprehension_id" type="hidden" value="<?php echo $reading_comprehension_id;?>">
    <textarea class="form-control" cols="80" rows="20" id="paragraph"><?php
      echo (isset($reading_comprehension) ? $reading_comprehension->full_text : '');
      ?></textarea>
  </div>
  <p>
    <a class="parse btn btn-default">Parse</a>&nbsp;<a class="save btn btn-default">Save</a>
  </p>

  <div class="row">
    <div class="well col-md-10">
      <p><h4>Questions Must be in this format.</h4></p>
      <p>Technology has allowed humans to produce more food and reduce the chance of starvation by individuals in some countries. How has this advance created additional problems?</p>
      <p>a. The technology has allowed populations to continue to grow, creating the need for additional food.</p>
      <p>b. The technology caused salts to be deposited in soils.</p>
      <p>c. The technology caused the false belief that the problem was solved forever.</p>
      <p>d. All of these.</p>
      <p>ANS: A</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5">
      <p><h4>Questions</h4></p>
    </div>
  </div>


  <div class="row">
    <div class="col-md-1">
      <p><a class="add_question btn btn-xs btn-success">Add</a></p>
    </div>
  </div>
  <div id="question_container">

    <?php if(isset($reading_comprehension) && count($reading_comprehension->questions)):?>

    <?php foreach($reading_comprehension->questions as $q):?>

    <div class="row">
      <div class="col-md-1">
        <button type="button" class="btn btn-xs btn-warning remove_question">Remove</button>
      </div>
      <div class="col-md-8 questions">
        <input type="hidden" class="question_id" name="question_id" value="<?php echo $q->id;?>">
        <p><textarea class="form-control" class="sp_question" cols="80" rows="10" name="question"><?php echo $q->original_text;?></textarea></p>
      </div>
    </div>

    <?php endforeach;?>

    <?php else:?>

    <div class="row">
      <div class="col-md-1">
        <button type="button" class="btn btn-xs btn-warning remove_question">Remove</button>
      </div>
      <div class="col-md-8 questions">
        <p><textarea class="form-control" class="sp_question" cols="80" rows="10" name="question"></textarea></p>
      </div>
    </div>

    <?php endif;?>

  </div>
</form>
</div>
<div class="col-md-5">
  <div class="row">
    <div class="col-md-5">
      <p><h4>Parsed Result</h4></p>
    </div>
  </div>
	<span id="parsed_result">
	</span>
</div>
</div>