( function($) {
	'use strict';

	function fnFormatDetails(full_text){

		var sOut = '<tr class="word_info"><td></td><td colspan="2">';
		sOut += '<div class="well">';
		sOut += '<b>Full Paragraph Text:&nbsp;</b>';
		sOut += full_text;
		sOut += '</div>';
		sOut += '</td></tr>';
		
		return sOut;
	}

	function toggle(obj){
		if(obj.hasClass('glyphicon-chevron-down')){
			obj.removeClass('glyphicon-chevron-down');
			obj.addClass('glyphicon-chevron-up');
		}else{
			obj.removeClass('glyphicon-chevron-up');
			obj.addClass('glyphicon-chevron-down');
		}
	}

	$(document).ready(function() {
		var oTable = $('#list_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"sAjaxSource": '/reading_comprehension/list_action',
			"fnDrawCallback": function( oSettings ) {
				$('#list_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#list_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#list_table_wrapper').find('#list_table_previous').addClass('btn btn-default');
				$('#list_table_wrapper').find('#list_table_next').addClass('btn btn-default');
			}
		});

		$('#list_table').on( 'click', '.edit_mode', function () {
			window.location = '/reading_comprehension/create/' + $(this).attr('paragraph_id');
		});

		$('#list_table').on( 'click', '.remove', function () {
			if(!confirm('Are you sure you want to delete this question?')){
				return false;
			}

			var $tr = $(this).closest('tr');
			$.ajax({
				type:'POST',
				url:'/reading_comprehension/delete',
				data: {'questionid':$(this).attr('paragraph_id')},
				success: function(data){
					if(data.success){
						$tr.remove();
					}else{
						alert(data.message);
					}
				}
			});
		});


		$('#list_table').on( 'click', '.more_info', function () {
			var $this = $(this);
			toggle($this);
			$this.prop('disabled',true);
			var $tr = $this.closest('tr');
			
			if($tr.next().hasClass('word_info')){

				if($tr.next().is( ":hidden" )){
					$tr.next().show();
				}else{
					$tr.next().hide();
				}
				$this.prop('disabled',false);
				return false;
			}

			$.ajax({
				type:'POST',
				url:'/reading_comprehension/get_passage_detail',
				data: {'paragraph_id':$this.attr('paragraph_id')},
				success: function(data){
					console.log(data);
					$tr.after(fnFormatDetails(data.full_text));
					$tr.find('button').prop('disabled',false);
				}
			});
		});

	});

}(jQuery) );