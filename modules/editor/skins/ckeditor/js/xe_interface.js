(function($){
	"use strict";

	$.widget('xe.CKEditor', {
		options : {
			test: 0,
		},
		default_ckeconfig: {
			bodyClass: 'xe_content editable',
			toolbarCanCollapse: true,
			toolbarGroups: [
				{ name: 'clipboard',   groups: [ 'undo', 'clipboard' ] },
				{ name: 'editing',     groups: [ 'find', 'selection' ] },
				{ name: 'links' },
				{ name: 'insert' },
				{ name: 'tools' },
				{ name: 'document',    groups: [ 'mode' ] },
				'/',
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
				'/',
				{ name: 'styles' },
				{ name: 'colors' },
				{ name: 'xecomponent' },
				{ name: 'others' }
			],
			allowedContent: true,
			removePlugins: 'stylescombo,language,bidi,flash,pagebreak',
			removeButtons: 'Save,Preview,Print,Cut,Copy,Paste',
			extraPlugins: 'autosavemsg',
			uiColor: '#EFF0F0'
		},
		_create: function(ele) {
			var that = this;
			var $form = this.element.closest('form');
			var $contentField = $form.find('[name="' + this.options.contentKeyName + '"]')

			this.editorSequence = this.options.editorSequence || this.element.data('primary-key-name')
			$form.attr('editor_sequence', this.editorSequence);

			this.options.ckeconfig = $.extend({}, this.default_ckeconfig, this.options.ckeconfig);
			this._mergeCkeconfig(this.options.ckeconfig);

			console.debug('xe.CKEditor _create()', this, this.options.ckeconfig);

			// 자동 저장 메시지 표시
			CKEDITOR.plugins.add( 'autosavemsg', {
				init: function(editor) {
					editor._.autosaveMsg = {
						idBase: 'cke_autosave_' + CKEDITOR.tools.getNextNumber() + '_',
						filters: []
					};

					editor.on('uiSpace', function(event) {
						if (event.data.space !== 'bottom') return;

						var spaceId = editor.ui.spaceId( 'autosave' );
						event.data.html += '<span id="' + spaceId + '" class="cke_autosave" style="float: right;"></span>';

						editor.on('uiReady', function() {
							var element = editor.ui.space('autosave');
							element && editor.focusManager.add(element, 1);
							CKEDITOR.document.getById(spaceId);
						});
					});
				}
			});

			// CKEditor instance
			this.ckeInstance = CKEDITOR.appendTo(this.element.get(0), this.options.ckeconfig, $contentField.val());

			this.ckeInstance.on('customConfigLoaded', function() {
				if($.isFunction(CKEDITOR.editorConfig)) {
					var customConfig = {};
					CKEDITOR.editorConfig(customConfig);
					that.ckeInstance.config = that._mergeCkeconfig(customConfig);
				}
			});

			console.debug('xe.CKEditor _create()', this.options.ckeconfig, this.ckeInstance.config);

			$.data(this, 'xe-ckeditor-instance', this.ckeInstance);
			jQuery("#ckeditor_instance_" + this.editorSequence).data('cke_instance', this.ckeInstance);

			console.debug('data xe-ckeditor-instance', $.data(this, 'xe-ckeditor-instance'));

			if(this.options.autosave.enable) {
				this._enableAutosave();
				this._loadAutosave();
			}

			window.editorRelKeys[this.editorSequence] = {};
			window.editorRelKeys[this.editorSequence].primary = this.element.get(0);
			window.editorRelKeys[this.editorSequence].content = $contentField.get(0);
			window.editorRelKeys[this.editorSequence].func = function(seq) {
				return that.getContent.call(that, seq);
			};
			window.editorRelKeys[this.editorSequence].pasteHTML = function(text){
				that.ckeInstance.insertHtml(text, 'html');
			};
		},
		getInstance: function() {
			return $.data(this, 'xe-ckeditor-instance');
		},
		getTitle: function() {
			var $form = this.element.closest('form');
		},
		getContent: function() {
			console.debug('getContent', this.ckeInstance.getData());
			return this.ckeInstance.getData();
		},
		insertContent: function(content) {
			this.ckeInstance.insertHtml(content);
		},
		_autosave: function() {
			var that = this;
			var $form = this.element.closest('form');
			var data = {
				title : $('[name=title]', $form).val() || null,
				content : this.ckeInstance.getData() || null,
				mid : window.current_mid || null,
				document_srl : $('[name=document_srl]', $form).val() || null
			};

			if(!data.title && !data.content) return;

			// 너무 잦은 서버 요청 방지
			if(Modernizr.sessionstorage) {
				var storageKey = ['autosave'];
				storageKey.push(window.current_mid || '*');
				storageKey.join('.');

				if(window.sessionStorage.getItem(storageKey) === JSON.stringify(data)) {
					return;
				}
				window.sessionStorage.setItem(storageKey, JSON.stringify(data));
			}

			// 저장
			window.exec_json('editor.procEditorSaveDoc', data, function(res) {
				console.debug('editor.procEditorSaveDoc', res, that._spaceElement);

				// 자동 저장 완료 메시지 출력
				var spaceId = that.ckeInstance.ui.spaceId('autosave');
				var spaceElement = CKEDITOR.document.getById(spaceId);
				var nowDate = new Date();
				var message = nowDate.toLocaleTimeString(navigator.language, {}) + ' ' + res.message;
				spaceElement.setHtml(message);
			});
		},
		_enableAutosave: function() {
			var that = this;
			var debounceAutosaveServer = debounce(function() { that._autosave()}, 10000);

			setInterval(function() {
				debounceAutosaveServer();
			}, 15000);

			this.ckeInstance.on('saveSnapshot', function(e) {
				debounceAutosaveServer();
			});
		},
		_mergeCkeconfig: function(ckeconfig) {
			var tmepConfig = $.extend({}, this.options.ckeconfig);

			if(ckeconfig.bodyClass) {
				var bodyClass = tmepConfig.bodyClass.split(' ');
				bodyClass.concat(ckeconfig.bodyClass.split(' '));
				bodyClass = arrayUnique(bodyClass);
				tmepConfig.bodyClass = bodyClass.join(' ');
			}

			if(this.options.loadXeComponent) {
				var extraPlugins = this.options.ckeconfig.extraPlugins.split(',');

				extraPlugins.push('xe_component');
				extraPlugins = arrayUnique(extraPlugins);
				tmepConfig.extraPlugins = extraPlugins.join(',');
			}

			// @see https://github.com/xpressengine/xe-core/issues/2207
			if(CKEDITOR.env.iOS && this.options.removePlugins) {
				var removePlugins = this.options.ckeconfig.removePlugins.split(',');

				removePlugins.push('enterkey');
				removePlugins = arrayUnique(removePlugins);
				tmepConfig.removePlugins = removePlugins.join(',');
			}

			if(!this.options.enableToolbar) tmepConfig.toolbar = [];

			this.options.ckeconfig = tmepConfig;

			return this.options.ckeconfig;
		},
		_loadAutosave: function() {
			var that = this;
			var $form = this.element.closest('form');

			if(!this.options.autosave.exists) return;

			if(confirm(this.options.autosave.message)) {
				$form.find('[name=title]').val($form.find('[name=_saved_doc_title]').val());
				this.ckeInstance.setData($form.find('[name=_saved_doc_content]').val());

				var param = new Array();
				param.mid = window.current_mid;
				param.editor_sequence = this.editorSequence;

				window.exec_json('editor.procEditorLoadSavedDocument', param, function(res) {
					if(res.document_srl) {
						window.editorRelKeys[that.editorSequence]['primary'].value = res.document_srl;
					}
				});
			} else {
				that._editorRemoveSavedDoc();
			}
		},
		_editorRemoveSavedDoc: function() {
			window.exec_json('editor.procEditorRemoveSavedDoc', {mid: current_mid});
		}
	});

	function arrayUnique(data) {
		return $.grep(data, function(v, k){
			return (v.length && $.inArray(v, data) === k);
		});
	}

	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	}
})(jQuery);


function _getCkeInstance(editor_sequence) {
	var $editor_area = jQuery("#ckeditor_instance_"+editor_sequence);
	console.debug('_getCkeInstance', $editor_area.data('cke_instance'));
	return $editor_area.data('cke_instance');
}

//Get content from editor
function editorGetContentTextarea_xe(editor_sequence) {
	console.debug('editorGetContentTextarea_xe', _getCkeInstance(editor_sequence).getText());
	return _getCkeInstance(editor_sequence).getText();
}


function editorGetSelectedHtml(editor_sequence) {
	console.debug('editorGetSelectedHtml', _getCkeInstance(editor_sequence).getSelection().getSelectedText());
	return _getCkeInstance(editor_sequence).getSelection().getSelectedText();
}

function editorGetContent(editor_sequence) {
	console.debug('editorGetContent', _getCkeInstance(editor_sequence).getData());
	return _getCkeInstance(editor_sequence).getData();
}

function insertElement(editor_sequence, code) {
	console.debug('insertElement', code);
	return _getCkeInstance(editor_sequence).insertHtml(code);
}

//Replace html content to editor
function editorReplaceHTML(iframe_obj, content) {
	console.debug('editorReplaceHTML', iframe_obj, content);
	content = editorReplacePath(content);

	var editor_sequence = parseInt(iframe_obj.id.replace(/^.*_/, ''), 10);

	_getCkeInstance(editor_sequence).insertHtml(content, "unfiltered_html");
}

function editorGetIFrame(editor_sequence) {
	console.debug('editorGetIFrame', editor_sequence);
	return jQuery('#ckeditor_instance_' + editor_sequence).get(0);
}

function editorReplacePath(content) {
	// 태그 내 src, href, url의 XE 상대경로를 http로 시작하는 full path로 변경
	content = content.replace(/\<([^\>\<]*)(src=|href=|url\()("|\')*([^"\'\)]+)("|\'|\))*(\s|>)*/ig, function(m0,m1,m2,m3,m4,m5,m6) {
		if(m2=="url(") { m3=''; m5=')'; } else { if(typeof(m3)=='undefined') m3 = '"'; if(typeof(m5)=='undefined') m5 = '"'; if(typeof(m6)=='undefined') m6 = ''; }
		var val = jQuery.trim(m4).replace(/^\.\//,'');
		if(/^(http\:|https\:|ftp\:|telnet\:|mms\:|mailto\:|\/|\.\.|\#)/i.test(val)) return m0;
		return '<'+m1+m2+m3+request_uri+val+m5+m6;
	});

	return content;
}


// @DEPRECATED
//Insert uploaded file to editor
function ckInsertUploadedFile(editorSequence){
	var temp_code='';

	var settings = uploaderSettings[editorSequence];
	var fileListAreaID = settings["fileListAreaID"];
	var fileListObj = get_by_id(fileListAreaID);
	if(!fileListObj) return;

	if(editorMode[editorSequence]=='preview') return;

	for(var i=0;i<fileListObj.options.length;i++) {
		if(!fileListObj.options[i].selected) continue;
		var file_srl = fileListObj.options[i].value;
		if(!file_srl) continue;

		var file = uploadedFiles[file_srl];

		if(file.direct_download == 'Y') {
			if(/\.(jpg|jpeg|png|gif)$/i.test(file.download_url)) {
				if(loaded_images[file_srl]) {
					var obj = loaded_images[file_srl];
				}
				else {
					var obj = new Image();
					obj.src = file.download_url;
				}
				temp_code += "<img src=\""+file.download_url+"\" alt=\""+file.source_filename+"\"";
				if(obj.complete == true) { temp_code += " width=\""+obj.width+"\" height=\""+obj.height+"\""; }
				temp_code += " />\r\n";
			} else {
				temp_code="<img src=\"common/img/blank.gif\" editor_component=\"multimedia_link\" multimedia_src=\""+file.download_url+"\" width=\"400\" height=\"320\" style=\"display:block;width:400px;height:320px;border:2px dotted #4371B9;background:url(./modules/editor/components/multimedia_link/tpl/multimedia_link_component.gif) no-repeat center;\" auto_start=\"false\" alt=\"\" />";
			}

		} else {
			temp_code="<a href=\""+file.download_url+"\">"+file.source_filename+"</a>\n";
		}
	}
	cked_instance = 'ckeditor_instance_'+editorSequence;
	CKEDITOR.instances[cked_instance].insertHtml(temp_code);
}


// @DEPRECATED
function editorStartTextarea(editor_sequence, content_key, primary_key) {
    var obj = xGetElementById('editor_'+editor_sequence);
    var use_html = xGetElementById('htm_'+editor_sequence).value;
    obj.form.setAttribute('editor_sequence', editor_sequence);

    obj.style.width = '100%';

    editorRelKeys[editor_sequence] = new Array();
    editorRelKeys[editor_sequence]["primary"] = obj.form[primary_key];
    editorRelKeys[editor_sequence]["content"] = obj.form[content_key];
    editorRelKeys[editor_sequence]["func"] = editorGetContentTextarea;

    var content = obj.form[content_key].value;
    if(use_html) {
        content = content.replace(/<br([^>]*)>/ig,"\n");
        if(use_html!='br') {
            content = content.replace(/&lt;/g, "<");
            content = content.replace(/&gt;/g, ">");
            content = content.replace(/&quot;/g, '"');
            content = content.replace(/&amp;/g, "&");
        }
    }
    obj.value = content;
}

// @DEPRECATED
function editorGetContentTextarea(editor_sequence) {
    var obj = xGetElementById('editor_'+editor_sequence);
    var use_html = xGetElementById('htm_'+editor_sequence).value;
    var content = obj.value.trim();
    if(use_html) {
        if(use_html!='br') {
            content = content.replace(/&/g, "&amp;");
            content = content.replace(/</g, "&lt;");
            content = content.replace(/>/g, "&gt;");
            content = content.replace(/\"/g, "&quot;");
        }
        content = content.replace(/(\r\n|\n)/g, "<br />");
    }
    return content;
}

