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

function validEnrollItemModify() {
/*
	var  ckFile = checkFile();
	if(!ckFile) return;

var response_tags = ['validation'];
var tf = false;
var params = {
        module_srl : jQuery('input[name=module_srl]').val(),
        email_address: jQuery('input[name=email_address]').val(),
        password: jQuery('input[name=password]').val()
		};
		exec_xml('enroll', 'validEnrollItem', params,
			function(data){
				if(!data['validation']) {
					alert('비밀번호가 틀립니다');  return false;
				} else {
					jQuery('form#container').submit();
					return true;
				}
			}, response_tags );
*/
	jQuery('form#container').submit();
}

function validEnrollItemInsert() {
	if(jQuery('input[name=provision]:checked').val() != 'Y')
	{
		alert('약관에 동의하셔야 합니다.');
		return;
	}
	//var ckFile = checkFile();
	//if(!ckFile) return;
	jQuery('form#container').submit();


}
function checkFile() {
//파일 체크
	if(jQuery('input[name=file]').val()) {
		var filename= jQuery('input[name=file]').val();
		var str_loc = filename.lastIndexOf(".");
		var file_ext = filename.substring(str_loc+1); 
		file_ext = file_ext.toLowerCase();
		if(file_ext != 'png' && file_ext != 'jpg' && file_ext != 'jpeg' && file_ext != 'gif') {
			alert('파일 확장자가 올바르지 않습니다.');
			return false;
		}
	}
	return true;
}

