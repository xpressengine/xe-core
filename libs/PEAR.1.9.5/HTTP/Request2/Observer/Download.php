<?php

/**
 * Download a file to disk instead of buffering it in memory.
 * 
 * Source: https://pear.php.net/manual/en/package.http.http-request2.observers.php
 */
class HTTP_Request2_Observer_Download implements SplObserver
{
	protected $filename;
	protected $fp;

	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	public function update(SplSubject $subject)
	{
		$event = $subject->getLastEvent();

		switch($event['name'])
		{
			case 'receivedHeaders':
				$this->fp = @fopen($this->filename, 'wb');
				if(!$this->fp)
				{
					throw new Exception("Cannot open target file '{$filename}'");
				}
				break;

			case 'receivedBodyPart':
			case 'receivedEncodedBodyPart':
				fwrite($this->fp, $event['data']);
				break;

			case 'receivedBody':
				fclose($this->fp);
		}
	}
}
