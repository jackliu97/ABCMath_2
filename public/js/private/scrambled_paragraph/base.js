( function($) {
	'use strict';

	$( document ).ready(function(){
		$('.list-search').on('submit', function(){

			var search_text = $('.search-text').val();
			$.each($('.full-text'), function(ind, val){

				if($(val).html().indexOf(search_text) != -1){
					$(this).closest('.question').show();
				}else{
					$(this).closest('.question').hide();
				}

			});

			$('.search-text').focus();

			return false;

		});
		$('.create_exam').on('click', function(){
			var questions = $(this).closest('.list-group').find('.selected_question');
			var question_id = [];
			var option_start = [];
			
			$.each(questions, function(index, value){
				question_id.push($(value).attr('questionid'));
				option_start.push($(value).find('.option_start').html());
			});

			$.ajax({
				type:'POST',
				url:'/scrambled_paragraph/create_exam',
				data: 	{
						'question_id':question_id,
						'option_start':option_start,
						'exam_name':$('#exam_name').val()
						},
				success: function(data){
					if(data.success){
						window.open("/create_exam/scrambled_paragraph/" + data.exam_id,
									'_blank',
									'scrollbars=yes');
					}else{
						console.log(data.message);
					}
				}
			});
		});

		$('.edit').on('click', function(){
			var id = $(this).attr('questionid');
			window.location = "/scrambled_paragraph/create_from_question/" + id;
		});

		$('.select').on('click', function(){
			var container = $('#exam_container');
			container.append('<a class="list-group-item selected_question" questionid="' + 
								$(this).attr('questionid')
								+ '" nowrap>' + 
								'<button type="button" class="btn btn-warning btn-xs remove_selected">Remove</button>&nbsp;' + 
								'<button type="button" class="btn btn-warning btn-xs option_start">A</button>' + 
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

			container.find('a[questionid=' + $(this).attr('questionid') + ']').find('.option_start').on('click', function(){
				if($(this).html() == 'A'){
					$(this).html('Q');
				}else{
					$(this).html('A');
				}
			});

		});

		$('.delete').on('click', function(){
			if(!confirm('Are you sure you want to delete this record?')){
				return false;
			}
			var submit_data = {
				'q_type': $(this).attr('questiontype'),
				'q_id': $(this).attr('questionid')
			};

			$.ajax({
				type:'POST',
				url:'/scrambled_paragraph/delete',
				data: submit_data,
				success: function(data){

					if(data.success){
						$('#panel_q_' + data.removed_id).remove();

					}else{
						alert(data.message);
					}
				}
			});

		});

	});
}(jQuery) );