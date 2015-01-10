( function($) {
	'use strict';

	$(document).ready(function() {
		$('#object_list_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"paging":false,
			"sAjaxSource": '/scaffolding/list_all',
			"fnDrawCallback": function( oSettings ) {
				$('#object_list_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#object_list_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#object_list_table_wrapper').find('#entity_table_previous').addClass('btn btn-default');
				$('#object_list_table_wrapper').find('#entity_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
	    "sWrapper": "dataTables_wrapper form-inline"
		} );

		$('#object_list_table').on( 'click', '.list_mode', function () {
			window.location = '/scaffolding/object_list/' + $(this).attr('entity_id');
		});

		$('#object_list_table').on( 'click', '.add_mode', function () {
			window.location = '/scaffolding/object_detail/' + $(this).attr('entity_id');
		});

	});

}(jQuery) );