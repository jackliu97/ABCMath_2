var COMMON = (function ( $ ) {
	var self = {};

	self.error = function (msg, $target){
			
		if(!$target){
			$target = $('#main_error');
		}

		$target.html('<div class="alert alert-dismissible alert-danger" role="alert">' + 
			'<button type="button" class="close" data-dismiss="alert">' + 
			'<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' + 
				msg + '</div>');
	};

	self.success = function (msg, $target){
			
		if(!$target){
			$target = $('#main_error');
		}

		$target.html('<div class="alert alert-dismissible alert-success" role="alert">' + 
			'<button type="button" class="close" data-dismiss="alert">' + 
			'<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' + 
				msg + '</div>');
	};

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

	$( document ).ready(function() {

		get_names();

		$('#student_name_form').submit(function(){
			var student_id = $('#student_name').val();
			$.ajax({
				type:'POST',
				url:'/student_dashboard/check_student',
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
		});

		$('#semester_id').on('change', function(){
			var semester_id = $('#semester_id').val();
			$.ajax({
				type:'POST',
				url:'/common/set_semester_id',
				data: {'semester_id': semester_id},
				success: function(data){
					location.reload();
				}
			});
			return false;
		});

	});


	return self;

}( jQuery ));