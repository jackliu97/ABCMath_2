<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
<div class="col-md-1">&nbsp;</div>
<div class="col-md-5">
<form role="form" id="add_word_form">
  <div class="row" id="save_progress" style="display:none;">
    <div class="well col-md-10">
      <div id="save_progress_message">Saving progress... </div>
      <div class="progress">
      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
        <span class="sr-only">0% Complete</span>
      </div>
    </div>
    </div>
  </div>
  <div class="form-group">
    <label>Input word and definition</label>
    <textarea class="form-control" cols="80" rows="20" id="word_text"></textarea>
  </div>
  <a id="parse" class="btn btn-default">Parse</a>
  <button type="submit" class="btn btn-default">Save</button>
</form>
</div>
<div class="col-md-5">
	<label>Parse result</label>
	<span id="parse_result">
	</span>
</div>
<div class="col-md-1">&nbsp;</div>
</div>