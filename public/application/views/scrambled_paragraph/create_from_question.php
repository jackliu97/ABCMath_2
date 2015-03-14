<div class="row">
<div class="col-md-5 col-md-offset-1">
<form role="form" id="scrambled_paragraph_form">
  <div class="form-group">
    <label>Input Question</label>
    <input id="scrambled_paragraph_id" type="hidden" value="<?php echo $scrambled_paragraph_id;?>">
    <input id="paragraph_original" type="hidden">
    <textarea class="form-control" cols="80" rows="20" id="paragraph"><?php echo (isset($paragraph_text) ? $paragraph_text : '');?></textarea>
  </div>
  <a id="parse" class="btn btn-default">Parse</a>
  <button type="submit" class="btn btn-default">Save</button>
</form>
</div>
<div class="col-md-5">
	<label>Split result</label>
	<span id="split_result">
	</span>
</div>
<div class="col-md-1">&nbsp;</div>
</div>