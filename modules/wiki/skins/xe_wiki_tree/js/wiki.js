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
var leftWidth;
var docHeight;
var marginRight = 50;
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

function resizeDiv(docHeight)
{
	// Make sure left side column has full height, no matter what content is in the main area
	var windowHeight = jQuery(window).height()-140; // What does 140 stand for?
	var tree = jQuery("#leftSideTreeList");
	
	if (docHeight < windowHeight)
    {
		docHeight = windowHeight;
    }
    tree.height(docHeight);

	// Position show/hide arrow in page
	var treeWidth = tree.width();
	var toggleButton = jQuery("#showHideTree");

	if(tree.is(":visible"))
	{
		toggleButton.css("left", treeWidth - 13);
	}
	else 
	{
		toggleButton.css("left", 0);
	}
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
				console.log(htmlText);
				//var htmlText = diffString(old,current);
				jQuery('#diff'+history_srl).html(htmlText);
				jQuery(type+'[name*="diff"]').each(function()
				{
					if (!jQuery(this).hasClass("hide")) 
					jQuery(this).addClass("hide");
				});
				jQuery('#diff'+history_srl).toggleClass("hide");
				docHeight = jQuery("#wikiBody").height();
				resizeDiv(docHeight);
			}
		);
    }
    docHeight = jQuery("#wikiBody").height();
    resizeDiv(docHeight);
}

jQuery(document).ready(function(){
	
	// Initialize tree view, if a classic tree is used (instead of MSDN style)
	// TODO Only load this when classic view is used
    jQuery("#navigation").treeview({
	    animated: "fast",
	    collapsed: false,
	    unique: false,
	    persist: "cookie"
    });
	
	// Define show/hide tree behaviour
    jQuery("#showHideTree").click(function()
    {
		var tree = jQuery("#leftSideTreeList");
		var treeWidth = tree.width();
		var toggleButton = jQuery("#showHideTree");
		var wikiBody = jQuery("#wikiBody");	
		
		tree.animate({
							width: 'toggle'
						 }
						 , 200
						 , function() {
								resizeDiv(docHeight);
						   }
						);		
		
		var treeIsVisible = tree.width() > 1;
		if(treeIsVisible)
		{
			// Hide left tree elements during slide
			tree.children().hide();
			
			// Remove body left padding - the one used for the left sidebar
			wikiBody.animate({
				'padding-left': '0'
			}, 200);			
			
			// Position toggle button
			toggleButton.animate({
									left: '-='+(treeWidth-13)
								}, 200
								, function() {
									tree.children().show();
									toggleButton.css('background-position', "-13px 0px");
									toggleButton.attr("title",titleDivShowHideTree[1]);
								});
		}
		else
		{
			// Hide left tree elements, otherwise they look funny during the resize
			tree.children().hide();
			
			// Re-add the body padding
			wikiBody.animate({
				'padding-left': '250px'
			}, 200);	
			
			// Position toggle button
			toggleButton.animate({
								  left: '+='+(treeWidth-13)
								}, 200
								, function() {
									tree.children().show();		
									toggleButton.css('background-position', "0px 0px");
									toggleButton.attr("title",titleDivShowHideTree[0]);
								});								
		}
    });
});

jQuery(window).load(function() {
    docHeight = jQuery("#wikiBody").height();
    resizeDiv(docHeight);
	
	// Prepare tooltip for Show/Hide toggle button
    if (jQuery("#showHideTree").length > 0)
    {
		titleDivShowHideTree = jQuery("#showHideTree").attr("title").split("/");
		jQuery("#showHideTree").attr("title",titleDivShowHideTree[0]);
    }
});

jQuery(window).resize(function(){
    docHeight = jQuery("#wikiBody").height();
    resizeDiv(docHeight);
});

function loadCommentForm(document_srl)
{
	jQuery.exec_json
	(
		"wiki.procDispCommentEditor",
		{
			"document_srl": document_srl
		},
		function(data)
		{
			var editor = data.editor;
			var pos = -1;
			var posEnd;
			while ((pos = editor.indexOf('<!--#Meta:', pos + 1)) > -1)
			{
				posEnd = editor.indexOf('-->', pos);

				// Check if the resource has extension .CSS
				if (editor.substr(posEnd - 4, 4) == '.css')
				{
					// 10 is the length of "<!--#Meta:"
					jQuery("head").append('<link rel="stylesheet" type="text/css" href="' + editor.substring(pos + 10, posEnd) + '" />');
				}
				else{
					// 10 is the length of "<!--#Meta:"
					jQuery("head").append('<script src="' + editor.substring(pos + 10, posEnd) + '"></script>');
				}
			}
			jQuery('div.editor').append(editor);
			jQuery("#editor-box").hide();
			
			jQuery(".wikiEditor .wikiEditorSubmit").show();
			jQuery(".wikiEditor .editorOption").show();
			
			jQuery("div.commentEditor").find(".wikiNavigation").removeClass("hide");
			scrollTo("div.editor");
			docHeight = jQuery("#wikiBody").height();
			resizeDiv(docHeight);
		}
	)
}

function hideEditor()
{
	jQuery(".wikiEditor .wikiEditorSubmit").hide();	
	jQuery(".wikiEditor .editorOption").hide();	
	
	jQuery('div.editor').html("");
	jQuery("div.commentEditor").find(".wikiNavigation").addClass("hide");
	jQuery("#editor-box").show();
	docHeight = jQuery("#wikiBody").height();
	resizeDiv(docHeight);
}

function scrollTo(elem)
{
    jQuery("html, body").animate({scrollTop: jQuery(elem).offset().top}, 2000);
}
