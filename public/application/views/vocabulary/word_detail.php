<div class="row">
    <div class="col-md-1">
        &nbsp;
    </div>
</div>
<div class="row">
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-5">
        <form role="form" id="add_passage_form">
            <input type="hidden" name="word_id" id="word_id" value="<?php echo $word_id;?>">
            <div class="form-group">
                <label>Word</label>
                <div class="well">
                    <h2>
                        <?php echo ucwords($word);?>
                    </h2>
                </div>
            </div>
            <div class="form-group">
                <label>Definition</label> <?php
                      if(count($definition)):
                        foreach($definition as $d):
                    ?>
                <div class="well" id="definition">
                    <b><?php echo $d->word;?></b> (<?php echo $d->parts_of_speech;?>) <?php echo nl2br($d->definition);?>
                </div><?php
                        endforeach;
                      endif;
                    ?>
            </div><a href="/vocabulary/list_words" class="btn btn-default">Back</a>
        </form>
    </div>
    <div class="col-md-5">
        <label>More word information</label>
        <div id="parse_result" class="well">
            <div class="alert alert-info">
                <b>Retrieveing word information...</b><br>
            </div>
        </div>
    </div>
    <div class="col-md-1">
        &nbsp;
    </div>
</div>