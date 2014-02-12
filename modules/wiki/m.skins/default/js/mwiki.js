function viewHistory(history_srl) {
    var zone = jQuery('#historyContent'+history_srl);
    if(zone.css('display')=='block') zone.css('display','none');
    else zone.css('display','block');
}
/**
 * @file   modules/document/tpl/js/document_category.js
 * @author sol (sol@ngleader.com)
 * @brief  document 모듈의 category tree javascript
 **/

var simpleTreeCollection;
var titleDivShowHideTree = new Array();

function Tree(){
    var url = request_uri.setQuery('mid',current_mid).setQuery('act','getWikiTreeList');
    if(typeof(xeVid)!='undefined') url = url.setQuery('vid',xeVid);

    // clear tree;
    jQuery('#tree > ul > li > ul').remove();

    //ajax get data and transeform ul il
    jQuery.get(url,function(data){
        jQuery(data).find("node").each(function(i){
            var title = jQuery(this).text();
            var node_srl = jQuery(this).attr("node_srl");
            var parent_srl = jQuery(this).attr("parent_srl");

            var url = request_uri;
            var args = new Array("mid="+current_mid, "entry="+title);
            if(typeof(xeVid)!='undefined') args[args.length] = "vid="+xeVid;
            url = request_uri+'?'+args.join('&');

            // node
            var node = jQuery('<li id="tree_'+node_srl+'" rel="'+url+'"><span></span></li>');
			jQuery('span', node).text(title);

            // insert parent child
            if(parent_srl>0){
                if(jQuery('#tree_'+parent_srl+'>ul').length==0) jQuery('#tree_'+parent_srl).append(jQuery('<ul>'));
                jQuery('#tree_'+parent_srl+'> ul').append(node);
            }else{
                if(jQuery('#tree ul.simpleTree > li > ul').length==0) jQuery("<ul>").appendTo('#tree ul.simpleTree > li');
                jQuery('#tree ul.simpleTree > li > ul').append(node);
            }
        });

        // draw tree
        simpleTreeCollection = jQuery('.simpleTree').simpleTree({
            autoclose: false,
            afterClick:function(node){ 
                location.href = node.attr('rel');
                return false;
            },
            afterMove:function(destination, source, pos){
                if(!isManageGranted) return;
                if(destination.size() == 0){
                    Tree();
                    return;
                }
                var parent_srl = destination.attr('id').replace(/.*_/g,'');
                var source_srl = source.attr('id').replace(/.*_/g,'');

                var target = source.prevAll("li:not([class^=line])");
                var target_srl = 0;
                if(target.length >0){
                    target_srl = source.prevAll("li:not([class^=line])").get(0).id.replace(/.*_/g,'');
                }

                jQuery.exec_json("wiki.procWikiMoveTree",{"mid":current_mid,"parent_srl":parent_srl,"target_srl":target_srl,"source_srl":source_srl}, function(data){Tree();});

            },
            beforeMovedToLine : function() {return true;},
            beforeMovedToFolder : function() {return true;},
            afterAjax:function() { },

            docToFolderConvert:true,
            drag:isManageGranted
        });
        jQuery("[class*=close]", simpleTreeCollection[0]).each(function(){
            simpleTreeCollection[0].nodeToggle(this);
        });
    },"xml");
}

jQuery.fn.decHTML = function() {
  return this.each(function(){
    var me   = jQuery(this);
    var html = me.html();
    me.html("<div style='text-align:left'>"+html.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>')+"</div>");
  });
};
function getDiff(elem,document_srl,history_srl)
{
    var type = "td";
    var diffDiv = jQuery("#"+elem);
    if (!diffDiv.hasClass("hide"))
    {
	diffDiv.addClass("hide");
    }
    else
    {
	jQuery.exec_json
	(
	    "wiki.procWikiContentDiff",
	    {
		"document_srl": document_srl,
		"history_srl": history_srl
	    },
	    function(data)
	    {
		var old = data.old;
		var current = data.current;
		wDiffHtmlInsertStart = "<span class='wDiffHtmlInsert'>";
		wDiffHtmlInsertEnd = "</span>";
		wDiffHtmlDeleteStart = "<span class='wDiffHtmlDelete'>";
		wDiffHtmlDeleteEnd = "</span>";
		var htmlText = WDiffString(old, current);
		//var htmlText = diffString(old,current);
		jQuery('#diff'+history_srl).html(htmlText);
		jQuery(type+'[name*="diff"]').each(function()
		{
		    if (!jQuery(this).hasClass("hide")) 
			jQuery(this).addClass("hide");
		});
		jQuery('#diff'+history_srl).toggleClass("hide");
	    }
	);
    }
}
jQuery(document).ready(function(){
    if (jQuery("#navigation").length>0)
    {
	jQuery("#navigation").treeview({
		animated: "fast",
		collapsed: false,
		unique: false,
		persist: "cookie"
	});
    }
    jQuery("#content_msg").text(jQuery("input[name='content']").val());
    jQuery("#content_msg").keyup(function(){
	var t = jQuery(this);
	var v = t.val() || t.html() || t.text();
	jQuery("input[name='content']").val(v);
    })
})
