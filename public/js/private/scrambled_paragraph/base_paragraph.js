( function($) {
	'use strict';

	$( document ).ready(function() {

		$('#parse').on('click', function(){

			$.ajax({
				type:'POST',
				url:'/scrambled_paragraph/parse',
				data: {
					'type': 'paragraph',
					'paragraph': $('#paragraph').val()
				},
				success: function(data){

					if(data.success){

						var $container = $('#split_result');
						var $ul = $('<ol>');

						for(var i=0, l=data.pieces.length; i<l; i++){
							$ul.append($('<li>', {
								'text':data.pieces[i]
							}));
						}

						$container.empty();
						$container.append($ul);

					}else{
						alert(data.message);
					}
				}
			});

		});


		$('#scrambled_paragraph_form').submit(function(){

			var submit_data = {
				'type': 'paragraph',
				'paragraph': $('#paragraph').val(),
				'scrambled_paragraph_id': $('#scrambled_paragraph_id').val()
			};

			submit_data['keyword'] = [];
			$.each($('.selected_keyword'), function(index, value){
				submit_data['keyword'].push(
										{
											'id':$(value).attr('keywordid'),
											'word':$(value).text()
										});
			});

			$.ajax({
				type:'POST',
				url:'/scrambled_paragraph/save',
				data: submit_data,
				success: function(data){

					if(data.success){
						window.location = "/scrambled_paragraph/list_all";
					}else{
						alert(data.message);
					}
				}
			});


			return false;
		});
	});
}(jQuery) );