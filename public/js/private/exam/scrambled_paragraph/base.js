( function($) {
	'use strict';

	function build_q_html(q){
		var html = '';
		var answer = q['answer'];
		for(var i=0, l=answer.length; i<l; i++){
			var letter = num_to_alpha(i+1);
			var order = parseInt(answer[i]['order_id'], 10);
			html += '<li class="list-group-item line">';
			html += '<span class="line_choice" num="' + answer[i]['choice'] + '">';
			html += num_to_alpha(answer[i]['choice']);
			html += '</span>.&nbsp;';
			html += '<span class="line_text">' + answer[i]['line'] + '</span></li>';
		}

		return html;

	}

	function num_to_alpha(i, str){
		var ord = 64;
		if(str == 'Q'){
			var ord = 80;
		}
		return String.fromCharCode(ord + i);
	}

	function make_text(){
		var rows = $('#question_container').find('.row');
		var text = 'scramble bank \nSHORT ANSWER \n\n';

		$.each(rows, function(index, value){
			var val_obj = $(value);
			var lines = val_obj.find('.line');

			text += val_obj.find('.panel-heading').html() + '\n';
			$.each(val_obj.find('.line'), function(i, v){
				text += '____' + $(v).find('.line_choice').html() + '. ' + $(v).find('.line_text').html() + '\n';
			});

			text += val_obj.find('.ans').html() + '\n\n';

		});

		return text;

	}

	function swtich_letter_logic(obj){
		var row_obj = $(obj).closest('.row');
		var str = $(obj).html();
		var solution = row_obj.find('.int_ans').val().split(',');
		var solution_alpa = [];
		$.each(row_obj.find('.line_choice'), function(index, value){
			$(value).html(num_to_alpha(parseInt($(value).attr('num')), str));
		});

		$.each(solution, function(index, value){
			solution_alpa.push(num_to_alpha(parseInt(value), str));
		});
		row_obj.find('.ans').html('ANS: ' + solution_alpa.join(', '));
	}


	$( document ).ready(function() {


		$('#generate_questions').on('click', function(){

			var submit_data = {
				'num': $('#num_q').val()
			};

			$.ajax({
				type:'POST',
				url:'/create_exam/get_questions',
				data: submit_data,
				success: function(data){

					if(data.success){
						var html = '';
						$.each(data.questions, function(index, value){
							html += '<div class="row">';
							html += '<div class="col-md-1">';
							html += '<span class="label label-primary pull-right start_with">Q</span>';
							html += '<span class="label label-primary pull-right start_with">A</span>';
							html += '</div>';
							html += '<div class="col-md-8">';
							html += '<div class="panel panel-default"><div class="panel-heading">' + value['header'] + '</div>';
							html += '<ul class="list-group">';
							html += build_q_html(value);
							html += '<li class="list-group-item ans"> ANS: ' + value['solution_alpa'].join(', ') + '</li>';
							html += '<input class="int_ans" type="hidden" value="' + value['solution'].join(',') + '">';
							html += '</ul></div></div><div class="col-md-1">&nbsp;</div></div>';
						});

						$('#question_container').empty();
						$('#question_container').append(html);

						$('.start_with').on('click', function(){
							swtich_letter_logic(this);
						});

					}else{
						console.log(data.message);
					}
				}
			});


			return false;

		});

		$('.display_text').on('click', function(){

			if($(this).hasClass('btn-default')){
				$('#question_textarea').val(make_text());
				$(this).removeClass('btn-default');
				$(this).addClass('btn-info');
				$('#text_question_container').show();
				$('#question_container').hide();
			}else{
				$(this).removeClass('btn-info');
				$(this).addClass('btn-default');
				$('#text_question_container').hide();
				$('#question_container').show();
			}

		});

		$('.display_pdf').on('click', function(){
			window.location = '/create_exam/scrambled_paragraph_pdf/' + $(this).attr('examid');
		});

		$('.start_with').on('click', function(){
			swtich_letter_logic(this);
		});


	});

}(jQuery) );