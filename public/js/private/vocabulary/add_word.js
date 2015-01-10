( function($) {
	'use strict';

	function set_words(word_data){

		if(word_data.length == 1){
			return '<p><span class="word">' + word_data[0].trim().toLowerCase() + '</span></p>';
		}

		if(word_data.length == 2){
			return '<p><b><span class="word">' + word_data[0].trim().toLowerCase() + '</span></b>&nbsp;' + 
								'<span class="definition">'+ word_data[1].trim() + '</span></p>';
		}

		if(word_data.length == 3){
			return '<p><b><span class="word">' + word_data[0].trim().toLowerCase() + '</span></b>&nbsp;' + 
								'(<span class="usage">' + word_data[1].trim() + '</span>)&nbsp;' + 
								'<span class="definition">'+ word_data[2].trim() + '</span></p>';
		}

		return '<p>Bad Format detected, please fix these lines and try again.</p>'

	}

	function init_progressbar(){
		var percentage = 0;
		$('.progress-bar').attr('aria-valuenow', percentage);
		$('.progress-bar').css('width', percentage + '%');
		$('.progress-bar').html('<span class="sr-only">' + percentage + '% Complete</span>')
	}



	$( document ).ready(function(){

		$('#parse').on('click', function(){

			$('#parse_result').empty();
			var parse_html = [];
			var error = ['<p>Bad Format detected, please fix these lines and try again.</p>'];
			var count = 0;
			$.each($('#word_text').val().split(/\n/), function(ind, val){
				var word_data = val.split(/\t/);
				parse_html.push(set_words(word_data));
			});

			if(error.length > 1){
				$('#parse_result').append(error.join(''));
			}else{
				$('#parse_result').append('<p class="meta">' + count + ' words successfully parsed.</p>');
				$('#parse_result').append(parse_html.join(''));
			}

			return false;
		});

		$('#add_word_form').submit(function(){
			var BATCH_SIZE = 100;
			var word_batches = Array();
			var count = 0;
			var submit_data = {'words':[], 'keyword':[]};

			$('#save_progress').show();
			$('#save_progress_message').html('saving ... ');
			init_progressbar();

			$.each($('.selected_keyword'), function(index, value){
				submit_data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});

			/*
			* Create batches and push into word_batches.
			*/
			$.each($('#parse_result').find('p'), function(index, value){
				var $v = $(value);
				if($v.find('.word').html() == undefined){
					return true;
				}
				word = {
					'word': $v.find('.word').html(),
					'parts_of_speech': $v.find('.usage').html(),
					'definition': $v.find('.definition').html()
				}

				submit_data['words'].push(word);
				count += 1;
				if(count % BATCH_SIZE == 0){
					word_batches.push(submit_data);
					submit_data = {'words':[],
									'keyword':submit_data['keyword']
									};
				}
			});
			word_batches.push(submit_data);
			
			var batch_size = word_batches.length;
			var error = false;
			(function submit_batch(){
				if(error != false){
					$('#save_progress_message').html(error);
					return false;
				}
				if(word_batches.length == 0){
					$('#save_progress_message').html('Process Completed Successfully.');
					$('#parse_result').empty();
					$('#word_text').val('');
					return false;
				}

				var batch = word_batches.pop();

				$.ajax({
					url: '/vocabulary/save_word',
					success: function(data){
						console.log(data);
						if(!data.success){
							$('#save_progress_message').html(data.message);
							error = data.message;
							return false;
						}
						var percentage = 100 - Math.floor(word_batches.length/batch_size * 100);
						$('.progress-bar').attr('aria-valuenow', percentage);
						$('.progress-bar').css('width', percentage + '%');
						$('.progress-bar').html('<span class="sr-only">' + percentage + '% Complete</span>')
					},
					type:'POST',
					dataType: "json",
					data: batch,
					complete: submit_batch,
					timeout: 1000});
			})();
			return false;
		});
	});
}(jQuery) );