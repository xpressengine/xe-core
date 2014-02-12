<?php
    /**
     * @class  resourceAdminController
     * @author NHN (developers@xpressengine.com)
     * @brief  resource admin controller class
     **/

    class resourceAdminController extends resource {

        function init() {
        }

        function procResourceAdminInsert() {
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');

            $args = Context::getRequestVars();
            $args->module = 'resource';
            $args->mid = $args->resource_name;
            unset($args->body);
            unset($args->resource_name);

            $args->use_category = 'N';

            if($args->module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
            }

            if(!$args->module_srl) {
                $output = $oModuleController->insertModule($args);
                $msg_code = 'success_registed';
            } else {
                $output = $oModuleController->updateModule($args);
                $msg_code = 'success_updated';
            }

            if(!$output->toBool()) return $output;

            if(Context::get('success_return_url'))
            {
            	changeValueInUrl('mid', $args->mid, $module_info->mid);
            	$this->setRedirectUrl(Context::get('success_return_url'));
            }
            else
            {
            	$this->setRedirectUrl(getNotEncodedUrl('','module','admin','act','dispResourceAdminInsert','module_srl',$output->get('module_srl')));
            }
            $this->setMessage($msg_code);
        }

        function procResourceAdminDelete() {
            $oModuleController = &getController('module');

            $args->module_srl = $module_srl = Context::get('module_srl');

            $output = executeQuery('resource.deleteDependency', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('resource.deleteItems', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('resource.deletePackages', $args);
            if(!$output->toBool()) return $output;

            $output = $oModuleController->deleteModule($module_srl);
            if(!$output->toBool()) return $output;

            if(Context::get('success_return_url'))
            {
            	$this->setRedirectUrl(Context::get('success_return_url'));
            }
            else
            {
            	$this->setRedirectUrl(getNotEncodedUrl('','module','admin','act','dispResourceAdminList'));
            }
            $this->setMessage('success_deleted');
        }

        function procResourceAdminDeletePackage() {
            $oResourceModel = &getModel('resource');
            $oDocumentController = &getController('document');
            $oFileController = &getController('file');

            $site_module_info = Context::get('site_module_info');
            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');
            $package_srl = Context::get('package_srl');
            if(!$package_srl) return new Object(-1,'msg_invalid_request');
            $selected_package = $oResourceModel->getPackage($this->module_srl, $package_srl);
            if(!$selected_package->package_srl) return new Object(-1,'msg_invalid_request');

            $args->package_srl = $package_srl;
            $args->module_srl = $this->module_srl;
            $output = executeQuery('resource.deletePackage', $args);
            if(!$output->toBool()) return $output;

            $output = executeQueryArray('resource.getItems', $args);
            if(!$output->toBool()) return $output;
            if($output->data) {
                foreach($output->data as $key => $val) {
                   if($val->document_srl) $oDocumentController->deleteDocument($val->document_srl,true);
                   $file = $oFileController->deleteFile($val->file_srl);
                }
            }

            $output = executeQuery('resource.deleteItems', $args);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_deleted');
            $this->setRedirectUrl(getSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispResourceManage'));

        }

    }
?>
