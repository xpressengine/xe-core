(function($){
	"use strict";

	$.widget('xe.fileUploader', $.blueimp.fileupload, {
		options: {
			loadedFileList: function(e,d) {dd('loadedFileList', e, d);},
			/**
			 * 이미지 자동 본문 삽입
			 */
			imageAutoAttach: true,
			sequentialUploads: true,
			maxChunkSize: 1024000,

			leftUploadLimit: 0,
			limitFileSize: 0,
			limitTotalFileSize: 0,

			editorSequence: null,
			uploadTargetSrl: null,

			tmplFileItemId: 'tmpl-xefu-item-file',
			tmplImageItemId: 'tmpl-xefu-item-image',
			/**
			 * element 셀렉터 목록
			 */
			classes: {
				// dropzone
				dropZone: '.xefu-dropzone',
				dropZoneMessage: '.xefu-dropzone-message',

				// 파일 목록
				controll: '.xefu-controll',
				fileListContainer: '.xefu-list-container',
				filelistOther: '.xefu-list-other ul',
				filelistImages: '.xefu-list-images ul',
				fileItem: '.xefu-file',

				// progressbar
				progressbar: '.xefu-progressbar',
				progressbarGraph: '.xefu-progressbar div',
				progressStatus: '.xefu-progress-status',
				progressPercent: '.xefu-progress-percent',

				// 버튼: 파일 선택
				actSelectable : '.xefu-act-mode-selectable',
				actSelectedInsertContent : '.xefu-act-link-selected',
				actSelectedDeleteFile : '.xefu-act-delete-selected',
				actSelectAll : '.xefu-select-all',
				actSelectAllImages : '.xefu-select-all-images',
				actUnselectAll : '.xefu-unselect-all',

				// 버튼
				actInsertMedia : '.xefu-act-insert-media',
				actInsertFile : '.xefu-act-insert-file',
				actDeleteFile : '.xefu-act-delete',
				actSetCover : '.xefu-act-set-cover',
				actSelect : '.xefu-act-select',
				actUnselect : '.xefu-act-unselect',

				// 상태
				statusCount: '.xefu-status-count',
				statusAttachedSize: '.xefu-status-attached-size',
				statusLimitSize: '.xefu-status-limit-size',
				statusFiletypesContainer: '.xefu-allowed-filetypes-container',
				statusFiletypesText: '.xefu-allowed-filetypes',
			},
			/**
			 * selectable 설정
			 */
			configSelectable: {
				appendTo: ".xefu-list-container",
				autoRefresh: true,
				distance: 0,
				filter: "li",
				tolerance: "touch",
				toggle: false,
				debug: true,
				selected: function(e, targets) {
					dd('event selected', targets)
				},
				unselected: function(e, targets) {
					dd('event unselected', targets)
				}
			},
			/**
			 * $.blueimp.fileupload.add()
			 */
			add: function (e, data) {
				var $this = $(this);
				var that = $this.data('xe-fileUploader');
				var options = $this.fileUploader('option');
				var fileSize = data.files[0].size;
				var overLimit = false;

				dd('_super.options.add()', that, options, data);

				if(options.limitFileSize <= data.files[0].size || options.leftUploadLimit <= data.files[0].size) {
					alert(window.xe.msg_exceeds_limit_size);
					return false;
				}

				if(/^image\/(gif|jpeg|png)$/.test(data.files[0].type)) {
					var holder = $('<li class="xefu-file xefu-file-image xefu-uploading"><div class="progress"></div></li>').attr('data-uploading', data.files[0].name);

					that.element.find(options.classes.filelistImages).append(holder);
				}

				data.submit();
			},
			send: function() {
				dd('_super.options.send()', arguments);
			},
			/**
			 * $.blueimp.fileupload.done()
			 */
			done: function(e, data) {
				var $this = $(this);
				var that = $this.data('xe-fileUploader');
				var result = JSON.parse(data.result);
				that.latestFiles.push(result);
			},
			/**
			 * $.blueimp.fileupload.stop()
			 */
			stop: function(e, res) {
				dd('_super.options.stop()', e, res, this)
				var $this = $(this);
				var that = $this.data('xe-fileUploader');

				that._loadFiles();
			}
		},
		booted: false,
		files: [],
		latestFiles: [],
		selectMode: false,
		isTouchDevice: false,
		chunkedUpload: false,
		/**
		 * create
		 */
		_create: function() {
			_debug = true//this.options.debug;

			this.options.sequentialUploads = true;
			this.options.leftUploadLimit = this.options.limitTotalFileSize;

			this._super();

			this.isTouchDevice = window.Modernizr.touch;
			this.files = {};
			this.selected_files = [];
			this.chunkedUpload = this.chunkedUpload && typeof $.support.blobSlice !== 'function' && this.options.maxChunkSize;

			this.options.formData = {
				'editor_sequence': null,
				'upload_target_srl' : null,
				'mid' : window.current_mid,
				'act': 'procFileUpload'
			};

			if(!this.chunkedUpload) {
				delete this.options.maxChunkSize;
			}

			if(this.isTouchDevice) {
				this.element.addClass('xefu-select-mode');
			}

			// selectable
			this.options.configSelectable.toggle = this.isTouchDevice;
			this.selectable = $.xe.selectable(this.options.configSelectable, $('.xefu-list-container'));
			if(!Modernizr.touch) this.selectable.disable();

			dd('_create()', this);
		},
		toggleSelectable: function() {
			dd('toggleSelectable()');
			this.element.toggleClass('xefu-select-mode');
			this.selectable.unselectAll();
		},
		modeSelectable: function(selectable) {
			if(!!selectable) {
				this.element.addClass('xefu-select-mode');
				this.selectable.enable();
			} else {
				this.element.removeClass('xefu-select-mode');
				this.selectable.disable();
			}
		},
		_init: function() {
			dd('_init()')
			var that = this;

			this._super();

			this.element.find('.xefu-image-auto-attach').prop('checked', this.options.imageAutoAttach);

			this.element.on('change', '.xefu-image-auto-attach', function() {
				var $el = $(this);
				dd('auto-attach.change', $el.prop('checked'))
				that.options.imageAutoAttach = $el.prop('checked');
			})

			// 본문 삽입: 이미지
			this.element.on('click', this.options.classes.actInsertMedia, function(e) {
				e.preventDefault();

				var $el = $(this);
				var file_srl = $el.data('file-srl') || $el.closest('.xefu-file').data('file-srl');

				that._insertToContent([file_srl]);
			});

			// 본문 삽입: 다운로드
			this.element.on('click', this.options.classes.actInsertFile, function(e) {
				e.preventDefault();

				var $el = $(this);
				var file_srl = $el.data('file-srl') || $el.closest('.xefu-file').data('file-srl');

				that._insertToContent([file_srl]);
			});

			// 선택
			this.element.on('click', this.options.classes.actSelect, function(e) {
				that.element.addClass('xefu-select-mode');
				that.modeSelectable(true);
				that.selectable.select($(e.target).closest('.xefu-file'));
			});

			// 선택
			this.element.on('click', this.options.classes.actUnselect, function(e) {
				that.element.addClass('xefu-select-mode');
				that.selectable.unselect($(e.target).closest('.xefu-file'));
			});

			// 파일 삭제
			this.element.on('click', this.options.classes.actDeleteFile, function(e) {
				e.preventDefault();
				e.stopPropagation();

				var $el = $(this);
				var file_srl = $el.data('file-srl') || $el.closest('.xefu-file').data('file-srl');

				that._deleteFile([file_srl]);
			});

			// 선택 모드 전환
			this.element.on('click', this.options.classes.actSelectable, function(e) {
				e.preventDefault();
				e.stopPropagation();

				that.toggleSelectable();
			});

			/* controll */
			// 전체 파일 선택
			this.element.on('click', this.options.classes.actSelectAll, function() {
				that.selectable.selectAll();
			});

			// 선택해제
			this.element.on('click', this.options.classes.actUnselectAll, function() {
				that.selectable.unselectAll();
			});

			// 이미지 전체 선택
			this.element.on('click', this.options.classes.actSelectAllImages, function() {
				that.selectable.select($('.xefu-file-image'));
			});

			// 선택 파일 삽입
			this.element.on('click', this.options.classes.actSelectedInsertContent, function(e) {
				e.preventDefault();

				var selected = that.selectable.getSelected();
				var file_srls = [];

				dd('actSelectedInsertContent', selected);

				selected.each(function(){
					console.log($.data(this));
					file_srls.push($(this).data('file-srl'));
				});

				dd('actSelectedInsertContent', file_srls);

				that._insertToContent(file_srls);
				that.selectable.unselectAll();
			});

			// 선택 파일 삭제
			this.element.on('click', this.options.classes.actSelectedDeleteFile, function(e) {
				var selected = that.selectable.getSelectedNodes();
				var file_srls = [];

				e.preventDefault();

				if(!selected.length) return;

				selected.forEach(function(node){
					file_srls.push($(node).data('file-srl'));
				});
				dd('actSelectedDeleteFilee', file_srls);

				that._deleteFile(file_srls);
				that.selectable.unselectAll();
			});

			// 커버 이미지로 지정
			this.element.on("click", this.options.classes.actSetCover, function(e){
				e.preventDefault();

				var $el = $(this);

				that._setCover($el.data('file-srl'));

				$el.closest('li').siblings('li').removeClass('xefu-is-cover-image');
				$el.closest('li').addClass('xefu-is-cover-image');
			});
			/* END:controll */

			// 파일 업로드 URL
			this.options.url = window.request_uri
				.setQuery('module', 'file')
				.setQuery('act', 'procFileUpload')
				.setQuery('mid', window.current_mid);
			this.options.editorSequence = this.element.data('editorSequence');
			this.options.formData.editor_sequence = this.options.editorSequence;

			// 첨부된 파일 목록
			this._loadFiles();
		},
		/**
		 * 파일 목록 그리기
		 */
		_renderList: function(data) {
			var that = this;
			var result = [];
			var options = this.options;
			var result_image = [];
			var tmpl_fileitem   = $('#' + options.tmplFileItemId).html();
			var template_fileitem = Handlebars.compile(tmpl_fileitem);
			var tmpl_fileitem_image = $('#' + options.tmplImageItemId).html();
			var template_fileitem_image = Handlebars.compile(tmpl_fileitem_image);
			var new_images = [];
			var files = [];

			if(!data.files.length) {
				this.element.find(options.classes.fileListContainer).hide();
				this.element.find(options.classes.controll).hide();
				return;
			}

			dd('_renderList', that.files, data.files);

			$.each(data.files, function (index, file) {
				files.push(file.file_srl);
				if(that.files[file.file_srl]) return;

				that.files[file.file_srl] = file;

				if(/\.(jpe?g|png|gif)$/i.test(file.source_filename)) {
					var eee = that.element.find(options.classes.filelistImages).find('[data-uploading="' + file.source_filename + '"]');

					if(eee.length) {
						eee.before(template_fileitem_image(file)).remove();
					} else {
						result_image.push(template_fileitem_image(file));
					}
					new_images.push(file.file_srl);
				}
				else
				{
					result.push(template_fileitem(file));
				}
			});

			$.each(that.files, function (index, file) {
				if($.inArray(file.file_srl, files) !== -1) return;

				var $list = $(that.options.classes.fileListContainer);
				$list.find('[data-file-srl=' + file.file_srl + ']').remove();
			});

			this.element.find(options.classes.filelistImages).append(result_image.join(''))
			this.element.find(options.classes.filelistOther).append(result.join(''))
			this.element.find(options.classes.fileListContainer).show();
			this.element.find(options.classes.controll).show();

			// 이미지 자동 삽입
			if(this.options.imageAutoAttach) {
				$.each(that.latestFiles, function (index, file) {
					if(that._isImage(file)) {
						that._insertToContent(file);
					}
				});
				that.latestFiles = [];
			}

			that.selectable.refresh();

			this._updateStatus.call(this, data);
		},
		_updateStatus: function(data) {
			this.element.find(this.options.classes.statusCount).text(data.files.length || 0);
			this.element.find(this.options.classes.statusAttachedSize).text(data.attached_size);
			this.element.find(this.options.classes.statusLimitSize).text(data.allowed_attach_size);

			if(data.allowed_filetypes && data.allowed_filetypes != '*.*') {
				this.element.find(this.options.classes.statusFiletypesContainer).show();
				this.element.find(this.options.classes.statusFiletypesText).show(data.allowed_filetypes);
			}
		},
		_loadFiles: function() {
			var that = this;
			var options = this.options;
			var data = {};
			data.mid = window.current_mid;
			data.editor_sequence = this.options.editorSequence;

			$.exec_json('file.getFileList', data, function(res) {
				dd('_loadFile', that.files, res);

				options.leftUploadLimit = res.left_size;
				if(!options.uploadTargetSrl) {
					options.uploadTargetSrl = res.upload_target_srl;
					options.formData.upload_target_srl = options.uplwoadTargetSrl;
				}

				if(!res.files.length) return;

				that._trigger('loadedFileList', null, res);

				that._renderList.call(that, res);
			});
		},
		/**
		 * 본문 삽입
		 *
		 * @param      {<type>}  file_list  The file list
		 */
		_insertToContent: function(file_list) {
			dd('_insertToContent', file_list);
			var that = this;
			var temp_code = '';
			var editorSequence = this.options.editorSequence;

			$.each(file_list, function(idx, file_srl) {
				var fileinfo = that.files[file_srl];

				if(!fileinfo) return;

				if(/\.(jpe?g|png|gif)$/i.test(fileinfo.download_url)) {
					if(fileinfo.download_url.indexOf('http://') === 0 || fileinfo.download_url.indexOf('https://') === 0) {
						temp_code += '<p><img src="' + fileinfo.download_url + '" alt="' + fileinfo.source_filename + '" editor_component="image_link" data-file-srl="' + fileinfo.file_srl + '" /></p>';
					} else {
						temp_code += '<p><img src="' + window.request_uri + fileinfo.download_url + '" alt="' + fileinfo.source_filename + '" editor_component="image_link" data-file-srl="' + fileinfo.file_srl + '" /></p>';
					}
				} else {
					temp_code += '<a href="' + window.request_uri + fileinfo.download_url + '" data-file-srl="' + fileinfo.file_srl + '">' + fileinfo.source_filename + '</a> ';
				}
			});

			// @FIXME
			insertElement(editorSequence, temp_code);
		},

		/**
		 * 지정된 하나의 파일 또는 다중 선택된 파일 삭제
		 *
		 * @param      {<type>}    el        Element
		 * @param      {Function}  file_srl  The file srl
		 */
		_deleteFile: function(file_list) {
			var that = this;
			var file_srls = '';
			var editorSequence = this.options.editorSequence;
			var _ck = _getCkeInstance(editorSequence);

			dd('_deleteFile()', file_list);
			file_srls = file_list.join(',');

			if(!file_srls) return;

			window.exec_json('file.procFileDelete', {'file_srls': file_srls, 'editor_sequence': editorSequence}, function(res) {
				dd('file.procFileDelete', res);
				$.each(file_list, function(idx, srl) {
					var img = _ck.document.find('img');

					for(var i = 0; i <= img.count() - 1; i++) {
						var elItem = img.getItem(i);
						if(elItem.getAttribute('data-file-srl') == srl) {
							var elParent = elItem.getParent();
							dd('_deleteFile. find element', elParent, elParent.getHtml(), elParent.getChildCount(), elParent.getChildren())

							if(elParent.getChildCount() === 1) {
								elParent.remove();
							} else {
								elItem.remove();
							}
						}
					}
				});
				that._loadFiles();
			});
		},
		_setCover: function(file_srl) {
			var data = {};
			data.file_srl = file_srl;
			data.editor_sequence = this.options.editorSequence;

			window.exec_json('file.procFileSetCoverImage', data, function(res) {
				dd('_setCover(). file.procFileSetCoverImage', data, res);
			});
		},
		_isImage: function(file) {
			return /\.(jpe?g|png|gif)$/i.test(file.download_url)
		}
	});

	var _debug = false;
	function dd() {
		if(!_debug || typeof console.debug !== 'function') return;

		arguments[0] = '[$.xe.fileUploader] ' + arguments[0];

		console.debug.apply(this, arguments);
	}
})(jQuery);
