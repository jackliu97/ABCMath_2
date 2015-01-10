( function($) {
	'use strict';

	function filter_list(){
		var selected_id = [];

		$.each($('#keyword_container').find('.selected_keyword'), function(){
			selected_id.push($(this).attr('keywordid'));
		});

		if(selected_id.length == 0){
			show_all();
			return true;
		}

		$.each($('.question').find('.keyword_list'), function(index, value){
			var show = true;
			var question_keywords = [];
			$.each($(value).find('.selected_keyword'), function(){
				question_keywords.push($(this).attr('keywordid'));
			});

			$.each(selected_id, function(i, v){
				if(show == true){
					show = in_array(v, question_keywords);
				}
			});

			if(show){
				$(this).closest('.question').show();
			}else{
				$(this).closest('.question').hide();
			}

		});

	}

	function in_array(needle, haystack){
		return ($.inArray(needle, haystack) != -1);
	}

	function show_all(){
		$.each($('.question'), function(){
			$(this).show();
		});
	}

	function hide_all(){
		$.each($('.question'), function(){
			$(this).hide();
		});
	}

	$( document ).ready(function() {

		$('.keyword').on('click', function(){
			var keywordid = $(this).attr('keywordid');
			var html = '<span class="label label-default selected_keyword" keywordid="' + 
						keywordid
						+ '">' + 
						$(this).text()
						+ '</span>&nbsp;';

			$('#keyword_container').append(html);
			$('.selected_keyword').on('click', function(){
				$(this).remove();
				filter_list();
			});

			filter_list();

		});

	});
}(jQuery) );