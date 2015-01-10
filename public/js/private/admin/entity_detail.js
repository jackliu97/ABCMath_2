( function($) {
	'use strict';

	$(document).ready(function() {

		$('#add_entity_form').submit(function(){
			var submit_data = {	'id': $('#table_id').val(), 
								'table_name':$('#table_name').val(),
								'display_name':$('#display_name').val(),
								'before_hook':$('#before_hook').val(),
								'after_hook':$('#after_hook').val()
								};

			$.ajax({
				type:'POST',
				url:'/admin/save_entity',
				data: submit_data,
				success: function(data){

					if(data.success){

						window.location = '/admin';

					}else{
						alert(data.message);
					}
				}
			});
			return false;
		});

	});

}(jQuery) );