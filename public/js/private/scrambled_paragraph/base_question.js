( function($) {
	'use strict';

	function alpha_to_num(i){
		return i.toUpperCase().charCodeAt(0) - 64;
	}
	function num_to_alpha(i){
		return String.fromCharCode(64 + i);
	}


	$( document ).ready(function() {


		$('#parse').on('click', function(){
			$('#split_result').empty();

			var text_split = $('#paragraph').val().split(/\n[ABCDEabcde]. /);
			var text_sentences = [];
			var split_html = '';
			var answer = [];

			//set lead sentence.
			text_sentences.push(text_split[0]);

			//if last line contains the answer "ANS: A, B, C, D, E", we sepeate it here.
			var last_line = text_split.pop().split('ANS: ');
			text_split.push(last_line[0].replace('\n\r'));

			//if "ANS: ..." is provided, we format it into array format.
			//else it's not provided, assume a, b, c, d, e.
			if(last_line.length == 2){
				answer = last_line[1].split(', ');
			}else{
				answer = ['A', 'B', 'C', 'D', 'E'];
			}


			$.each(answer, function(ind, val){
				text_sentences.push(text_split[alpha_to_num(val)]);
			});

			$.each(text_sentences, function(index, value){
				var letter = '';
				if(index > 0){
					letter = '<span class="label label-primary">' + num_to_alpha(index) + '</span>';
				}

				split_html += '<p>' + letter + '&nbsp;<span class="parsed">' + value + '</p>';

			});
			$('#paragraph_original').val(text_sentences.join(''));
			$('#split_result').append(split_html);


		});

		$('#scrambled_paragraph_form').submit(function(){

			var submit_data = {
				'paragraph': $('#paragraph_original').val(),
				'scrambled_paragraph_id': $('#scrambled_paragraph_id').val()
			};

			submit_data['pieces'] = [];

			$.each($('.parsed'), function(index, value){
				submit_data['pieces'].push($(value).text());
			});

			submit_data['keyword'] = [];
			$.each($('.selected_keyword'), function(index, value){
				submit_data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});

			$.ajax({
				type:'POST',
				url:'/scrambled_paragraph/save',
				data: submit_data,
				success: function(data){

					if(data.success){
						window.location = "/scrambled_paragraph/list_all";
					}else{
						alert(data.message);
					}
				}
			});


			return false;
		});

	});
}(jQuery) );