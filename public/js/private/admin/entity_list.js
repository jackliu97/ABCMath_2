( function($) {
	'use strict';

	$(document).ready(function() {
		$('#entity_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"paging":false,
			"sAjaxSource": '/admin/get_all_entities',
			"fnDrawCallback": function( oSettings ) {
				$('#entity_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#entity_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#entity_table_wrapper').find('#entity_table_previous').addClass('btn btn-default');
				$('#entity_table_wrapper').find('#entity_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
	    "sWrapper": "dataTables_wrapper form-inline"
		} );

		$('.add_new_entity').on( 'click', function () {
			window.location = '/admin/entity_detail';
		});

		$('#entity_table').on( 'click', '.list_mode', function () {
			window.location = '/admin/entity_fields/' + $(this).attr('entity_id');
		});

		$('#entity_table').on( 'click', '.edit_mode', function () {
			window.location = '/admin/entity_detail/' + $(this).attr('entity_id');
		});

		$('#entity_table').on( 'click', '.remove', function () {
			if(!confirm('Are you sure you want to delete this entity?')){
				return false;
			}

			var $tr = $(this).closest('tr');
			$.ajax({
				type:'POST',
				url:'/admin/delete_entity',
				data: {'entity_id':$(this).attr('entity_id')},
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