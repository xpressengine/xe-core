function loadCommentForm(document_srl)
{
    var loadComment = true,
        commentForm = jQuery('form.wikiEditor');

    if (commentForm.length)
    {
        loadComment = confirm('Would you like to cancel editing your comment?');
    }

    if (loadComment)
    {
        if (commentForm.length)
        {
            commentForm.siblings().show();
            commentForm.remove();
        }

        var params = new Array();
        params['document_srl'] = document_srl;
        //params['mid'] = 'test';

        exec_xml('textyle','dispCommentEditor', params, showCommentEditor, new Array('error', 'message', 'html'));
    }

    return false;
}

function showCommentEditor(response, response_tags)
{
	//response['html'] = decodeURIComponent(response['html']);
	response['html'] = base64_decode(response['html']);
	
    var pos = -1, posEnd, head = jQuery("head"), labScript = '$LAB';

    while ((pos = response['html'].indexOf('<!--#Meta:', pos + 1)) > -1)
    {
        posEnd = response['html'].indexOf('-->', pos);

        // Check if the resource has extension .CSS
        if (response['html'].substr(posEnd - 4, 4) == '.css')
        {
            // 10 is the length of "<!--Meta:."
            head.append('<link rel="stylesheet" type="text/css" href="' + response['html'].substring(pos + 10, posEnd) + '">');
        }
        else{
            // 10 is the length of "<!--Meta:."
            labScript += '.script("' + response['html'].substring(pos + 10, posEnd) + '").wait()';
        }
    }

    labScript += '.wait(function(){' +
        'jQuery("#editor-box").append(jQuery(response["html"]));' +
        '$LAB.runQueue();' +
        'jQuery("#loadEditorButton").hide();' +
        'scrollTo("#editor-box");' +
        '});';

    eval(labScript);
}

function base64_decode (data) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        dec = "",
        tmp_arr = [];
 
    if (!data) {
        return data;
    }
 
    data += '';
 
    do { // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));
 
        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;
 
        o1 = bits >> 16 & 0xff;
        o2 = bits >> 8 & 0xff;
        o3 = bits & 0xff;
 
        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);
 
    dec = tmp_arr.join('');
    dec = this.utf8_decode(dec);
 
    return dec;
}

function utf8_decode (str_data) {
    var tmp_arr = [],
        i = 0,
        ac = 0,
        c1 = 0,
        c2 = 0,
        c3 = 0;
 
    str_data += '';
 
    while (i < str_data.length) {
        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if (c1 > 191 && c1 < 224) {
            c2 = str_data.charCodeAt(i + 1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i + 1);
            c3 = str_data.charCodeAt(i + 2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    }
 
    return tmp_arr.join('');
}

function doDeleteComment(comment_srl)
{
    var params = new Array();
    params['comment_srl'] = comment_srl;
    exec_xml('textyle','procTextyleCommentItemDelete', params, function()
    {
        jQuery('#comment_' + comment_srl).hide(1000).remove();
    });
}

function modifyCommentForm(comment_srl)
{
    var loadComment = true,
        commentForm = jQuery('form.wikiEditor');

    if (commentForm.length)
    {
        loadComment = confirm('Would you like to cancel editing your comment?');
    }

    if (loadComment)
    {
        if (commentForm.length)
        {
            commentForm.siblings().show();
            commentForm.remove();
        }

        var params = new Array();
        params['comment_srl'] = comment_srl;

        exec_xml('textyle','dispModifyComment', params, showModifyCommentForm, new Array('error', 'message', 'html', 'comment_srl'));
    }

    return false;
}

function showModifyCommentForm(response, response_tags)
{
	response['html'] = base64_decode(response['html']);
    var pos = -1, posEnd, head = jQuery("head"), labScript = '$LAB';

    while ((pos = response['html'].indexOf('<!--#Meta:', pos + 1)) > -1)
    {
        posEnd = response['html'].indexOf('-->', pos);

        // Check if the resource has extension .CSS
        if (response['html'].substr(posEnd - 4, 4) == '.css')
        {
            // 10 is the length of "<!--Meta:."
            head.append('<link rel="stylesheet" type="text/css" href="' + response['html'].substring(pos + 10, posEnd) + '">');
        }
        else{
            // 10 is the length of "<!--Meta:."
            labScript += '.script("' + response['html'].substring(pos + 10, posEnd) + '").wait()';
        }
    }

    labScript += '.wait(function(){' +
        'var comment = jQuery("#comment_' + response['comment_srl'] + '");' +
        'comment.children().hide();' +
        'comment.append(jQuery(response["html"]));' +
        '$LAB.runQueue();' +
        'scrollTo("#" + comment.attr("id"));' +
        '});';

    eval(labScript);
}

function scrollTo(elem)
{
    jQuery("html, body").animate({ scrollTop: jQuery(elem).offset().top}, 2000);
}

function replyCommentForm(comment_srl)
{
    var loadComment = true,
        commentForm = jQuery('form.wikiEditor');

    if (commentForm.length)
    {
        loadComment = confirm('Would you like to cancel editing your comment?');
    }

    if (loadComment)
    {
        if (commentForm.length)
        {
            commentForm.siblings().show();
            commentForm.remove();
        }

        var params = new Array();
        params['comment_srl'] = comment_srl;

        exec_xml('textyle','dispReplyComment', params, showReplyCommentEditor, new Array('error', 'message', 'html', 'parent_srl'));
    }

    return false;
}

function showReplyCommentEditor(response, response_tags)
{
	response['html'] = base64_decode(response['html']);
    var pos = -1, posEnd, head = jQuery("head"), labScript = '$LAB';

    while ((pos = response['html'].indexOf('<!--#Meta:', pos + 1)) > -1)
    {
        posEnd = response['html'].indexOf('-->', pos);

        // Check if the resource has extension .CSS
        if (response['html'].substr(posEnd - 4, 4) == '.css')
        {
            // 10 is the length of "<!--Meta:."
            head.append('<link rel="stylesheet" type="text/css" href="' + response['html'].substring(pos + 10, posEnd) + '">');
        }
        else{
            // 10 is the length of "<!--Meta:."
            labScript += '.script("' + response['html'].substring(pos + 10, posEnd) + '").wait()';
        }
    }

    labScript += '.wait(function(){' +
        'var comment = jQuery("#comment_' + response['parent_srl'] + '");' +
        'comment.append(jQuery(response["html"]));' +
        '$LAB.runQueue();' +
        'scrollTo("#" + comment.attr("id"));' +
        '});';

    eval(labScript);
}

function hideCommentForm()
{
    var commentForm = jQuery('form.wikiEditor');

    if (commentForm.length)
    {
        commentForm.siblings().show();
        commentForm.remove();
    }
}

function afterInsertComment(ret_obj)
{
    var form = jQuery('form.wikiEditor'),
        comment = form.siblings('.itemContent'),
        comment_id = 'comment_' + ret_obj['comment_srl'];

    if (comment.parent().attr('id') == comment_id)
    {
        comment.children('.xe_content').empty().append(form.children('[name="content"]').val());

        form.remove();
        comment.show().siblings().show();
    }
    else{
        var pos = location.href.indexOf('c=');
        location = (pos < 0 ? location + (location.href.indexOf('?') < 0 ? '?' : '&') : location.href.substring(0, pos)) + 'c=' + Math.floor(Math.random()*11) + '#' + comment_id ;
    }
}

function toggleCategory(evt) {
    var e = new xEvent(evt);
    var obj = e.target;
    if(obj.nodeName != 'BUTTON') return;

    var node_srl = obj.className.replace(/^category_/,'');
    if(!node_srl) return;

    var li_obj = xGetElementById("category_parent_"+node_srl);
    if(!li_obj) return;
    var className = li_obj.className;

    if(/nav_tree_off/.test(className)) {
        xInnerHtml(obj,'-');
        li_obj.className = className.replace(/nav_tree_off/,'nav_tree_on');
    } else {
        xInnerHtml(obj,'+');
        li_obj.className = className.replace(/nav_tree_on/,'nav_tree_off');
    }
}

xAddEventListener(document, 'click', toggleCategory);