<?php

class Import_Dummy 
{
	private $dummy_file = 'dummy.xml';
	
	private $dummy_folder = 'dummy';
	
	private $importer_error = false;
	private $response = '';
	
	public function run()
	{
		if( $this->isParentClassExist() )
		{
			if( $this->isDummyFile())
			{
				defined('IMPORT_DEBUG') || define( 'IMPORT_DEBUG', false );

				$error_level = error_reporting();
				
				if(defined('WP_DEBUG') && WP_DEBUG === false)
				{
					error_reporting(0);
				}
				
				ob_start();
				$result = $this->importFromFile();
				if(is_wp_error($result))
				{
					$this->response('error',  $result->get_error_message());
				}
				else
				{
					$this->importThemeManual();
					$this->markAsImported();
					$this->admin_init();
					
				}
				$data = ob_get_clean();
				
				error_reporting($error_level);
				if(strlen($data))
				{
					$this->response('error', $data);
				}
						
			}
			else
			{
				$this->response('error', "The XML file containing the dummy content is not available or could not be read in <pre>" . get_template_directory() . "/backend/dummy/</pre>");
			}
		}
		else
		{
			$this->response('error', "Import error! try to import dummy content manually from <pre>" . get_template_directory() . "/backend/dummy/</pre>");
		}
		$this->response('success');
	}
	
	private function isParentClassExist()
	{
		if (!class_exists('WP_Importer'))
		{
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if (file_exists($class_wp_importer))
			{
				require_once($class_wp_importer);
				return true;
			}
		}
		return false;
	}

	private function importFromFile()
	{
		$wp_import = new Import_Importer();
		$wp_import->fetch_attachments = false;
		$wp_import->import( $this->getDummyFilePath() );
	}
	
	private function importThemeManual()
	{
		foreach ($this->getThemeImportItems() as $itemClass)
		{
			$itemObj = new $itemClass();
			$itemObj->import();
		}
	}
	
	private function markAsImported()
	{
		update_option(SHORTNAME.'_dummy_install', "completed");
	}
	
	private function getThemeImportItems()
	{
		return array(
					'Import_Theme_Media',
					'Import_Theme_Menus',
					'Import_Theme_Options',
					'Import_Theme_Widgets',
				);
	}
	
	private function getDummyFileName()
	{
		return $this->dummy_file;
	}
	
	private function getDummyDirName()
	{
		return $this->dummy_folder;
	}
	
	private function admin_init()
	{
		add_action( 'admin_init', array($this, 'wordpress_odin_importer_init'));
	}
	
	private function wordpress_odin_importer_init()
	{
		load_plugin_textdomain( 'wordpress-importer', false, get_template_directory(). '/backend/languages' );
		
		$GLOBALS['wp_import'] = new Import_Importer();
		register_importer( 'wordpress', 'WordPress', __('Import <strong>posts, pages, comments, custom fields, categories, and tags</strong> from a WordPress export file.', 'wordpress-importer'), array( $GLOBALS['wp_import'], 'dispatch' ) );
	}
	private function response($status, $data='')
	{
		$response = json_encode(array('status' => $status, 'data' => $data));
		die ($response);
	}


	
//	private function setError()
//	{
//		$this->importer_error = true;
//	}
//	
//	private function isImpoterError()
//	{
//		return $this->importer_error;
//	}
	
	private function isDummyFile()
	{
		return is_file($this->getDummyFilePath());
	}
	
	private function getDummyFilePath()
	{
		return get_template_directory(). "/backend/". $this->getDummyDirName() . DIRECTORY_SEPARATOR. $this->getDummyFileName();
	}
}
?>
