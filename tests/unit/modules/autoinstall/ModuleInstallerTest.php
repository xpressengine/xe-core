<?php
require_once _XE_PATH_ . 'classes/file/FileHandler.class.php';
require_once _XE_PATH_ . 'modules/autoinstall/autoinstall.lib.php';

class ModuleInstallerTest extends \Codeception\TestCase\Test
{
	public function testArchivePathValidation()
	{
		$installer = new ModuleInstaller();

		$valid_paths = array(
			'./modules/example/example.class.php',
			'modules/example/conf/info.xml',
			'directory/.../file.php',
		);
		foreach($valid_paths as $path)
		{
			$this->assertTrue($installer->_isSafeArchivePath($path), $path);
		}

		$invalid_paths = array(
			'../../index.php',
			'directory/../../../index.php',
			'..\\..\\index.php',
			'/var/www/index.php',
			'\\\\server\\share\\index.php',
			'C:\\var\\www\\index.php',
			"file.php\0.jpg",
			'',
		);
		foreach($invalid_paths as $path)
		{
			$this->assertFalse($installer->_isSafeArchivePath($path), $path);
		}
	}
}
