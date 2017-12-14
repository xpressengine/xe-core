(function($){
	"use strict";

	$.widget('xe.fileUploader', $.blueimp.fileupload, {
		options: {
			loadedFileList: function(e,d) {console.debug('loadedFileList', e, d);},
			/**
			 * 이미지 자동 본문 삽입
			 */
			imageAutoAttach: true,
			sequentialUploads: true,

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
				fileList: '.xefu-list-container',
				filelistOther: '.xefu-list-other ul',
				filelistImages: '.xefu-list-images ul',

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
				actDeselectAll : '.xefu-deselect-all',

				// 버튼
				actInsertMedia : 'xefu-act-insert-media',
				actDeleteFile : '.xefu-act-delete',
				actSetCover : '.xefu-act-set-cover',

				// 상태
				statusCount: '.xefu-status-count',
				statusAttachedSize: '.xefu-status-attached-size',
				statusLimitSize: '.xefu-status-limit-size',
				statusFiletypesContainer: '.xefu-allowed-filetypes-container',
				statusFiletypesText: '.xefu-allowed-filetypes',
			},
			/**
			 * FinderSelect 설정
			 */
			configFinderSelect: {
				children: 'li',
				enableDesktopCtrlDefault: true,
				enableClickDrag: true
			},
			/**
			 * $.blueimp.fileupload.add()
			 */
			add: function (e, data) {
				var $this = $(this);
				var that = $(this);
				var options = $this.fileUploader('option');
				var fileSize = data.files[0].size;
				console.debug('add() that', that);

				var overLimit = false;

				if(options.limitFileSize <= data.files[0].size) {
					alert(window.xe.msg_exceeds_limit_size);
					return false;
				} else {
					data.submit();
				}
			},
			/**
			 * $.blueimp.fileupload.done()
			 */
			done: function() {
				console.debug('done()', arguments)
			},
			/**
			 * $.blueimp.fileupload.stop()
			 */
			stop: function(e, res) {
				console.debug('stop()', res, this)
				var $this = $(this);

				var that = $this.data('xe-fileUploader');

				that._loadFiles();
			}
		},
		booted: false,
		files: [],
		selectMode: false,
		isTouchDevice: false,
		/**
		 * create
		 */
		_create: function() {
			console.debug('xe.fileUploader._create()');
			var that = this;

			this.options.sequentialUploads = true;
			this.options.leftUploadLimit = this.options.limitTotalFileSize;

			this._super();

			this.isTouchDevice = window.Modernizr.touch;
			this.files = {};
			this.selected_files = [];

			if(this.isTouchDevice) {
				this.element.addClass('xefu-select-mode');
			}

			this.options.formData = {
				'editor_sequence': null,
				'upload_target_srl' : null,
				'mid' : window.current_mid,
				'act': 'procFileUpload'
			};

			// finderSelect
			this.finderSelect = this.element.find(this.options.classes.fileList).finderSelect({
				children: 'li',
				enableDesktopCtrlDefault: true,
				enableClickDrag: true
			});
		},
		toggleSelectMode: function() {
			console.debug('toggleSelectMode()');
			this.element.toggleClass('xefu-select-mode');
			this.element.find(this.options.classes.fileList).finderSelect('unHighlightAll');
		},
		_init: function() {
			console.debug('xe.fileUploader._init()')
			var that = this;

			this._super();

			this.element.find('.xefu-image-auto-attach').prop('checked', this.options.imageAutoAttach);

			this.element.find('.xefu-image-auto-attach').on('change', function() {
				var $el = $(this);
				console.debug($el.prop('checked'))
				that.options.imageAutoAttach = $el.prop('checked');
			})

			// 본문 삽입
			this.element.on('click', '.' + this.options.classes.actInsertMedia, function(e) {
				e.preventDefault();
				e.stopPropagation();
				var $el = $(this);
				var file_srl = $el.data('file-srl') || $el.closest('.xefu-file').data('file-srl');

				that._insertToContent([file_srl]);
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

				that.toggleSelectMode();
			});

			/* controll */
			// 전체 파일 선택
			this.element.on('click', this.options.classes.actSelectAll, function() {
				that.finderSelect.finderSelect('highlightAll');
			});

			// 선택해제
			this.element.on('click', this.options.classes.actDeselectAll, function() {
				that.finderSelect.finderSelect('unHighlightAll');
			});

			// 이미지 전체 선택
			this.element.on('click', this.options.classes.actSelectAllImages, function() {
				that.finderSelect.finderSelect('highlight', that.element.find(that.options.classes.filelistImages).find('li'));
			});

			// 선택 파일 삽입
			this.element.on('click', this.options.classes.actSelectedInsertContent, function(e) {
				e.preventDefault();

				var selected = that.finderSelect.finderSelect('selected');
				var file_srls = [];

				selected.each(function(idx, el){
					file_srls.push($(el).data('file-srl'));
				});

				console.debug('actSelectedInsertContent file_srls', file_srls);
				that._insertToContent(file_srls);
				that.finderSelect.finderSelect('unHighlightAll');
			});

			// 선택 파일 삭제
			this.element.on('click', this.options.classes.actSelectedDeleteFile, function(e) {
				e.preventDefault();

				var selected = that.finderSelect.finderSelect('selected');
				var file_srls = [];

				selected.each(function(idx, el){
					file_srls.push($(el).data('file-srl'));
				});

				console.debug('act Selected Delete File file_srls', file_srls);
				that._deleteFile(file_srls);
				that.finderSelect.finderSelect('unHighlightAll');
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
		 *
		 * @param      {<type>}  data    The data
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
				this.element.find(options.classes.fileList).hide();
				this.element.find(options.classes.controll).hide();
				return;
			}

			console.debug('_renderList', that.files, data.files, files);

			$.each(data.files, function (index, file) {
				files.push(file.file_srl);
				if(that.files[file.file_srl]) return;

				that.files[file.file_srl] = file;

				if(/\.(jpe?g|png|gif)$/i.test(file.source_filename)) {
					result_image.push(template_fileitem_image(file));
					new_images.push(file.file_srl);
				}
				else
				{
					result.push(template_fileitem(file));
				}
			});

			$.each(that.files, function (index, file) {
				if($.inArray(file.file_srl, files) !== -1) return;

				var $list = $(that.options.classes.filelistImages);
				$list.find('[data-file-srl=' + file.file_srl + ']').remove();
			});

			this.element.find(options.classes.filelistImages).append(result_image.join(''))
			this.element.find(options.classes.filelistOther).append(result.join(''))
			this.element.find(options.classes.fileList).show();
			this.element.find(options.classes.controll).show();
			if(this.options.imageAutoAttach) this._insertToContent(new_images);

			this.finderSelect.finderSelect('unHighlightAll');

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
			console.debug('_loadFiles', this.files);
			var that = this;
			var options = this.options;
			var data = {};
			data.mid = window.current_mid;
			data.editor_sequence = this.options.editorSequence;

			$.exec_json('file.getFileList', data, function(res) {
				console.debug('_loadFiles', res);

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
			console.debug('_insertToContent', file_list);
			var that = this;
			var temp_code = '';
			var editorSequence = this.options.editorSequence;

			$.each(file_list, function(idx, file_srl) {
				var fileinfo = that.files[file_srl];

				if(!fileinfo) return;

				if(/\.(jpe?g|png|gif)$/i.test(fileinfo.download_url)) {
					if(fileinfo.download_url.indexOf('http://')!=-1 || fileinfo.download_url.indexOf('https://')!=-1) {
						temp_code += '<p><img src="' + fileinfo.download_url + '" alt="' + fileinfo.source_filename + '" editor_component="image_link" data-file-srl="' + fileinfo.file_srl + '" /></p>';
					}
					else {
						temp_code += '<p><img src="' + window.request_uri + fileinfo.download_url + '" alt="' + fileinfo.source_filename + '" editor_component="image_link" data-file-srl="' + fileinfo.file_srl + '" /></p>';
					}
				} else {
					temp_code += '<a href="' + window.request_uri + fileinfo.download_url + '" data-file-srl="' + fileinfo.file_srl + '">' + fileinfo.source_filename + "</a>\n";
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
			console.debug('_deleteFile()', file_list, this);
			var that = this;
			var file_srls = '';
			var editorSequence = this.options.editorSequence;
			var _ck = _getCkeInstance(editorSequence);

			file_srls = file_list.join(',');

			if(!file_srls) return;

			window.exec_json('file.procFileDelete', {'file_srls': file_srls, 'editor_sequence': editorSequence}, function(res) {
				console.debug('file.procFileDelete', file_srls, res);
				$.each(file_list, function(idx, srl) {
					var img = _ck.document.find('img');

					for(var i = 0; i <= img.count() - 1; i++) {
						var elItem = img.getItem(i);
						if(elItem.getAttribute('data-file-srl') == srl) {
							var elParent = elItem.getParent();
							console.debug('_deleteFile', elParent, elParent.getHtml(), elParent.getChildCount(), elParent.getChildren())

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
				console.debug('file.procFileSetCoverImage', res);
			});
		}
	});
})(jQuery);
