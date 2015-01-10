
function populate_stats(keywords, words){

	var html = '';
	$.each(keywords, function(k, keyword){
		var score = (words[keyword['id']].length / 20) * 100;
		html += '<label><button type="button" class="btn btn-success btn-xs" keyword_id="' + 
					keyword['id'] + '">' + keyword['word'] + '</button></label>';
		html += '&nbsp;<span class="badge pull-right">' + words[keyword['id']].length + '</span>';
		html += '<div class="progress">';
		html += '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' + 
					score + '" aria-valuemin="0" aria-valuemax="100" style="width: '+
					score + '%">';
		html += '<span class="sr-only">' + 
					words[keyword['id']].length +' words found</span>';
		html += '</div></div>';
	});

	$('#parse_result').html(html);
	$('#parse_result').on( 'click', 'button', function (){
		highlight_words(this, words);
	});


}

function highlight_words(obj, words){
	var $this = $(obj);
	$('#parse_result').find('button').prop('disabled',false);
	$this.prop('disabled',true);
	var text = $('#original_text').val();
	var html = '<table class="table">';
	var id = 0;

	$.each(words[$this.attr('keyword_id')], function(k, word){
		id += 1;
		text = text.replace(word['word'], '<b class="text-danger highlighted_word">' + word['word'] + '</b>');
		html += '<tr>';
		html += '<td class="word_container"><b>' + word['word'] + '</b></td>';
		//html += '<td class="word_definition">' + word['definition'] + '</td>';
		html += '</tr>';
	});
	html += '</table>';
	$('#passage_text').html(text);
	$('#parse_result_words').html(html);

}

$( document ).ready(function(){

	$.ajax({
		type:'POST',
		url:'/passage/parse_passage_all',
		data: {'article_id': $('#article_id').val()},
		success: function(data){
			if(data.success){
				populate_stats(data.keywords, data.words);
			}else{
				$('#parse_result').html(
					'<div class="alert alert-danger"><b>Error getting passage information.</b><br />' + 
						data.message + 
					'</div>');
			}
		}
	});
	return false;
});