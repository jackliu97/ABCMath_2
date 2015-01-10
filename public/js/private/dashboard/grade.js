( function($, $C, $M) {
	'use strict';

	function _saveContents(){
		var grade_data = [];
		$('.process_grade').each(function(index, value){
			var $input = $(value);
			grade_data.push({
				'assignment_id':$input.attr('assignment_id'),
				'student_id':$input.attr('student_id'),
				'grade_id':$input.attr('grade_id'),
				'grade':$input.val()
			});
		});

		$.ajax({
			type:'POST',
			url:'/grade_dashboard/process_grades',
			data: {
				'grade_data': grade_data,
			},
			success: function(data){
				if(data.success){
					location.reload();
				}else{
					$C.error(data.message);
				}
			}
		});

	}

	function _makeInput($span){
		var assignment_id = $span.attr('assignment_id');
		var student_id = $span.attr('student_id');
		var grade_id = $span.attr('grade_id');
		var grade = $span.attr('grade');

		$span.replaceWith(
			$('<input>').attr({
				'class':'process_grade',
				'assignment_id':assignment_id,
				'student_id':student_id,
				'grade_id':grade_id,
				'grade':grade,
				'value':grade,
				'size':4
				})
			);
	}

	function _undoInput($input){
		var assignment_id = $input.attr('assignment_id');
		var student_id = $input.attr('student_id');
		var grade_id = $input.attr('grade_id');
		var grade = $input.attr('grade');

		$input.replaceWith(
			$('<span>').attr({
				'class':'process_grade',
				'assignment_id':assignment_id,
				'student_id':student_id,
				'grade_id':grade_id,
				'grade':grade
				}).html(grade)
			);
	}

	$( document ).ready(function() {

		$M.stopCallback = function() {
			return false;
		}

		$M.bind(['mod+s', 'ctrl-s'], function(e){
			e.preventDefault();
			_saveContents();
		});

		$('.class_dropdown').on('change', function(){
			window.location.href='/grade_dashboard/grade/' + $(this).val();
			return false;
		});

		$('#gradeContainer').on('click', '.header', function(){
			var $this = $(this);
			var editing = $this.attr('editing');

			if(editing != 1){
				$(this).attr('editing', '1');
				$('span[assignment_id=' + $(this).attr('assignment_id') + ']')
					.each( function (index, value){
						_makeInput($(this));
					});
			}else{
				if(!confirm('Are you sure you want to undo your changes?')){
					return false;
				}
				$(this).attr('editing', '0');
				$('input[assignment_id=' + $(this).attr('assignment_id') + ']')
					.each( function (index, value){
						_undoInput($(this));
					});
			}
		});


		$('#gradeContainer').on('click', '.grade_action', function(){
			_makeInput($(this).find('span'));

			return false;

		});

		$('.save').on('click', function(){
			_saveContents();
		});

	});

}(jQuery, COMMON, Mousetrap) );