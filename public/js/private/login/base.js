( function($) {
	'use strict';

	$( document ).ready(function(){

		$('#username').focus();

		$('#login_form').submit(function(){
			var submit_data = {
				'email': $('#email').val(),
				'password': $('#password').val(),
				'redirect': $('#redirect').val(),
				'rememeber': ($('#rememeber').is(':checked') ? 1 : 0)
			};

			$.ajax({
				type:'POST',
				url:'/login/check_login',
				data: submit_data,
				success: function(data){
					console.log(data);
					if(data.success){
						window.location = data.redirect;
					}else{
						alert(data.message);
					}
				}
			});

			return false;
		});


	});
}(jQuery) );