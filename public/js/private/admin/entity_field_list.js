( function($) {
	'use strict';

	$(document).ready(function() {
		$('#entity_fields_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"paging":false,
			"sAjaxSource": '/admin/get_all_entity_fields/' + $('#entity_id').val(),
			"fnDrawCallback": function( oSettings ) {
				$('#entity_fields_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#entity_fields_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#entity_fields_table_wrapper').find('#entity_fields_table_previous').addClass('btn btn-default');
				$('#entity_fields_table_wrapper').find('#entity_fields_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
	    "sWrapper": "dataTables_wrapper form-inline"
		} );

		//add_new_field
		$('.add_new_field').on( 'click', function () {
			window.location = '/admin/entity_field_detail/' + 
								$(this).attr('entity_id');
		});

		$('#entity_fields_table').on( 'click', '.edit_mode', function () {
			window.location = '/admin/entity_field_detail/' + 
								$(this).attr('entity_id') + '/' + 
								$(this).attr('field_id');
		});

		$('#entity_fields_table').on( 'click', '.remove', function () {
			if(!confirm('Are you sure you want to delete this field?')){
				return false;
			}

			var $tr = $(this).closest('tr');
			$.ajax({
				type:'POST',
				url:'/admin/delete_entity_field',
				data: {'field_id':$(this).attr('field_id')},
				success: function(data){
					console.log(data);
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