( function($, $C) {
	'use strict';

	function reset_class_datatable(){
		$('.datatable_class_container').empty();
		$('.datatable_class_container').html(
			'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-striped" id="classes_table">' +
			'<thead><tr>' + 
			'<th>Internal ID</th>' + 
			'<th>External ID</th>' + 
			'<th>Class name</th>' + 
			'<th>Subject</th>' + 
			'<th>Teacher</th>' + 
			'<th>Start Time</th>' + 
			'<th>End Time</th>' + 
			'<th>Days</th>' + 
			'</tr></thead><tbody></tbody></table>');
	}

	function reset_student_datatable(){
		$('.datatable_student_container').empty();
		$('.datatable_student_container').html(
			'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-striped" id="students_table">' +
			'<thead><tr>' + 
			'<th>Internal ID</th>' + 
			'<th>External ID</th>' + 
			'<th>Name</th>' + 
			'<th>Email</th>' + 
			'<th>Home Phone</th>' + 
			'<th>Cell Phone</th>' + 
			'<th>Class Name</th>' + 
			'</tr></thead><tbody></tbody></table>');
	}

	function get_students(type){
		$('#students_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": false,
			"sAjaxSource": '/admin_dashboard/get_students/' + type,
			"fnDrawCallback": function( oSettings ) {
				$('#students_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#students_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#students_table_wrapper').find('#classes_table_previous').addClass('btn btn-default');
				$('#students_table_wrapper').find('#classes_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
			"sWrapper": "dataTables_wrapper form-inline"
		} );
	}

	function get_classes(active){
		var class_status = active === true ? 'active_classes' : 'all_classes';

		$('#classes_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": false,
			"sAjaxSource": '/admin_dashboard/get_classes/' + class_status,
			"fnDrawCallback": function( oSettings ) {
				$('#classes_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#classes_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#classes_table_wrapper').find('#classes_table_previous').addClass('btn btn-default');
				$('#classes_table_wrapper').find('#classes_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
			"sWrapper": "dataTables_wrapper form-inline"
		} );
	}

	$( document ).ready(function() {
		$('#student_name').focus();
		$('#class_in_session').addClass('active');
		$('#all_students').addClass('active');
		reset_class_datatable();
		get_classes(true);

		reset_student_datatable();
		get_students('');

		$('.datatable_class_container').on('click', '.class_detail', function(){
			window.location = '/class_dashboard/info/' + $(this).attr('class_id');
			return false;
		});

		$('.datatable_student_container').on('click', '.student_detail', function(){
			window.location = 'student_dashboard/info/' + $(this).attr('student_id');
			return false;
		});

		$('#all_students').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}
			$('.student_panel').find('button').removeClass('active');
			$(this).addClass('active');
			reset_student_datatable();
			get_students('');
			return false;
		});

		$('#absent_students').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}
			$('.student_panel').find('button').removeClass('active');
			$(this).addClass('active');
			reset_student_datatable();
			get_students('Absent');
			return false;
		});

		$('#late_students').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}
			$('.student_panel').find('button').removeClass('active');
			$(this).addClass('active');
			reset_student_datatable();
			get_students('Tardy');
			return false;
		});

		$('#all_classes').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}

			$('.class_panel').find('button').removeClass('active');
			$(this).addClass('active');
			reset_class_datatable();
			get_classes(false);

			return false;
		});

		$('#class_in_session').on('click', function(){
			if($(this).hasClass('active')){
				return false;
			}

			reset_class_datatable();
			$('.class_panel').find('button').removeClass('active');
			$(this).addClass('active');
			get_classes(true);

			return false;
		});
	});
}(jQuery, COMMON) );