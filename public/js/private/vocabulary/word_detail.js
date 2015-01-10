( function($) {
	'use strict';
	function populate_info(word){
		var html = '<h4>Additioal Information from Merriam Webster</h4>';
		console.log(word);
		$.each(word, function(ind, val){
			html += '<div class="well" id="more_definition">';
			html += '<b>' + val['word'] + '</b>';
			if(val['parts_of_speech']){
				html += '&nbsp;(' + val['parts_of_speech'] + ')';
			}

			if(val['definition']){
				html += '&nbsp;' + val['definition'];
			}

			html += '</div>';
		});

		$('#parse_result').html(html);

	}


	$( document ).ready(function(){

		$.ajax({
			type:'POST',
			url:'/vocabulary/extract_definition',
			data: {
					'word_id': $('#word_id').val(),
					'source':'MerriamWebster'
					},
			success: function(data){
				if(data.success){
					populate_info(data.word);
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
}(jQuery) );