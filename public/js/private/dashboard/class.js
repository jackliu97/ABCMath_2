( function($, $C) {
	'use strict';

	var attachment = false;


	function reset_detail($this){
		$('#detail_body').empty();

		$('.class_detail_tab').find('li').removeClass('active');
		$this.closest('li').addClass('active');

		//reset lesson links actions.
		$('.lessons_list').find('a')
			.removeClass('show_attendance show_lessons show_grades')
			.addClass($this.attr('action'));
	}

	function build_class_navigation(){
		$.ajax({
			type:'POST',
			url:'/class_dashboard/build_class_navigation',
			data: {
				'class_id': $('#class_id').val()
			},
			success: function(data){
				if(data.success){
					$('#class_navigation').html(data.html);
				}else{
					$C.error(data.message);
				}
			}
		});
	}

	function show_attendance(){
		$.ajax({
			type:'POST',
			url:'/class_dashboard/show_attendance',
			data: {
				'class_id': $('#class_id').val(),
				'lesson_id': $('.lessons_list').find('.active').attr('lesson_id')
			},
			success: function(data){
				if(data.success){
					$('#detail_body').html(data.html);
				}else{
					$C.error(data.message);
				}
			}
		});
	}

	function show_assignments(){
		$.ajax({
			type:'POST',
			url:'/class_dashboard/show_assignments',
			data: {
				'class_id': $('#class_id').val(),
				'lesson_id': $('.lessons_list').find('.active').attr('lesson_id')
			},
			success: function(data){
				if(data.success){
					$('#detail_body').html(data.html);
				}else{
					$C.error(data.message);
				}
			}
		});
	}

	function show_attachments(){
		$.ajax({
			type:'POST',
			url:'/class_dashboard/show_attachments',
			data: {
				'class_id': $('#class_id').val(),
				'lesson_id': $('.lessons_list').find('.active').attr('lesson_id')
			},
			success: function(data){
				if(data.success){
					$('#detail_body').html(data.html);
				}else{
					$C.error(data.message);
				}
			}
		});
	}

	function populate_assignment_data(id){
		$.ajax({
			type:'POST',
			url:'/class_dashboard/get_assignment_info',
			data: {
				'assignment_id': id
			},
			success: function(data){
				if(data.success){
					console.log(data);
					$('#assignment_id').val(data.info.id);
					$('#assignment_name').val(data.info.name);
					$('#assignment_description').val(data.info.description);
					$('#assignment_type_id').val(data.info.assignment_type_id);
					$('#assignment_weight').val(data.info.weight);
					$('#maximum_score').val(data.info.maximum_score);
					$('#apply_to_all').attr('checked', false);
					$('.apply_to_all').hide();
				}else{
					$C.error(data.message);
				}
			}
		});
	}

	function clear_assignment_data(){
		$('.apply_to_all').show();
		$('#assignment_name').val('');
		$('#assignment_description').val('');
		$('#assignment_type_id').val('');
		$('#assignment_id').val('');
		$('#assignment_weight').val('');
		$('#maximum_score').val('');
		$('#apply_to_all').attr('checked', false);
	}

	function reset_student_datatable(){
		$('.datatable_student_container').empty();
		$('.datatable_student_container').html(
			'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-striped" id="students_table">' +
			'<thead><tr>' + 
			'<th></th>' + 
			'<th>External ID</th>' + 
			'<th>Name</th>' + 
			'<th>Email</th>' + 
			'<th>Home Phone</th>' + 
			'<th>Cell Phone</th>' + 
			'</tr></thead><tbody></tbody></table>');
	}

	function get_students(class_id, action){

		if(action === 'add'){
			var pagination = true;
			var source = '/class_dashboard/get_students_for_add/' + class_id;
		}else{
			var pagination = false;
			var source = '/class_dashboard/get_students_for_remove/' + class_id;
		}

		$('#students_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": false,
			"bPaginate":pagination,
			"sAjaxSource": source,
			"stateSave": true,
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

	function attendence_button($container, mode){
		var $here_button = $container.find('.here_button');
		var $tardy_button = $container.find('.tardy_button');
		var $absent_button = $container.find('.absent_button');

		if(mode === 'here'){
			$here_button.removeClass('btn-default').addClass('btn-success');
			$tardy_button.removeClass('btn-warning').addClass('btn-default');
			$absent_button.removeClass('btn-danger').addClass('btn-default');
		}

		if(mode === 'tardy'){
			$here_button.removeClass('btn-default').addClass('btn-success');
			$tardy_button.removeClass('btn-default').addClass('btn-warning');
			$absent_button.removeClass('btn-danger').addClass('btn-default');
		}

		if(mode === 'absent'){
			$here_button.removeClass('btn-success').addClass('btn-default');
			$tardy_button.removeClass('btn-warning').addClass('btn-default');
			$absent_button.removeClass('btn-default').addClass('btn-danger');
		}

	}

	$( document ).ready(function() {

		var class_id = $('#class_id').val();
		var $body = $('body');

		$body.tooltip({
			selector: '[data-toggle="tooltip"]'
			});

		//build left nav bar.
		build_class_navigation();

		//default to attendance being selected.
		reset_detail($('.attendance_tab'));
		show_attendance();

		//show student datatable.
		reset_student_datatable();
		get_students(class_id);
		$('#registered_students').addClass('active');

		
		/*
		* Show registered students
		*/
		$body.on('click', '#registered_students', function(){
			if($(this).hasClass('active')){
				return false;
			}

			$('.student_panel').find('.btn-default').removeClass('active');
			$(this).addClass('active');
			reset_student_datatable();
			get_students(class_id);
		});

		/*
		* Show all students (this is when we want to add a new student)
		*/
		$body.on('click', '#add_students', function(){
			if($(this).hasClass('active')){
				return false;
			}
			
			$('.student_panel').find('.btn-default').removeClass('active');
			$(this).addClass('active');
			reset_student_datatable();
			get_students(class_id, 'add');
		});


		//unregister a student from class
		$body.on('click', '.unregister', function(){

			if(!confirm('Are you sure you want to remove this student from class?')){
				return false;
			}

			var class_id = $(this).attr('class_id');
			var student_id = $(this).attr('student_id');

			$.ajax({
				type:'POST',
				url:'/class_dashboard/unregister',
				data: {
					'student_id': student_id,
					'class_id': class_id
				},
				success: function(data){
					reset_student_datatable();
					get_students(class_id);
				}
			});
		});

		//unregister a student from class
		$body.on('click', '.register', function(){
			if(!confirm('Are you sure you want to register this student for class?')){
				return false;
			}

			var class_id = $(this).attr('class_id');
			var student_id = $(this).attr('student_id');

			$.ajax({
				type:'POST',
				url:'/class_dashboard/register',
				data: {
					'student_id': student_id,
					'class_id': class_id
				},
				success: function(data){
					reset_student_datatable();
					get_students(class_id, 'add');
				}
			});
		});


		$body.on('click', '.grade_tab', function(){
			window.open('/grade_dashboard/grade/' + $(this).attr('class_id'));
		});

		$body.on('keyup', 'input.filter_class', function(){
			var pattern = $(this).val();
			$(".class_list").show();
			$(".class_list").find(':not(:contains( ' + pattern + '))').each(function(){
				$(this).parent().hide();
			});

		});

		$('.class_dropdown').on('change', function(){
			window.location.href='/class_dashboard/info/' + $(this).val();
			return false;
		});

		$body.on('click', '.mark_all_present', function(){
			$('.mark_present').each(function(){
				$(this).click();
			});

			return false;
		});

		$body.on('click', '.mark_present', function(){
			var $this = $(this);
			var student_id = $this.attr('student_id');
			var lesson_id = $('#lesson_id').val();

			$this.prop('disabled',true);
			$.ajax({
				type:'POST',
				url:'/class_dashboard/take_attendance',
				data: {
					'student_id': student_id,
					'lesson_id': lesson_id
				},
				success: function(data){
					console.log(data);
					var $container = $this.closest('.row');
					

					if(data.success){
						attendence_button($container, 'here');
						$this.prop('disabled',false);
					}else{
						$C.error(data.message);
					}
				}
			});

		});

		$body.on('click', '.mark_tardy', function(){
			var $this = $(this);
			var student_id = $this.attr('student_id');
			var lesson_id = $('#lesson_id').val();

			$this.prop('disabled',true);
			$.ajax({
				type:'POST',
				url:'/class_dashboard/mark_tardy',
				data: {
					'student_id': student_id,
					'lesson_id': lesson_id
				},
				success: function(data){
					console.log(data);
					var $container = $this.closest('.row');
					
					if(data.success){
						attendence_button($container, 'tardy');
						$this.prop('disabled',false);
					}else{
						$C.error(data.message);
					}
				}
			});

		});

		$body.on('click', '.mark_absent', function(){
			var $this = $(this);
			var student_id = $this.attr('student_id');
			var lesson_id = $('#lesson_id').val();

			$this.prop('disabled',true);
			$.ajax({
				type:'POST',
				url:'/class_dashboard/mark_absent',
				data: {
					'student_id': student_id,
					'lesson_id': lesson_id
				},
				success: function(data){
					console.log(data);
					var $container = $this.closest('.row');
					
					if(data.success){
						attendence_button($container, 'absent');
						$this.prop('disabled',false);
					}else{
						$C.error(data.message);
					}
				}
			});
		});

		$body.on('click', '.attendance_tab', function(){
			reset_detail($(this));
			show_attendance();
		});

		$body.on('click', '.print_attendance_tab', function(){
			var class_id = $(this).attr('class_id');
			window.open('/class_dashboard/print_attendance_view/' + class_id);
			return false;
		});

		$body.on('click', '.assignment_tab', function(){
			reset_detail($(this));
			show_assignments();
		});

		$body.on('click', '.attachment_tab', function(){
			reset_detail($(this));
			show_attachments();
		});


		$body.on('click', '.lesson_sidebar', function(){

			$('.lessons_list').find('a').removeClass('active');
		 	$(this).addClass('active');

			//if show attendance button is pushed, we show attendance.
			if($('.attendance_tab').parent().hasClass('active')){
				show_attendance();
			}else if($('.assignment_tab').parent().hasClass('active')){
				show_assignments();
			}else if($('.attachment_tab').parent().hasClass('active')){
				show_attachments();
			}

		});

		$body.on('click', '.edit_assignment, .add_assignment', function(){

			var assignment_id = $(this).attr('assignment_id');
			if(assignment_id){
				populate_assignment_data(assignment_id);
			}else{
				clear_assignment_data();
			}

			$('#assignment_modal').modal('show');
		});

		$body.on('click', '.edit_attachment, .add_attachment', function(){

			$('#attachment_modal').find('.attachment-form-group').html(
				'<label for="attachment_file">File input</label>' + 
				'<input type="file" id="attachment_file" name="attachment_file">');


			$('#attachment_file').on('change', function(){
				attachment = event.target.files;
			});

			$('#attachment_description').val('');
			$('#attachment_modal').modal('show');
		});

		$body.on('click', '.delete_attachment', function(){

			if(!confirm('Are you sure you want to delete this attachment?')){
				return false;
			}

			var attachment_id = $(this).attr('attachment_id');
			$.ajax({
				type:'POST',
				url:'/class_dashboard/delete_attachment',
				data: {
					'attachment_id': $(this).attr('attachment_id')
				},
				success: function(data){
					if(data.success){
						show_attachments();
					}else{
						$C.error(data.message, $('#attachment_error'));
						return false;
					}
				}
			});
		});

		$body.on('click', '.open_attachment', function(){
			var attachment_id = $(this).attr('attachment_id');

			

		});

		$body.on('click', '.delete_assignment', function(){

			if(!confirm('Are you sure you want to delete this assignment?')){
				return false;
			}

			var assignment_id = $(this).attr('assignment_id');
			$.ajax({
				type:'POST',
				url:'/class_dashboard/delete_assignment',
				data: {
					'assignment_id': $(this).attr('assignment_id')
				},
				success: function(data){
					if(data.success){
						show_assignments();
					}else{
						$C.error(data.message, $('#assignment_error'));
						return false;
					}
				}
			});
		});


		$('#attachment_form').on('submit', function(event){
			event.stopPropagation();
			event.preventDefault();
			
			var data = new FormData();
			$.each(attachment, function(key, value){
				data.append(key, value);
			});

			data.append('attachment_description', $('#attachment_description').val());
			data.append('lesson_id', $('#lesson_id').val());

			$.ajax({
				type:'POST',
				url:'/class_dashboard/upload_attachment',
				cache: false,
				processData: false, 
				contentType: false,
				dataType: 'json',
				data: data,
				success: function(data){
					if(data.success){
						show_attachments();
						$('#attachment_modal').modal('hide');
					}else{
						$C.error(data.message, $('#attachment_error'));
						return false;
					}
				}
			});
			return false;

		});

		$('#assignment_form').on('submit', function(){
			$.ajax({
				type:'POST',
				url:'/class_dashboard/save_assignment',
				data: {
					'id': $('#assignment_id').val(),
					'name': $('#assignment_name').val(),
					'description': $('#assignment_description').val(),
					'assignment_type_id': $('#assignment_type_id').val(),
					'maximum_score': $('#maximum_score').val(),
					'weight': $('#assignment_weight').val(),
					'lesson_id': $('#lesson_id').val(),
					'apply_to_all': $('#apply_to_all').is(':checked') ? '1' : '0'
				},
				success: function(data){
					if(data.success){
						show_assignments();
						$('#assignment_modal').modal('hide');
					}else{
						$C.error(data.message, $('#assignment_error'));
						return false;
					}
				}
			});
			return false;

		});

	});
}(jQuery, COMMON) );