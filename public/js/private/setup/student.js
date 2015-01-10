( function($, $C) {
	'use strict';
	var attachment = false;

	function alert_message($container, message, success){
		var alert_type = 'success';
		if(success !== true){
			alert_type = 'danger';
		}

		if(message instanceof Array){
			message = message.join('<br/>');
		}

		$container.html('<div class="alert alert-' + alert_type + ' alert-dismissible" role="alert">' + 
			'<button type="button" class="close" data-dismiss="alert" aria-label="Close">' + 
			'<span aria-hidden="true">&times;</span></button>' + 
			message + '</div>');
	}

	$( document ).ready(function() {
		$('#student_upload_form').find('.student-upload-form-group').html(
			'<label for="student_upload_file">File input</label>' + 
			'<input type="file" id="student_upload_file" name="student_upload_file">');


		$('#student_upload_file').on('change', function(){
			attachment = event.target.files;
		});

		$('.sample_student_csv').on('click', function(){
			window.open('setup/sample_student_csv');
		});

		$('#student_upload_form').on('submit', function(){
			event.stopPropagation();
			event.preventDefault();
			
			var data = new FormData();
			$.each(attachment, function(key, value){
				data.append(key, value);
			});

			$.ajax({
				type:'POST',
				url:'/setup/import_student_action',
				cache: false,
				processData: false, 
				contentType: false,
				dataType: 'json',
				data: data,
				success: function(data){
					console.log(data);
					alert_message($('#alert_container'), data.message, data.success);
				}
			});
			return false;
		});


	});
}(jQuery, COMMON) );