$( document ).ready(function() {

	$('.display_pdf').on('click', function(){
		window.location = '/create_exam/reading_comprehension_pdf/' + $(this).attr('examid');
	});


});