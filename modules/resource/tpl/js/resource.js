function completeChangeStatus() {
    location.reload();
}

function doSearchDependency() {
    var url = request_uri;
    if(typeof(xeVid)!='undefined') url.setQuery('vid', xeVid);
    url = url.setQuery('mid',current_mid).setQuery('act','dispResourceSearchDependency');
    popopen(url,'resourceSearchDependency');
}

function doDeleteDependency() {
    var s = jQuery('#sel_dependency');
    var o = s.find('option:selected');
    var i = s.get(0).selectedIndex;
    o.remove();
    var l = s.find('option').length;
    if(i+1>l) i=l-1;
    if(i>=0) s.get(0).selectedIndex = i;
}

function completeAttach(ret_obj, response_tags, callback_func_args, fo_obj) {
	fo_obj.document_srl.value = ret_obj.document_srl;
	fo_obj.item_srl.value = ret_obj.item_srl;
    fo_obj.act.value = 'procResourceAttachFile';
    fo_obj.submit();
}

function completeModifyAttach(ret_obj, response_tags, callback_func_args, fo_obj) {
    fo_obj.act.value = 'procResourceModifyAttachFile';
    fo_obj.submit();
}

function completeDeletePackage(ret_obj) {
	location.href = current_url.setQuery('act', 'dispResourcePackageList').setQuery('package_srl', '');
}

function doDeletePackage(package_srl) {
    var params = new Array();
    params['package_srl'] = package_srl;
    params['mid'] = current_mid;
    exec_xml('resource','procResourceAdminDeletePackage', params, function() { location.reload(); });
}

function doDeleteAttach(package_srl, item_srl) {
    var params = new Array();
    params['package_srl'] = package_srl;
    params['item_srl'] = item_srl;
    params['mid'] = current_mid;
    exec_xml('resource','procResourceDeleteAttach', params, function() { location.reload(); });
}

function doInsertDependency(item_srl, text) {
    if(!opener) window.close();
    opener.doInsertDependencyItem(item_srl, text);
}

function doInsertDependencyItem(item_srl, text) {
    var o = jQuery('#sel_dependency');
    var stop = false;
    o.find('option').each( function() {
        if(this.value==item_srl) {
            stop = true;
            return;
        }
    });
    if(stop) return;
    o.append(jQuery("<option></option>").attr("value", item_srl).text(text));
}

function doCalDependency(fo_obj) {
    var o = jQuery('#sel_dependency');
    var dependency_srl = new Array();
    o.find('option').each( function() {
        dependency_srl.push(this.value);
    });

    // for backward compatibility, change both value of a attach_dependency and a dependency.
    jQuery('input[name=attach_dependency], input[name=dependency]').val(dependency_srl.join(','));
}

function doDeleteComment(package_srl, item_srl, comment_srl) {
    var params = new Array();
    params['package_srl'] = package_srl;
    params['item_srl'] = item_srl;
    params['comment_srl'] = comment_srl;
    if(typeof(xeVid)!='undefined') params['vid'] = xeVid;
    params['mid'] = current_mid;
    exec_xml('resource','procResourceDeleteComment', params, function() { location.reload() });
}

jQuery(window).load( function() {
    jQuery('ul.starPoint').find('button, a').click(function() {
        var o = jQuery(this);
        var target_point = o.attr('data-point') || o.attr('rel');

        jQuery('ul.starPoint').find('button, a').each( function(i) {
            if(i<target_point) jQuery(this).addClass('on');
            else jQuery(this).removeClass('on');
        });
        jQuery('input[name=star_point]').val(target_point);
    });
    
    var start_point = jQuery('input[name=star_point]').val();
    jQuery('ul.starPoint').find('button').each( function(i) {
        if(i<start_point) jQuery(this).addClass('on');
        else jQuery(this).removeClass('on');
    });
});


function doDeleteItem(package_srl, item_srl) {
    var params = new Array();
    params['package_srl'] = package_srl;
    params['item_srl'] = item_srl;
    if(typeof(xeVid)!='undefined') params['vid'] = xeVid;
    params['mid'] = current_mid;
    exec_xml('resource','procResourceDeleteItem', params, function() { location.reload(); });
}

jQuery(function($){
	var act = current_url.getQuery('act');
	if(act == 'dispResourceAttach' || act == 'dispResourceModifyAttach'){
		
		// create a plugin for performed a task when before validate.
		var BeforeValidateStub = xe.createPlugin('before_validate_stub', {
			API_BEFORE_VALIDATE : function(sender, params) {
				var form = params[0];
				doCalDependency(form);
				
				// for backward compatibility, copy a value of attach_description to description.
				$('input[name="description"]').val($('input[name="attach_description"]').val());
			}
		});
		var v = xe.getApp('validator')[0];
		if(!v) return false;
		v.registerPlugin(new BeforeValidateStub);
		
		// reset a value because validator resotre a value of hidden form
		jQuery('input[name="dependency"]').val('');
	}
});