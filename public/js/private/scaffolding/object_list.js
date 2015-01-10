( function($) {
	'use strict';

	$(document).ready(function() {
		$('#object_list_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"sAjaxSource": '/scaffolding/get_all_objects/' + $('#entity_id').val(),
			"fnDrawCallback": function( oSettings ) {
				$('#object_list_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#object_list_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#object_list_table_wrapper').find('#object_list_table_wrapper_table_previous').addClass('btn btn-default');
				$('#object_list_table_wrapper').find('#object_list_table_wrapper_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
	    "sWrapper": "dataTables_wrapper form-inline"
		} );

		$('.add_new_object').on( 'click', function () {
			window.location = '/scaffolding/object_detail/' + $('#entity_id').val();
		});

		$('#object_list_table').on( 'click', '.edit_mode', function () {
			window.location = '/scaffolding/object_detail/' + 
								$('#entity_id').val() + '/' +
								$(this).attr('object_id');
		});

		$('#object_list_table').on( 'click', '.remove', function () {
			if(!confirm('Are you sure you want to delete this object?')){
				return false;
			}

			var $tr = $(this).closest('tr');
			$.ajax({
				type:'POST',
				url:'/scaffolding/delete_object',
				data: {
					'entity_id':$('#entity_id').val(),
					'object_id':$(this).attr('object_id')
				},
				success: function(data){
					if(data.success){
						$tr.remove();
					}else{
						alert(data.message);
					}
				}
			});
		});

	});

}(jQuery) );