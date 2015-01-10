( function($) {
	'use strict';

	function nl2br (str, is_xhtml) {
	    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
	    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	}

	$( document ).ready(function(){

		$('#parse').on('click', function(){
			var submit_data = {'passage': $('#passage_text').val(),
								'keyword':[]};
			$.each($('.selected_keyword'), function(index, value){
				submit_data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});

			$.ajax({
				type:'POST',
				url:'/passage/parse_passage',
				data: submit_data,
				success: function(data){

					if(data.success){
						$('#group_ids').val(data.group_ids);
						$('#vocabulary_ids').val(data.vocabulary_ids);
						$('#parse_result').empty();
						$('#parse_result').append(nl2br(data.passage));

					}else{
						alert(data.message);
					}
				}
			});
			return false;
		});

		$('#add_passage_form').submit(function(){
			var submit_data = {	'title': $('#passage_title').val(), 
								'article':$('#passage_text').val(),
								'keyword':[]};

			$.each($('.selected_keyword'), function(index, value){
				submit_data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});

			$.ajax({
				type:'POST',
				url:'/passage/save_passage',
				data: submit_data,
				success: function(data){

					if(data.success){

						window.location = '/passage/list_passage';

					}else{
						alert(data.message);
					}
				}
			});
			return false;
		});
	});
}(jQuery) );