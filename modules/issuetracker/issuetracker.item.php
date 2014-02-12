<?php

    require_once(_XE_PATH_.'modules/document/document.item.php');

    class issueItem extends documentItem {

        var $milestone = null; 
        var $priority = null;
        var $type = null;
        var $status = null;
        var $component = null;
        var $occured_version = null;
        var $closed_status = array('invalid', 'resolve');
        
        function issueItem($document_srl = 0, $load_extra_vars = true) {
            parent::documentItem($document_srl, $load_extra_vars);
        }

        function setIssue($document_srl) {
            $this->document_srl = $document_srl;
            $this->_loadFromDB();
        }

        function setProjectInfo($variables) {
            $this->adds($variables);

            $oIssuetrackerModel = &getModel('issuetracker');
            $project = &$oIssuetrackerModel->getProjectInfo($this->get('module_srl'));

            if($this->get('milestone_srl') && count($project->milestones)) {
                foreach($project->milestones as $val) {
                    if($this->get('milestone_srl')==$val->milestone_srl) {
                        $this->milestone = $val;
                        break;
                    }
                }
            }

            if($this->get('priority_srl') && count($project->priorities)) {
                foreach($project->priorities as $val) {
                    if($this->get('priority_srl')==$val->priority_srl) {
                        $this->priority = $val;
                        break;
                    }
                }
            }

            if($this->get('type_srl') && count($project->types)) {
                foreach($project->types as $val) {
                    if($this->get('type_srl')==$val->type_srl) {
                        $this->type = $val;
                        break;
                    }
                }
            }

            $this->status = $this->get('status');

            if($this->get('component_srl') && count($project->components)) {
                foreach($project->components as $val) {
                    if($this->get('component_srl')==$val->component_srl) {
                        $this->component = $val;
                        break;
                    }
                }
            }

            if($this->get('occured_version_srl') && count($project->releases)) {
                foreach($project->releases as $val) {
                    if($this->get('occured_version_srl')==$val->release_srl) {
                        $this->occured_version = $val;
                        break;
                    }
                }
            }

            if($this->occured_version) {
                foreach($project->packages as $val) {
                    if($this->occured_version->package_srl==$val->package_srl) {
                        $this->package = $val;
                        $this->add('package_srl', $val->package_srl);
                        break;
                    }
                }
            }
        }

        function _loadFromDB($load_extra_vars=true) {
            parent::_loadFromDB($load_extra_vars);

            $obj->target_srl = $this->document_srl;
            $output = executeQuery("issuetracker.getIssue", $obj);
            if(!$output->toBool()) return;

            $this->setProjectInfo($output->data);
        }

        function getMilestoneTitle() {
            if($this->milestone) return $this->milestone->title;
        }

        function getTypeTitle() {
            if($this->type) return $this->type->title;
        }

        function getPriorityTitle() {
            if($this->priority) return $this->priority->title;
        }

        function getComponentTitle() {
            if($this->component) return $this->component->title;
        }

        function getResolutionTitle() {
            if($this->resolution) return $this->resolution->title;
        }

        function getStatus() {
            $status_lang = Context::getLang('status_list');
            return $status_lang[$this->status];
        }

        function getOccuredVersionTitle() {
            if($this->occured_version) return $this->occured_version->title;
        }

        function getReleaseTitle() {
            return $this->getOccuredVersionTitle();
        }

        function getPackageTitle() {
            if($this->package) return $this->package->title;
        }

		function replaceRevision($matches) 
		{
			return $matches[1].sprintf('<a href="%s" onclick="window.open(this.href); return false;">r%s</a>',getUrl('','mid',Context::get('mid'),'act','dispIssuetrackerViewSource','type','compare','erev',$matches[2],'brev',''),$matches[2])."</a>";
		}

		function replaceRevision2($matches)
		{
			return sprintf('<a href="%s" onclick="window.open(this.href); return false;">[%s]</a>', getUrl('','mid',Context::get('mid'),'act','dispIssuetrackerViewSource','type','compare','erev',$matches[1],'brev',''),$matches[1]);
		}

		function replaceIssueNumber($matches) 
		{
			return $matches[1].sprintf('<a href="%s" onclick="window.open(this.href); return false;">#%s</a>',getUrl('','document_srl',$matches[2]),$matches[2])."</a>";
		}

		function replaceContent($content)
		{
			$content = preg_replace_callback('!(^| |\t|>)r([0-9]+)!s', array($this, replaceRevision), $content);
			$content = preg_replace_callback('!\[([0-9]+)\]!s', array($this, replaceRevision2), $content);
			$content = preg_replace_callback('!(^| |\t|>)#([0-9]+)!s', array($this, replaceIssueNumber), $content);
			return $content;	
		}

        function getContent($add_popup_menu = true, $add_content_info = true, $resource_realpath = false) {
            $content = parent::getContent($add_popup_menu, $add_content_info, $resource_realpath);
			$content = preg_replace_callback('!(^| |\t|>)r([0-9]+)!s', array($this, replaceRevision), $content);
			$content = preg_replace_callback('!\[([0-9]+)\]!s', array($this, replaceRevision2), $content);
			$content = preg_replace_callback('!(^| |\t|>)#([0-9]+)!s', array($this, replaceIssueNumber), $content);
            return $content;
        }

        function isClosed() {
            return in_array($this->status, $this->closed_status);
        }

        function isAccessible() {
            $grant = Context::get('grant');
            if($grant->commiter) return true;
            else return parent::isAccessible() || $this->isGranted();
        }

        /**
         * @brief 댓글 에디터 html을 구해서 return
         **/
        function getCommentEditor() {
            if(!$this->isEnableComment()) return;

            $oEditorModel = &getModel('editor');
            return $oEditorModel->getModuleEditor('comment', $this->get('module_srl'), null, 'history_srl', 'content');
        }


    }
?>
