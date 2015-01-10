( function($) {
	'use strict';
	
	function fnFormatDetails(word){

		console.log(word);
		var sOut = '<tr class="word_info"><td></td><td>';
		$.each(word.definitions, function(i, def){
			sOut += '<div class="well">';
			sOut += '<b>' + def['word'] + '</b>';
			sOut += '&nbsp;(' + def['parts_of_speech'] + ')&nbsp;';
			sOut += def['definition'];
			sOut += '</div>';

		});
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
		var oTable = $('#word_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"sAjaxSource": '/vocabulary/word_list_action',
			"fnDrawCallback": function( oSettings ) {
				$('#word_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#word_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#word_table_wrapper').find('#word_table_previous').addClass('btn btn-default');
				$('#word_table_wrapper').find('#word_table_next').addClass('btn btn-default');
			}
		});


		$('#word_table').on( 'click', '.more_info', function () {
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
				url:'/vocabulary/get_word_detail',
				data: {'word_id':$this.attr('word_id')},
				success: function(data){
					$tr.after(fnFormatDetails(data.word));
					$tr.find('button').prop('disabled',false);
				}
			});
		});

		$('#word_table').on( 'click', '.word_detail', function () {
			window.location = '/vocabulary/word_detail/' + $(this).attr('word_id');
		});

		$('#word_table').on( 'click', '.remove', function () {
			if(!confirm('Are you sure you want to delete this word?')){
				return false;
			}

			var $tr = $(this).closest('tr');
			$.ajax({
				type:'POST',
				url:'/vocabulary/delete_word',
				data: {'word_id':$(this).attr('word_id')},
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