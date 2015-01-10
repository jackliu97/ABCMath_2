( function($) {
	'use strict';

	$(document).ready(function() {

		$('#add_entity_field_form').submit(function(){
			$.ajax({
				type:'POST',
				url:'/admin/save_entity_field',
				data: $(this).serialize(),
				success: function(data){

					if(data.success){
						window.location = '/admin/entity_fields/' + $('#entity_table_id').val();
					}else{
						alert(data.message);
					}
				}
			});
			return false;
		});

	});

}(jQuery) );