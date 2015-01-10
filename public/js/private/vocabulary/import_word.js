( function($) {
	'use strict';

	$( document ).ready(function(){

		$('#import_word_form').submit(function(){

			var submit_data = {'group':[]};

			$.each($('.selected_group'), function(index, value){
				submit_data['group'].push(
										{
											'id':$(value).attr('groupid'),
											'name':$(value).text()
										});
			});

			$.ajax({
				type:'POST',
				url:'/vocabulary/save_import_word',
				data: submit_data,
				success: function(data){
					if(data.success){
						alert(data.message);
					}else{
						alert(data.message);
					}
				}
			});
			return false;
		});

		$('.group').on('click', function(){

			var html = '<span class="label label-default selected_group" groupid="' + 
						$(this).attr('groupid')
						+ '">' + 
						$(this).text()
						+ '</span>&nbsp;';

			$('#group_container').append(html);
			$('.selected_group').on('click', function(){
				$(this).remove();
			});

		});

		$('#group_form').submit(function(){

			var html = '<span class="label label-default selected_group" groupid="">' + 
						$('#group_input').val()
						+ '</span>&nbsp;';

			$('#group_container').append(html);
			$('#group_input').val('');
			$('.selected_group').on('click', function(){
				$(this).remove();
			});

			return false;
		});

		$('.selected_group').on('click', function(){
			$(this).remove();
		});


	});
}(jQuery) );