( function($) {
	'use strict';

	function remove_question_action(obj){
		obj.closest('.row').remove();
	}

	function num_to_alpha(i){
		var ord = 64;
		return String.fromCharCode(ord + i);
	}

	function build_submit_data(){
		var paragraph = $('#paragraph').val();
		var questions = $('#parsed_result').find('.question_info');
		var data = {'paragraph':paragraph, 
					'reading_comprehension_id':$('#reading_comprehension_id').val()};


		data['lines'] = [];
		data['questions'] = [];

		$.each(paragraph.split('\n'), function(ind, val){
			data['lines'].push(val.trim());
		});

		$.each(questions, function(ind, val){
			var q = {};
			var $val = $(val);
			var ans = $val.find('.answer').html().trim();
			q['id'] = $val.find('.question_id').val();
			q['question'] = $val.find('.question').html();
			q['choice'] = [];
			q['original_text'] = $val.find('textarea').val();
			
			$.each($val.find('.choice'), function(i, v){
				q['choice'].push($(v).text().trim());
				if(num_to_alpha(i+1) == ans){
					q['is_answer'] = i;
				}
			});
			data['questions'].push(q);
		});

		data['keyword'] = [];
			$.each($('.selected_keyword'), function(index, value){
				data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});

		return data;
	}

	function alp_to_number(alp){
		return string.charCodeAt(alp.toLowerCase()) - 64;
	}

	$( document ).ready(function() {

		//ensure only one of each group is checked.
		$('.unique').on('click', function(){
			$(this).closest('.questions').find('.unique').filter(':checked').not(this).removeAttr('checked');
		});

		$('.add_question').on('click', function(){

			var container = $('#question_container');
			var html = '';

			html += '<div class="row">';
			html += '<div class="col-md-1">';
			html += '<button type="button" class="btn btn-xs btn-warning remove_question">Remove</button>';
			html += '</div>';
			html += '<div class="col-md-8 questions">';
			html += '<input type="hidden" class="question_id" name="question_id" value="">';
			html += '<p><textarea class="form-control" class="sp_question" cols="80" rows="10" name="question"></textarea></p>';
			html += '</div></div>';
			$('#question_container').append(html);

			$('.remove_question').on('click', function(){
				remove_question_action($(this));
			});
		});

		$('.remove_question').on('click', function(){
			remove_question_action($(this));
		});

		$('.parse').on('click', function(){
			var paragraph = $('#paragraph').val().split('\n');
			var questions = $('#question_container').find('.questions');
			var html = '';
			var line = '';
			var count = 0;
			$.each(paragraph, function(ind, val){
				if($.trim(val) == ''){
					return;
				}

				count += 1;

				if(count % 5==0){
					line = 'Line ' + count;
				}else{
					line = '';
				}

				html += '<div class="row">';
				html += '<div class="col-md-2 text-right"><b><i>'+ line + '</i></b></div>';
				html += '<div class="col-md-10 line">'+ val + '</div>';
				html += '</div>';
			});


			$.each(questions, function(ind, val){

				var original_text = $(val).find('textarea').val();
				var question_id = $(val).find('.question_id').val();
				var question = '';
				var choices = [];
				var answer = '';

				if(!original_text){
					return false;
				}


				$.each(original_text.split('\n'), function(i, p){
					p = $.trim(p);
					var head = $.trim(p.substring(0, 3));

					if(head.match(/[a-zA-Z]\./g) != null){ //match "a.", "b.", "c.", ... etc.
						choices.push($.trim(p.substring(3, p.length)));

					}else if(head.match(/ANS/g) != null){ //match anything that starts with "ANS"
						answer = $.trim(p.substring(4, p.length)).toUpperCase();

					}else{
						question += p;

					}

				});

				html += '<div class="row question_info">';
				html += '<div class="col-md-7"><i><b>Question '+ (ind+1) + '&nbsp;</b></i><p class="question">' + question + '</p></div>';
				
				$.each(choices, function(i, v){
					var ans = '';
					html += '<div class="col-md-7"><i>'+ num_to_alpha(i+1) + '.&nbsp;</i><span class="choice">' + v + '</span></div>';
				});
				html += '<input type="hidden" class="question_id" value="' + question_id + '">';
				html += '<textarea class="original_text" style="display:none;">' + original_text + '</textarea>';

				html += '<div class="col-md-7"><i>ANSWER:&nbsp;</i><span class="answer">' + answer + '</span></div></div>';
			});

			$('#parsed_result').empty();
			$('#parsed_result').append(html);


		});

		$('.save').on('click', function(){
			$.ajax({
				type:'POST',
				url:'/reading_comprehension/save',
				data: build_submit_data(),
				success: function(data){

					if(data.success){
						window.location = "/reading_comprehension/list_all";
					}else{
						console.log(data.message);
					}
				}
			});
		});

		$('.edit').on('click', function(){
			window.location = "/reading_comprehension/create/" + $(this).attr('questionid');
		});

		$('.delete').on('click', function(){
			$.ajax({
				type:'POST',
				url:'/reading_comprehension/delete',
				data: {'questionid':$(this).attr('questionid')},
				success: function(data){
					if(data.success){
						window.location = "/reading_comprehension/list_all";
					}else{
						console.log(data.message);
					}
				}
			});

		});


		$('.select').on('click', function(){
			var container = $('#exam_container');
			container.append('<a class="list-group-item selected_question" questionid="' + 
								$(this).attr('questionid')
								+ '" nowrap>' + 
								'<button type="button" class="btn btn-warning btn-xs remove_selected">Remove</button>' + 
								$(this).parent().find('.accordion-toggle').html()
								+ '</a>');
			$(this).hide();
			$('.num_selected').text(container.find('a').length);

			$('.remove_selected').on('click', function(){
				var id = $(this).parent().attr('questionid');
				$(this).parent().remove();
				$('.select[questionid="' + id + '"]').show();
				$('.num_selected').text(container.find('a').length);

			});

		});

		$('.create_exam').on('click', function(){
			var questions = $(this).closest('.list-group').find('.selected_question');
			var question_id = [];
			var option_start = [];
			
			$.each(questions, function(index, value){
				question_id.push($(value).attr('questionid'));
				option_start.push('A');
			});

			$.ajax({
				type:'POST',
				url:'/reading_comprehension/create_exam',
				data: 	{
						'question_id':question_id,
						'option_start':option_start,
						'exam_name':$('#exam_name').val()
						},
				success: function(data){
					if(data.success){
						window.open("/create_exam/reading_comprehension/" + data.exam_id);
					}else{
						console.log(data.message);
					}
				}
			});
		});
	});

}(jQuery) );