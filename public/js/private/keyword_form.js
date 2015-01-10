$( document ).ready(function() {


	$('.keyword').on('click', function(){

		var html = '<span class="label label-default selected_keyword" keywordid="' + 
					$(this).attr('keywordid')
					+ '">' + 
					$(this).text()
					+ '</span>&nbsp;';

		$('#keyword_container').append(html);
		$('.selected_keyword').on('click', function(){
			$(this).remove();
		});

	});

	$('#keyword_form').submit(function(){

		var html = '<span class="label label-default selected_keyword" keywordid="">' + 
					$('#keyword_input').val()
					+ '</span>&nbsp;';

		$('#keyword_container').append(html);
		$('#keyword_input').val('');
		$('.selected_keyword').on('click', function(){
			$(this).remove();
		});

		return false;
	});

	$('.selected_keyword').on('click', function(){
		$(this).remove();
	});

});