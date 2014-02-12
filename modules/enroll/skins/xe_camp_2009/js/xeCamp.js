(function($){
    $(function(){
        $('input[name=class]').change(function(){
           var t = $(this);
           if(t.val()=='expert') $('tr.classC').show();
           else  $('tr.classC').hide();
       });

        $('textarea').focus(function(){
                var t=$(this);
                if(t.val()== t.attr('title')) t.val('');
            }).blur(function(){
                var t=$(this);
                if(!t.val()) t.val(t.attr('title'));
            }).focus().blur();


    });
})(jQuery);
