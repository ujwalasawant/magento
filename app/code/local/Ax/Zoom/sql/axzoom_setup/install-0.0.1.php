<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, install-0.0.1.php
*  Copyright: Copyright (c) 2010-2015 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.0.9
*  Date: 2015-12-01
*  Review: 2015-12-01
*  URL: http://www.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=modules&module=magento
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2015 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

//die('AJAX ZOOM module setup');

$installer = $this;
$installer->startSetup();
$installer->run("
				CREATE TABLE IF NOT EXISTS `ajaxzoom360` (`id_360` int(11) NOT NULL AUTO_INCREMENT,  `id_product` int(11) NOT NULL,  `name` varchar(255) NOT NULL,  `num` int(11) NOT NULL DEFAULT '1',  `settings` text NOT NULL,  `status` tinyint(1) NOT NULL DEFAULT '0',  `combinations` text NOT NULL, PRIMARY KEY (`id_360`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
				CREATE TABLE IF NOT EXISTS `ajaxzoom360set` (`id_360set` int(11) NOT NULL AUTO_INCREMENT,  `id_360` int(11) NOT NULL,  `sort_order` int(11) NOT NULL, PRIMARY KEY (`id_360set`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
				CREATE TABLE IF NOT EXISTS `ajaxzoomproducts` (`id_product` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");

$installer->endSetup();

$io = new Varien_Io_File();
foreach (array('360', 'cache', 'zoomgallery', 'zoommap', 'zoomthumb', 'zoomtiles_80', 'tmp') as $folder) {
	$io->checkAndCreateFolder(Mage::getBaseDir() . '/js/axzoom/pic/' . $folder);
}

// download axZm if not exists
if (!file_exists(Mage::getBaseDir() . '/js/axzoom/axZm') && ini_get('allow_url_fopen') ) {
    $remoteFileContents = file_get_contents('http://www.ajax-zoom.com/download.php?ver=latest');
    $localFilePath = Mage::getBaseDir() . '/js/axzoom/pic/tmp/jquery.ajaxZoom_ver_latest.zip';
    
    if ($remoteFileContents !== false){
        file_put_contents($localFilePath, $remoteFileContents);
        
        $zip = new ZipArchive;
        $res = $zip->open($localFilePath);
        $zip->extractTo(Mage::getBaseDir() . '/js/axzoom/pic/tmp/');
        $zip->close();
        
        rename(Mage::getBaseDir() . '/js/axzoom/pic/tmp/axZm', Mage::getBaseDir() . '/js/axzoom/axZm');
    }
}