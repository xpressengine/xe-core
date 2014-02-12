//////////////////////////////////////////////////////////////////////////////// 
// 
// NHN CORPORATION
// Copyright 2002-2007 NHN Coporation 
// All Rights Reserved. 
// 
// 이 문서는 NHN㈜의 지적 자산이므로 NHN(주)의 승인 없이 이 문서를 다른 용도로 임의 
// 변경하여 사용할 수 없습니다. 
// 
// 파일명 : flashObject.js 
// 
// 작성일 : 2009.02.02 
// 
// 최종 수정일: 2009.04.08
// 
// Version : 1.1.0
// 
////////////////////////////////////////////////////////////////////////////////

/**
 * @author seungkil choi / kgoon@nhncorp.com
 */

 if (typeof nhn == 'undefined') nhn = {};
 
 nhn.FlashObject = (function(){
 	
	var FlashObject = {};
 	
	//-------------------------------------------------------------
	// private properties
	//-------------------------------------------------------------
	var sClassPrefix = 'F' + new Date().getTime() + parseInt(Math.random() * 1000000);
	var bIE = /MSIE/i.test(navigator.userAgent);
	var bFF = /FireFox/i.test(navigator.userAgent);
	var bChrome = /Chrome/i.test(navigator.userAgent);
		
	
	
    /**
     *
     *  @param oElement 이벤트 등록 객체.
     *  @param sEvent	등록할 이벤트 Type
     *  @param fHandler	이벤트 핸들러
     *  @return void
     */
	var bind = function(oElement, sEvent, fHandler) 
	{
		
		if (typeof oElement.attachEvent != 'undefined')
			oElement.attachEvent('on' + sEvent, fHandler);
		else
			oElement.addEventListener(sEvent, fHandler, true);
		
	};
	
	
	var objectToString = function(oObj, sSeparator)
	{
		
		var s = "";
		var first = true;
		var name = "";
		var value;

		for (var p in oObj)
		{
			if (first)
				first = false;
			else
				s += sSeparator;

			value = oObj[p];
			
			switch (typeof(value)) {
				case "string":
					s += p + '=' + encodeURIComponent(value);
					break;

				case "number":
					s += p + '=' + encodeURIComponent(value.toString());
					break;

				case "boolean":
					s += p + '=' + (value ? "true" : "false");
					break;

				default:
					// array 이거나 object 일때 변환하지 않는다.
			}
		}

		return s;
	}

    /**
     *  ExternalInterface 
     *  for 'Out of memory line at 56' error
     *
     *  @return void
     */
	var unloadHandler = function() {
		
		obj = document.getElementsByTagName('OBJECT');

		for (var i = 0, theObj; theObj = obj[i]; i++) {

			theObj.style.display = 'none';

			for (var prop in theObj)
				if (typeof(theObj[prop]) == 'function')
					try { theObj[prop] = null; } catch(e) {}

		}
		
	};
	
    /**
     *
     *  @param e		
     *  @return void
     */
	var wheelHandler = function(e) {
		
		e = e || window.event;
		
		var nDelta = e.wheelDelta / (bChrome ? 360 : 120);
		if (!nDelta) nDelta = -e.detail / 3;
		
		var oEl = e.target || e.srcElement;
		
		if (!(new RegExp('(^|\b)' + sClassPrefix + '_([a-z0-9_$]+)(\b|$)', 'i').test(oEl.className))) return;
		
		var sMethod = RegExp.$2;

		var nX = 'layerX' in e ? e.layerX : e.offsetX;
		var nY = 'layerY' in e ? e.layerY : e.offsetY;
		
		try {
			
			if (!oEl[sMethod](nDelta, nX, nY)) {

				if (e.preventDefault) e.preventDefault();
				else e.returnValue = false;

			}
			
		} catch(err) {
		}
		
	};	

	/**
	 * 
	 * @param {Object} oEl	오브젝트 참조
	 */
	var getAbsoluteXY = function(oEl) {
		
		var oPhantom = null;
	
		// getter
		var bSafari = /Safari/.test(navigator.userAgent);
		var bIE = /MSIE/.test(navigator.userAgent);
	
		var fpSafari = function(oEl) {
	
			var oPos = { left : 0, top : 0 };
	
			// obj.offsetParent is null in safari, because obj.parentNode is '<object>'.
			if (oEl.parentNode.tagName.toLowerCase() == "object") {
				oEl = oEl.parentNode;
			}
	
			for (var oParent = oEl, oOffsetParent = oParent.offsetParent; oParent = oParent.parentNode; ) {
	
				if (oParent.offsetParent) {
					oPos.left -= oParent.scrollLeft;
					oPos.top -= oParent.scrollTop;
				}
	
				if (oParent == oOffsetParent) {
					oPos.left += oEl.offsetLeft + oParent.clientLeft;
					oPos.top += oEl.offsetTop + oParent.clientTop ;
	
					if (!oParent.offsetParent) {
						oPos.left += oParent.offsetLeft;
						oPos.top += oParent.offsetTop;
					}
	
					oOffsetParent = oParent.offsetParent;
					oEl = oParent;
				}
			}
	
			return oPos;
	
		};
	
		var fpOthers = function(oEl) {
	
			var oPos = { left : 0, top : 0 };
	
			for (var o = oEl; o; o = o.offsetParent) {

				oPos.left += o.offsetLeft;
				oPos.top += o.offsetTop;

			}

			for (var o = oEl.parentNode; o; o = o.parentNode) {

				if (o.tagName == 'BODY') break;
				if (o.tagName == 'TR') oPos.top += 2;

				oPos.left -= o.scrollLeft;
				oPos.top -= o.scrollTop;

			}
	
			return oPos;
	
		};
	
		return (bSafari ? fpSafari : fpOthers)(oEl);
	}
	
	/**
	 * 
	 */
	var getScroll = function() {
		var bIE = /MSIE/.test(navigator.userAgent);
		
		if (bIE) {
			var sX = document.documentElement.scrollLeft || document.body.scrollLeft;
			var sY = document.documentElement.scrollTop || document.body.scrollTop;
			return {scrollX:sX, scrollY:sY}
		}
		else {
			return {scrollX:window.pageXOffset, scrollY:window.pageYOffset};
		}
	}
	
	/**
	 * 
	 */
	var getInnerWidthHeight = function() {
		var bIE = /MSIE/.test(navigator.userAgent);
		var obj = {};
		
		if (bIE) {
			obj.nInnerWidth = document.documentElement.clientWidth || document.body.clientWidth;
			obj.nInnerHeight = document.documentElement.clientHeight || document.body.clientHeight;
		}
		else {
			obj.nInnerWidth = window.innerWidth;
			obj.nInnerHeight = window.innerHeight;
		}
		return obj;
	}


	//-------------------------------------------------------------
	// public static function
	//-------------------------------------------------------------

    /**
     *
     *  @param div			
     *  @param sTag			
     *  		
     *  @return void
     */
	FlashObject.showAt = function(sDiv, sTag){
		document.getElementById(sDiv).innerHTML = sTag;
	}


    /**
     *  generateTag 
     *
     *  @param sURL			
     *  @param nWidth		(default : 100%)
     *  @param nHeight		(default : 100%)
     *  @param oParam		(default : null)
     *  @param sAlign		
     *  @param sFPVersion	
     *  		
     *  @return void
     */
	FlashObject.show = function(sURL, sID, nWidth, nHeight, oParam, sAlign, sFPVersion){
		document.write( FlashObject.generateTag(sURL, sID, nWidth, nHeight, oParam, sAlign, sFPVersion) );
	}


    /**
     *
     *  @param sURL			
     *  @param nWidth		(default : 100%)
     *  @param nHeight		(default : 100%)
     *  @param oParam		(default : null)
     *  @param sAlign		
     *  @param sFPVersion	
     *  		
     *  @return String
     */
	FlashObject.generateTag = function(sURL, sID, nWidth, nHeight, oParam, sAlign, sFPVersion) {
		
		nWidth = nWidth || "100%";
		nHeight = nHeight || "100%";
		sFPVersion = sFPVersion || "9,0,0,0";
		sAlign = sAlign || "middle";
		
		var oOptions = FlashObject.getDefaultOption();
		
		if (oParam)
		{
			if(oParam.flashVars && typeof(oParam.flashVars) == "object")
				oParam.flashVars = objectToString(oParam.flashVars, "&");
			
			for (var key in oParam)
				oOptions[key] = oParam[key];
		}

		var sClsID = 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000';
		var sCodeBase = 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=' + sFPVersion;

		var sStyle = 'position:relative !important;';
		var sClassName = sClassPrefix + '_' + oOptions.wheelHandler;

		var objCode = [];
		var embedCode = [];
		
		
		objCode.push('<object classid="' + sClsID + '" codebase="' + sCodeBase + '" class="' + sClassName + '" style="' + sStyle + '" ' + '" width="' + nWidth + '" height="' + nHeight + '" id="' + sID + '" align="' + sAlign + '">');
		objCode.push('<param name="movie" value="' + sURL + '" />');

		embedCode.push('<embed width="' + nWidth + '" height="' + nHeight + '" name="' + sID + '" class="' + sClassName + '" style="' + sStyle + '" ' + '" src="' + sURL + '" align="' + sAlign + '" ');
		
		
		for(var vars in oOptions){
			objCode.push('<param name="'+vars+'" value="' + oOptions[vars] + '" />');
			embedCode.push(vars +'="' + oOptions[vars] + '" ');
		}

		embedCode.push('type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'); 

		objCode.push(embedCode.join(""));
		objCode.push('</object>');
		

		if (bind) {
			bind(window, 'unload', unloadHandler);
			bind(document, !bFF ? 'mousewheel' : 'DOMMouseScroll', wheelHandler);
			bind = null;
		}

		return objCode.join("");

	};


    /**
     *
     *  @return object
     */
	FlashObject.getDefaultOption = function() {
		return {
					 quality:"high",
					 bgColor:"#FFFFFF", 
					 allowScriptAccess:"always",
					 wmode:"window",
					 menu:"false",
					 allowFullScreen:"true"
				};
	};
	

    /**
     *
     *  @param objID		 ID
     *  @param doc			default : null
     *  @return object
     */
	FlashObject.find = function(sID, oDoc) {
		oDoc = oDoc || document;
		return oDoc[sID] || oDoc.all[sID];
	};

    /**
     *
     *  @param objID		 ID
     *  @param value		
     *  @return void
     */
	FlashObject.setWidth = function(sID, value) {
		FlashObject.find(sID).width = value;
	};
	
    /**
     *
     *  @param objID		ID
     *  @param value		
     *  @return void
     */
	FlashObject.setHeight = function(sID, value) {
		FlashObject.find(sID).height = value;
	};
	
    /**
     *
     *  @param objID		ID
     *  @param nWidth		
     *  @param nHeight		
     *  @return void
     */
	FlashObject.setSize = function(sID, nWidth, nHeight) {
		FlashObject.find(sID).height = nHeight;
		FlashObject.find(sID).width = nWidth;
	};
	
	/**
	 * 
	 * 	@param sID			ID
	 */
	FlashObject.getPositionObj = function(sID){
		var targetObj = FlashObject.find(sID);
		if(targetObj == null)
			return null;
			
		var absPosi = getAbsoluteXY(targetObj);
		var scrollPosi = getScroll();
		
		var obj = {}
		obj.absoluteX = absPosi.left;
		obj.absoluteY = absPosi.top;
		obj.scrolledX = obj.absoluteX - scrollPosi.scrollX;
		obj.scrolledY = obj.absoluteY - scrollPosi.scrollY;
		obj.browserWidth = getInnerWidthHeight().nInnerWidth;
		
		return obj;		
	}
	
	return FlashObject;
 })()
