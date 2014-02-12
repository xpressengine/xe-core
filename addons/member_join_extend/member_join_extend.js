(function($){
    $(function() {
		// agree
		$('input.member_join_agree').click(function(){
			if($('.member_join_extend :checkbox').length == $('.member_join_extend :checked').length){
				exec_xml('memberjoinextend','MemberJoinExtendAgree',new Array(), function(){ location.reload()});
			}else{
				alert(msg_check_agree);
			}
		});

		// junior
		$('.junior_join').click(function(){ if(msg_junior_join) alert(msg_junior_join); });
    });
})(jQuery);
