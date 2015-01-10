( function($) {
	'use strict';

	$( document ).ready(function(){

		$('#new_user_form').submit(function(){


			var submit_data = {
				'email': $('#email').val(),
				'password': $('#password').val(),
				'password_re': $('#password_re').val()
			};

			$.each($('.permission:checked'), function(){
				submit_data['permission'].push($(this).val());
			});

			$.ajax({
				type:'POST',
				url:'/user/save',
				data: submit_data,
				success: function(data){

					if(data.success){
						window.location = '/user/all_user';
					}else{
						alert(data.message);
					}
				}
			});

			return false;
		});

		$('#new_group_form, #update_group_form').submit(function(){


			var submit_data = {
				'name': $('#group_name').val(),
				'group_id': $('#group_id').val(),
				'permission': []
			};

			$.each($('.permission'), function(){
				var this_permission = {};
				this_permission.name = $(this).attr('id');
				if($(this).is(':checked')){
					this_permission.permission = 1;
					submit_data['permission'].push(this_permission);
				}else{
					this_permission.permission = 0;
					submit_data['permission'].push(this_permission);
				}
			});

			console.log(submit_data);

			$.ajax({
				type:'POST',
				url:'/user/save_group',
				data: submit_data,
				success: function(data){

					if(data.success){
						window.location = '/user/all_groups';
					}else{
						alert(data.message);
					}
				}
			});

			return false;
		});

		$('#save_permission').submit(function(){
			var submit_data = {
				'permission': [],
				'user_id':$('#user_id').val()
			};

			$.each($('.permission:checked'), function(){
				submit_data['permission'].push($(this).val());
			});

			console.log(submit_data);
			$.ajax({
				type:'POST',
				url:'/user/save_user_permissions',
				data: submit_data,
				success: function(data){

					if(data.success){
						window.location = '/user/all_user';
					}else{
						alert(data.message);
					}
					
				}
			});

			return false;
		});

		$('.remove_group').on('click', function(){
			var submit_data = {
				'group_id': $(this).attr('groupid')
			};

			if(!confirm('Are you sure you want to delete this group?')){
				return false;
			}

			$.ajax({
				type:'POST',
				url:'/user/delete_group',
				data: submit_data,
				success: function(data){

					if(data.success){
						console.log(data);
						window.location = '/user/all_groups/';
					}else{
						alert(data.message);
					}
					
				}
			});

			return false;
		});

		$('.remove_user').on('click', function(){
			var submit_data = {
				'user_id': $(this).attr('userid')
			};

			if(!confirm('Are you sure you want to delete this user?')){
				return false;
			}

			$.ajax({
				type:'POST',
				url:'/user/delete_user',
				data: submit_data,
				success: function(data){

					if(data.success){
						console.log(data);
						window.location = '/user/all_user/';
					}else{
						alert(data.message);
					}
					
				}
			});

			return false;
		});


		$('.add_new').on('click', function(){
			window.location = '/user/create_user/';
			return false;
		});

		$('.cancel_user').on('click', function(){
			window.location = '/user/all_user/';
			return false;
		});

		$('.cancel_group').on('click', function(){
			window.location = '/user/all_groups/';
			return false;
		});

		$('.edit_group').on('click', function(){
			window.location = '/user/edit_group/' + $(this).attr('groupid');
			return false;
		});


		$('.edit_user_group').on('click', function(){
			window.location = '/user/edit_user_group/' + $(this).attr('userid');
			return false;
		});


	});
}(jQuery) );