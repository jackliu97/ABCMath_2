( function($) {
	'use strict';

	$(document).ready(function() {

		$('#add_object_form').submit(function(){
			$.ajax({
				type:'POST',
				url:'/scaffolding/save_object_detail',
				data: $(this).serialize(),
				success: function(data){
					console.log(data);

					if(data.success){
						window.location = '/scaffolding/object_list/' + $('#entity_id').val();
					}else{
						alert(data.message);
					}
				}
			});
			return false;
		});

	});

}(jQuery) );