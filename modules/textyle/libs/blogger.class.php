<?php
    class blogger {
        var $url = null;
        var $user_id = null;
        var $password = null;
        var $blogid = null;

        function blogger($url, $user_id = null, $password = null) {
            if(!preg_match('/^(http|https)/i',$url)) $url = 'http://'.$url;
            $this->url = $url;
            $this->user_id = $user_id;
            $this->password = $password;
        }

        function getUsersBlogs() {
            $oXmlParser = new XmlParser();

            $input = sprintf(
                '<?xml version="1.0" encoding="utf-8" ?><methodCall><methodName>blogger.getUsersBlogs</methodName><params><param><value><string>%s</string></value></param><param><value><string>%s</string></value></param><param><value><string>%s</string></value></param></params></methodCall>',
                '0123456789ABCDEF',
                $this->user_id,
                $this->password
            );
            $output = $this->_request($this->url, $input, 'application/octet-stream','POST');

            $xmlDoc = $oXmlParser->parse($output);

            if(isset($xmlDoc->methodresponse->fault)) {
                $code = $xmlDoc->methodresponse->fault->value->struct->member[0]->value->int->body;
                $message = $xmlDoc->methodresponse->fault->value->struct->member[1]->value->string->body;
                return new Object($code, $message);
            }

            $val = $xmlDoc->methodresponse->params->param->value->array->data->value->struct->member;
            $output = new Object();
            $output->add('url', $val[0]->value->string->body?$val[0]->value->string->body:$val[0]->value->body);
            $output->add('blogid', $blogid = $val[1]->value->string->body?$val[1]->value->string->body:$val[1]->value->body);
            $output->add('name', $val[2]->value->string->body?$val[2]->value->string->body:$val[2]->value->body);
            return $output;
        }

        function getCategories() {
            return array();
        }

        function newMediaObject($filename, $source_file) {
            return new Object();
        }

        function newPost($oDocument, $category = null) {
            $oXmlParser = new XmlParser();

            $output = $this->getUsersBlogs();
            if(!$output->toBool()) return $output;
            $this->blogid = $output->get('blogid');

            $input = sprintf(
                '<?xml version="1.0"?>'.
                '<methodcall>'.
                '<methodname>blogger.newPost</methodname>'.
                '<params>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s<string></value></param>'.
                '<param><value><boolean>1</boolean></value></param>'.
                '</params>'.
                '</methodcall>',
                '0123456789ABCDEF',
                $this->blogid,
                $this->user_id,
                $this->password,
                str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$oDocument->get('content'))
            );

            $output = $this->_request($this->url, $input,  'application/octet-stream','POST');
            $xmlDoc = $oXmlParser->parse($output);

            if(isset($xmlDoc->methodresponse->fault)) {
                $code = $xmlDoc->methodresponse->fault->value->struct->member[0]->value->int->body;
                $message = $xmlDoc->methodresponse->fault->value->struct->member[1]->value->string->body;
                return new Object($code, $message);
            }
            $postid = $xmlDoc->methodresponse->params->param->value->string->body;
            if(!$postid) $postid = $xmlDoc->methodresponse->params->param->value->i4->body;
            $output = new Object();
            $output->add('postid', $postid);
            return $output;
        }

        function editPost($postid, $oDocument, $category = null) {
            $oXmlParser = new XmlParser();

            $output = $this->getUsersBlogs();
            if(!$output->toBool()) return $output;
            $this->blogid = $output->get('blogid');

            $input = sprintf(
                '<?xml version="1.0"?>'.
                '<methodcall>'.
                '<methodname>blogger.editPost</methodname>'.
                '<params>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s</string></value></param>'.
                '<param><value><string>%s<string></value></param>'.
                '<param><value><boolean>1</boolean></value></param>'.
                '</params>'.
                '</methodcall>',
                '0123456789ABCDEF',
                $postid,
                $this->user_id,
                $this->password,
                str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$oDocument->get('content'))
            );

            $output = $this->_request($this->url, $input,  'application/octet-stream','POST');
            $xmlDoc = $oXmlParser->parse($output);

            if(isset($xmlDoc->methodresponse->fault)) {
                $code = $xmlDoc->methodresponse->fault->value->struct->member[0]->value->int->body;
                $message = $xmlDoc->methodresponse->fault->value->struct->member[1]->value->string->body;
                return new Object($code, $message);
            }
            $output = new Object();
            $output->add('postid', $postid);
            return $output;
        }


        function _request($url, $body = null, $content_type = 'text/html', $method='GET', $headers = array(), $cookies = array()) {
            set_include_path(_XE_PATH_."libs/PEAR");
            require_once('PEAR.php');
            require_once('HTTP/Request.php');

            $url_info = parse_url($url);
            $host = $url_info['host'];

            if(__PROXY_SERVER__!==null) {
                $oRequest = new HTTP_Request(__PROXY_SERVER__);
                $oRequest->setMethod('POST');
                $oRequest->addPostData('arg', serialize(array('Destination'=>$url, 'method'=>$method, 'body'=>$body, 'content_type'=>$content_type, "headers"=>$headers)));
            } else {
                $oRequest = new HTTP_Request($url);
                if(count($headers)) {
                    foreach($headers as $key => $val) {
                        $oRequest->addHeader($key, $val);
                    }
                }
                if($cookies[$host]) {
                    foreach($cookies[$host] as $key => $val) {
                        $oRequest->addCookie($key, $val);
                    }
                }
                if(!$content_type) $oRequest->addHeader('Content-Type', 'text/html');
                else $oRequest->addHeader('Content-Type', $content_type);
                $oRequest->setMethod($method);
                if($body) $oRequest->setBody($body);
            }

            $oResponse = $oRequest->sendRequest();

            $code = $oRequest->getResponseCode();
            $header = $oRequest->getResponseHeader();
            $response = $oRequest->getResponseBody();
            if($c = $oRequest->getResponseCookies()) {
                foreach($c as $k => $v) {
                    $cookies[$host][$v['name']] = $v['value'];
                }
            }
            if($code > 300 && $code < 399 && $header['location']) {
                return $this->_request($header['location'], $body, $content_type, $method, $headers, $cookies);
            }

            if($code != 200) return;

            return $response;
        }

    }
?>
