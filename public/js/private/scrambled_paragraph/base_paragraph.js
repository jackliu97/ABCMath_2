( function($) {
	'use strict';
	function merge_array_element(list, p1, p2){
		list[p1] = list[p1] + ' ' + list[p2];
		return remove_element(list, p2);
	}

	function remove_element(list, ind){
		var new_list = [];
		for(var i=0, l=list.length; i<l; i++){
			if(i === ind){
				continue;
			}

			new_list.push(list[i]);
		}

		return new_list;
	}

	function find_shortest_merge(list){
		var min_l = 0;
		var min_p = 0;

		//find positon of shortest.
		for(var i=0, l=list.length; i<l; i++){
			if(list[i] == null){
				continue;
			}

			if(min_l == 0){
				min_l = list[i].length;
				continue;
			}

			if(list[i].length < min_l){
				min_l = list[i].length;
				min_p = i;
			}
		}
		//compare length of neighbors.

		//if min_p is first
		if(min_p == 0){

			return merge_array_element(list, min_p, min_p + 1);
		}

		//if min_p is first
		else if(min_p == list.length-1){

			return merge_array_element(list, min_p - 1, min_p);
		}


		else if(list[min_p + 1] > list[min_p-1]){
			
			return merge_array_element(list, min_p, min_p + 1);

		}else{

			return merge_array_element(list, min_p - 1, min_p);

		}


	}

	function num_to_alpha(i){
		return String.fromCharCode(64 + i);
	}


	$( document ).ready(function() {


		$('#parse').on('click', function(){

			var text = $('#paragraph').val();
			$('#paragraph_original').val(text);
			var new_text = '';

			for(var i=0, l=text.length; i<l; i++){

				if(text[i] == '.' && text[i-1] != '.' && text[i+1] != '.' && (text[i+1] == ' ' || text[i+1] == '\n')){
					new_text += '.$$';
				}else{
					new_text += text[i].replace(/[\n\r]/g, '');
				}

			}

			$('#paragraph').val(new_text);

		});


		$('#split').on('click', function(){

			var text_split = $('#paragraph').val().split('$$');
			$('#split_result').empty();

			var text_sentences = new Array();
			var sentence = '';
			var split_html = '';
			for(var i=0, l=text_split.length; i<l; i++){
				
				sentence += (text_split[i]);
				
				//exceptions.
				if(sentence[sentence.length-2] == 'r' && sentence[sentence.length-3] == 'M'){
					continue;
				}
				
				
				if(sentence.length > 10){
					sentence.replace(/[\n\r]/g, '');
					text_sentences.push(sentence.trim());
					sentence = '';
				}
			}

			while(text_sentences.length > 6){
				text_sentences = find_shortest_merge(text_sentences);
			}

			$.each(text_sentences, function(index, value){
				var letter = '';
				if(index > 0){
					letter = '<span class="label label-primary">' + num_to_alpha(index) + '</span>';
				}

				split_html += '<p>' + letter + '&nbsp;<span class="parsed">' + value + '</p>';

			});

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