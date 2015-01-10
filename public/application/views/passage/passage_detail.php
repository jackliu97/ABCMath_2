<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-5">
<form role="form" id="add_passage_form">
  <input type="hidden" name="article_id" id="article_id" value="<?php echo $article_id;?>">
  <div class="form-group">
    <label>Passage Title</label>
    <div class="well"><?php echo $title;?></div>
  </div>
  <div class="form-group">
    <label>Input Passage</label>
    <div style="display:none;"><textarea id="original_text"><?php echo nl2br($article);?></textarea></div>
    <div class="well" id="passage_text"><?php echo nl2br($article);?></div>
  </div>
  <a href="/passage/list_passage" class="btn btn-default">Back</a>
</form>
</div>
<div class="col-md-5">
  <label>Passage Information</label>
	<div id="parse_result" class="well"></div>
  <label>Passage Vocabularies</label>
  <div id="parse_result_words" class="well"></div>
</div>
<div class="col-md-1">&nbsp;</div>
</div>