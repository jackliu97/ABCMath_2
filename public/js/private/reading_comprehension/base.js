( function($, $C) {
	'use strict';

	function remove_question_action(obj){
		obj.closest('.row').remove();
	}

	function num_to_alpha(i){
		var ord = 64;
		return String.fromCharCode(ord + i);
	}

	function alp_to_number(alp){
		return string.charCodeAt(alp.toLowerCase()) - 64;
	}

	function show_parsed(data){
		var html = '<div class="row"><div class="col-sm-10 col-sm-offset-1"><span class="label label-success">Verify that the line numbers are correct</span></div></div>';
		var count = 1;

		$.each(data.lines, function(ind, val){
				if($.trim(val) === ''){
					return;
				}

				var line = count;
				if(count % 5==0){
					line = '<span class="label label-danger">' + count + '</span>';
				}else{
					line = '<span class="label label-default">' + count + '</span>';
				}

				html += '<div class="row">';
				html += '<div class="col-sm-1 text-right">'+ line + '</div>';
				html += '<div class="col-sm-10 line">'+ val + '</div>';
				html += '</div>';
				count += 1;
			});

		$.each(data.questions, function(ind, val){
			html += '<div class="row">';
			html += '<div class="col-md-7"><i><b>Question '+ (ind+1) + '&nbsp;</b></i><p class="question">' + val.text + '</p></div>';

			$.each(val.choices, function(cind, cval){
				var answer = '';
				if(cval.is_answer == '1'){
					answer = '<span class="label label-danger">Answer</span>&nbsp;';
				}

				html += '<div class="col-md-7"><i>'+ num_to_alpha(cind+1) + '.&nbsp;</i>' + answer + cval.text + '</div>';
			});

			html += '</div>';
		});

		$('#parsed_result').empty();
		$('#parsed_result').append(html);
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

			var submit_data = {
					'paragraph': $('#paragraph').val(),
					'questions': []
				};

			$.each($('#question_container').find('.questions'), function(ind, val){
				submit_data['questions'].push({
					question_id: $(val).find('.question_id').val(),
					question: $(val).find('textarea').val()
					});
			});

			$.ajax({
				type:'POST',
				url:'/reading_comprehension/parse',
				data: submit_data,
				success: function(data){
					if(data.success){
						show_parsed(data.parsed_result);
					}
				}
			});
		});

		$('.save').on('click', function(){

			var submit_data = {
					'reading_comprehension_id': $('#reading_comprehension_id').val(),
					'paragraph': $('#paragraph').val(),
					'questions': [],
					'keyword': []
				};

			$.each($('#question_container').find('.questions'), function(ind, val){

				var question = $(val).find('textarea').val();
				if(question === ''){
					return true;
				}

				submit_data['questions'].push(
					{
						'question_id': $(val).find('.question_id').val(),
						'question': question
					}
						);
			});

			$.each($('.selected_keyword'), function(index, value){
				submit_data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});


			$.ajax({
				type:'POST',
				url:'/reading_comprehension/save',
				data: submit_data,
				success: function(data){

					console.log(data);

					if(data.success){
						if(data.is_new === true){
							window.location = "/reading_comprehension/create/" + data.reading_comprehension_id;
						}else{
							$C.success('Question successfully saved!');
						}
					}else{
						$C.error(data.message);
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

}(jQuery, COMMON) );