( function($, $C) {
	'use strict';

	function reset_note(){
		$('#notes').val('');
		$('#note_id').val('');
	}

	function reset_class_datatable(){
		$('.datatable_class_container').empty();
		$('.datatable_class_container').html(
			'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-striped" id="classes_table">' +
			'<thead><tr>' + 
			'<th>Class ID</th>' + 
			'<th>Class name</th>' + 
			'<th>Subject</th>' + 
			'<th>Teacher</th>' + 
			'<th>Start Time</th>' + 
			'<th>End Time</th>' + 
			'<th>Days</th>' + 
			'</tr></thead><tbody></tbody></table>');
	}

	function reset_note_datatable(){
		$('.datatable_note_container').empty();
		$('.datatable_note_container').html(
			'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-striped" id="note_table">' +
			'<thead><tr>' + 
			'<th></th>' + 
			'<th>Date Created</th>' + 
			'<th>Last Updated</th>' + 
			'<th>Note Added by</th>' + 
			'<th>Note Content</th>' + 
			'</tr></thead><tbody></tbody></table>');
	}

	function get_classes(all_classes){
		if(all_classes === true){
			var source = '/admin_dashboard/get_classes/';
		}else{
			var source = '/student_dashboard/get_classes/' + $('#student_id').val();
		}

		$('#classes_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": false,
			"sAjaxSource": source,
			"fnDrawCallback": function( oSettings ) {
				$('#classes_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#classes_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#classes_table_wrapper').find('#classes_table_previous').addClass('btn btn-default');
				$('#classes_table_wrapper').find('#classes_table_next').addClass('btn btn-default');
			}
		});
	}

	function get_notes(){

		$('#note_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": false,
			"sAjaxSource": '/student_dashboard/get_all_notes/' + $('#student_id').val(),
			"fnDrawCallback": function( oSettings ) {
				$('#note_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#note_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#note_table_wrapper').find('#note_table_previous').addClass('btn btn-default');
				$('#note_table_wrapper').find('#note_table_next').addClass('btn btn-default');
			}
		});
	}

	function get_names(){
		var submit_data = {};

		$.ajax({
			type:'POST',
			url:'/student_dashboard/all_students',
			data: submit_data,
			success: function(data){
				if(data.success){
					$( "#student_name" ).autocomplete({
						source: data.students
					});
				}else{
					$( "#student_name" ).autocomplete({
						source: []
					});
				}
			}
		});
	}

	function toggle(obj){
		if(obj.hasClass('glyphicon-chevron-down')){
			obj.removeClass('glyphicon-chevron-down');
			obj.addClass('glyphicon-chevron-up');
		}else{
			obj.removeClass('glyphicon-chevron-up');
			obj.addClass('glyphicon-chevron-down');
		}
	}

	$( document ).ready(function() {

		$('#student_name').focus();
		$('#registered_classes').addClass('active');
		get_names();
		reset_class_datatable();
		get_classes(false);

		reset_note_datatable();
		get_notes();

		$('#notes_collapsable').click(function(){
			$('.notes_collapsable').collapse('toggle');
		});

		$('#class_collapsable').click(function(){
			$('.class_collapsable').collapse('toggle');
		});

		$('#add_note').on('click', function(){
			reset_note();
			$('#new_note_modal').modal('show');
		});

		$('.datatable_class_container').on('click', '.class_detail', function(){
			window.location = '/class_dashboard/info/' + $(this).attr('class_id');
		});

		$('.datatable_note_container').on('click', '.edit_note', function(){
			reset_note();
			$('#new_note_modal').modal('show');
			var note_id = $(this).attr('note_id');

			$.ajax({
				type:'POST',
				url:'/student_dashboard/get_one_note',
				data: {
					'note_id': note_id
				},
				success: function(data){
					if(data.success){
						$('#notes').val(data.notes);
						$('#note_id').val(note_id);
					}else{
						$C.error(data.message, $('#notes_error'));
						return false;
					}
				}
			});
			return false;

		});

		$('.datatable_note_container').on('click', '.note_detail', function(){
			var $this = $(this);
			var note_id = $(this).attr('note_id');
			var $tr = $this.closest('tr');

			toggle($this);
			$this.prop('disabled',true);
			if($tr.next().hasClass('full_notes')){

				if($tr.next().is( ":hidden" )){
					$tr.next().show();
				}else{
					$tr.next().hide();
				}
				$this.prop('disabled',false);
				return false;
			}

			$.ajax({
				type:'POST',
				url:'/student_dashboard/get_one_note',
				data: {
					'note_id': note_id
				},
				success: function(data){
					if(data.success){
						console.log(data.notes_parsed);
						var sOut = '<tr class="full_notes"><td colspan="5">';
						sOut += '<div class="well">';
						sOut += data.notes_parsed;
						sOut += '</div>';
						sOut += '</td></tr>';
						$tr.after(sOut);
						$tr.find('button').prop('disabled',false);
					}else{
						$C.error(data.message, $('#notes_error'));
						return false;
					}
				}
			});
			return false;

		});

		$('.datatable_note_container').on('click', '.remove_note', function(){
			if(!confirm('Are you sure you want to delete this note?')){
				return false;
			}
			var note_id = $(this).attr('note_id');
			$.ajax({
				type:'POST',
				url:'/student_dashboard/delete_note',
				data: {
					'note_id': note_id
				},
				success: function(data){
					if(data.success){
						reset_note_datatable();
						get_notes();
					}else{
						$C.error(data.message, $('#notes_error'));
						return false;
					}
				}
			});
			return false;

		});

		$('#note_form').on('submit', function(){
			$.ajax({
				type:'POST',
				url:'/student_dashboard/save_notes',
				data: {
					'student_id': $('#student_id').val(),
					'note_id': $('#note_id').val(),
					'notes': $('#notes').val()
				},
				success: function(data){
					if(data.success){
						$('#new_note_modal').modal('hide');
						reset_note_datatable();
						get_notes();
					}else{
						$C.error(data.message, $('#notes_error'));
						return false;
					}
				}
			});
			return false;

		});

		$('#all_classes').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}

			$('button').removeClass('active');
			$(this).addClass('active');
			reset_class_datatable();
			get_classes(true);
		});

		$('#registered_classes').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}

			$('button').removeClass('active');
			$(this).addClass('active');
			reset_class_datatable();
			get_classes(false);
		});

		$('#student_name_form').submit(function(){
			var student_id = $('#student_name').val();
			$.ajax({
				type:'POST',
				url:'/admin_dashboard/check_student',
				data: {'student_id': student_id},
				success: function(data){
					if(data.success){
						window.location = '/student_dashboard/info/' + student_id;
					}else{
						return false;
					}
				}
			});
			return false;
		})
	});
}(jQuery, COMMON) );