<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

class VirtualXMLDisplayHandler
{
	/**
	 * Produce virtualXML compliant content given a module object.\n
	 * @param ModuleObject $oModule the module object
	 * @return string
	 */
	function toDoc(&$oModule)
	{
		$error = $oModule->getError();
		$message = $oModule->getMessage();
		$redirect_url = $oModule->get('redirect_url');
		$request_uri = Context::get('xeRequestURI');
		$request_url = Context::getRequestUri();
		$output = new stdClass();

		if(substr_compare($request_url, '/', -1) !== 0)
		{
			$request_url .= '/';
		}

		if($error === 0)
		{
			if($message != 'success') $output->message = $message;

			$output->url = ($redirect_url) ? $redirect_url : $request_uri;
		}
		else
		{
			if($message != 'fail') $output->message = $message;
		}

		$html = array();
		$html[] = '<!DOCTYPE html><html><head><title>Moved...</title><meta charset="utf-8" /><script>';

		if($output->message)
		{
			$html[] = 'alert(' . json_encode($output->message, JSON_UNESCAPED_SLASHES) . ');';
		}

		if($output->url)
		{
			$url = json_encode(preg_replace('/#(.+)$/i', '', $output->url), JSON_UNESCAPED_SLASHES);
			$html[] = 'var win = (window.opener) ? window.opener : window.parent;';
			$html[] = 'win.location.href = ' . $url;
			$html[] = 'if(window.opener) self.close();';
		}

		$html[] = '</script></head></html>';

		return join(PHP_EOL, $html);
	}

}
/* End of file VirtualXMLDisplayHandler.class.php */
/* Location: ./classes/display/VirtualXMLDisplayHandler.class.php */
