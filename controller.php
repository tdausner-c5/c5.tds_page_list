<?php
/**
 * TDS Page List & Date Navigation add-on controller.
 *
 * Concrete\Package\TdsPageList\Block\TdsPageList derived from Concrete\Block\PageList
 * Concrete\Package\TdsPageList\Block\TdsDateNavigation derived from Concrete\Block\DateNavigation
 *
 * Copyright 2018 - TDSystem Beratung & Training - Thomas Dausner (tdausner)
 */
namespace Concrete\Package\TdsPageList;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Routing\Router;
use Concrete\Core\Support\Facade\Route;

class Controller extends \Concrete\Core\Package\Package
{

	protected $pkgHandle = 'tds_page_list';
	protected $appVersionRequired = '8.1.0';
	protected $pkgVersion = '0.9.1';

	public function getPackageDescription()
	{
		return t('Add an enhanced list of pages and an enhanced date navigation to your page');
	}

	public function getPackageName()
	{
		return t('TDS Page List &amp; Date Navigation Enhanced');
	}

 	public function install()
	{
		$pkg = parent::install();

        foreach( [ 'tds_page_list', 'tds_date_navigation' ] as $handle) {
            $blk = BlockType::getByHandle($handle);
            if (!is_object($blk)) {
                BlockType::installBlockType($handle, $pkg);
            }
        }
 	}

 	public function uninstall()
	{
		$pkg = parent::uninstall();
 	}

	public function on_start()
	{
        require_once DIR_APPLICATION . '/src/Form.php';
        /*
         * set route to page preview for form edit
         */
        Route::register('/ccm/' . $this->pkgHandle . '/preview', 'Concrete\Package\TdsPageList\Controller\Preview::preview');
    }
}
