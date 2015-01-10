( function($) {
	'use strict';

	$( document ).ready(function() {

		$('#question_container').on('click', '.vocabulary_answer', function(){

			console.log($(this).attr('wordid'));

			console.log($('#solution').val());
			$('.alert_container').empty();
			if($(this).attr('wordid') != $('#solution').val()){
				$('.alert_container').append(
					$('<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span>&nbsp;Incorrect.</div>')
					);
			}else{
				$('.alert_container').append(
					$('<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;Correct!</div>')
					);
			}

		});

		$('.load_students').on('click', function(){
			var $student_container = $('#student_container');
			$student_container.empty();

			$student_container.append(
								'<a class="list-group-item">Loading... </a>'
								);

			var submit_data = {
				'class_id': $('#class_id').val(),
				'num_students': $('#num_students').val()
			};

			$.ajax({
				type:'POST',
				url:'/teacher_dashboard/generate_random_students',
				data: submit_data,
				success: function(data){

					if(data.success){
						 $student_container.empty();

						$.each(data.students, function(index, value){
							$student_container.append(
								'<a class="list-group-item load_question" student_id="' + value['id'] + '">' + 
									value['first_name'] + '&nbsp;' + 
									value['last_name'] + 
								'</a>'
								);
						});

					}else{
						console.log(data.message);
					}
				}
			});

			return false;

		});


		$('#student_container').on('click', '.load_question', function(){
			var $question_container = $('#question_container');
			var submit_data = {
				'question_type': $('#question_type').val(),
				'student_id': $(this).attr('student_id')
			};

			$.ajax({
				type:'POST',
				url:'/teacher_dashboard/generate_random_question',
				data: submit_data,
				success: function(data){

					$question_container.empty();

					if(data.success){
						console.log(data);

						$question_container.empty();
						$question_container.html(data.question);

					}else{
						console.log(data.message);
					}
				}
			});

		});


	});
}(jQuery) );