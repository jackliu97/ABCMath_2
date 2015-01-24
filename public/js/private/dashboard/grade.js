( function($, $C, $M) {
	'use strict';
	var tabindex = 1;
	var ACCEPTABLE_GRADE = ['inc', 'abs']

	function _saveContents(){
		if(_validateContents() === false){
			$C.error('You have an invalid grade. The input is marked in red.');
			return false;
		}

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

	function _validateContents(){
		var all_values_valid = true;

		$('.process_grade').each(function(index, value){
			var $input = $(value);
			var input_val = $.trim($input.val());

			if(input_val === ''){
				return true;
			}

			if($.isNumeric(input_val)){
				return true;
			}

			if(ACCEPTABLE_GRADE.indexOf(input_val) === -1){
				all_values_valid = false;
				$input.addClass('invalid-value');
				return true;
			}

		});

		return all_values_valid;
	}


	function _makeInput($span, tabindex){
		var assignment_id = $span.attr('assignment_id');
		var student_id = $span.attr('student_id');
		var grade_id = $span.attr('grade_id');
		var grade = $span.attr('grade');

		var $container = $span.parent();
		$span.replaceWith(
			$('<input>').attr({
				'class':'process_grade',
				'assignment_id':assignment_id,
				'student_id':student_id,
				'grade_id':grade_id,
				'grade':grade,
				'value':grade,
				'size':4,
				'tabindex': tabindex
				})
			);

		$container.find('input').focus();

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

	function _sibling($obj, direction){
		if(direction === 'left'){
			var $sibling = $obj.closest('td').prev();
		}

		else if(direction === 'right'){
			var $sibling = $obj.closest('td').next();
		}

		else if(direction === 'up'){
			var $tr = $obj.closest('tr');
			var $td = $obj.closest('td');
			var $sibling = $tr.prev().find('td:eq(' + $td.index() + ')');
		}

		else if(direction === 'down'){
			var $tr = $obj.closest('tr');
			var $td = $obj.closest('td');
			var $sibling = $tr.next().find('td:eq(' + $td.index() + ')');

		}else{
			return false;
		}

		if($sibling.find('input').empty()){
			$sibling.find('span').click();
		}

		$sibling.find('input').focus();
	}

	$( document ).ready(function() {

		$M.stopCallback = function() {
			return false;
		}

		$M.bind(['mod+s', 'ctrl-s'], function(e){
			e.preventDefault();
			_saveContents();
		})

		.bind(['down'], function(e){
			e.preventDefault();
			_sibling($(e.target), 'down');
		})

		.bind(['up'], function(e){
			e.preventDefault();
			_sibling($(e.target), 'up');
		})

		.bind(['left'], function(e){
			e.preventDefault();
			_sibling($(e.target), 'left');
		})

		.bind(['right'], function(e){
			e.preventDefault();
			_sibling($(e.target), 'right');
		})

		;

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
						tabindex += 1;
						_makeInput($(this), tabindex);
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