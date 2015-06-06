( function($, $C) {
    'use strict';

    $( document ).ready(function(){
        $('.show_question').on('click', function(){
            var $this = $(this);
            $this.parent().find('.show_question').each(function(){
                $(this).removeClass('active');
            })
            $this.addClass('active');

            $.ajax({
                type:'POST',
                url:'/scrambled_paragraph/show_question',
                data:   {
                        'question_id':$this.attr('data-id')
                        },
                success: function(data){
                    if(data.success){
                        $('.question_panel').html(data.html);
                    }else{
                        $C.error(data.message);
                    }
                }
            });

        });
    });
}(jQuery, COMMON) );