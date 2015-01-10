( function($) {
	'use strict';

	function fnFormatDetails(article)
	{
		//var aData = oTable.fnGetData( nTr );
		var sOut = '<tr class="full_passage"><td>&nbsp;</td><td colspan="2"><div class="well">';
		sOut += article;
		sOut += '</div></td></tr>';
		
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
		$('#passage_table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bLengthChange": true,
			"sAjaxSource": '/passage/get_all_passages',
			"fnDrawCallback": function( oSettings ) {
				$('#passage_table_wrapper').find('input').addClass('form-control dataTables_form_override');
				$('#passage_table_wrapper').find('select').addClass('form-control dataTables_form_override');
				$('#passage_table_wrapper').find('#passage_table_previous').addClass('btn btn-default');
				$('#passage_table_wrapper').find('#passage_table_next').addClass('btn btn-default');
			}
		});

		$.extend( $.fn.dataTableExt.oStdClasses, {
			"sWrapper": "dataTables_wrapper form-inline"
		} );

		$('#passage_table').on( 'click', '.more_info', function () {
			var $this = $(this);
			toggle($this);
			$this.prop('disabled',true);
			var $tr = $this.closest('tr');
			
			if($tr.next().hasClass('full_passage')){

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
				url:'/passage/get_passage',
				data: {'article_id':$this.attr('article_id')},
				success: function(data){
					$tr.after(fnFormatDetails(data.article));
					$tr.find('button').prop('disabled',false);
				}
			});
		});

		$('#passage_table').on( 'click', '.article_detail', function () {
			window.location = '/passage/passage_detail/' + $(this).attr('article_id');
		});

		$('#passage_table').on( 'click', '.remove', function () {
			if(!confirm('Are you sure you want to delete this passage?')){
				return false;
			}

			var $tr = $(this).closest('tr');
			$.ajax({
				type:'POST',
				url:'/passage/delete_passage',
				data: {'article_id':$(this).attr('article_id')},
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