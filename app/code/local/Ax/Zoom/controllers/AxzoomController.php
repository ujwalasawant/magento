<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/controllers/IndexController.php
*  Copyright: Copyright (c) 2010-2015 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.0.7
*  Date: 2015-11-15
*  Review: 2015-11-15
*  URL: http://www.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=modules&module=magento
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2015 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/
//Mage_Core_Controller_Front_Action
class Ax_Zoom_AxzoomController extends Mage_Adminhtml_Controller_Action  
{
    public function AddProductImage360Action()
    {
        $productId = Mage::app()->getRequest()->getParam('id_product');
        $id360set = $this->getRequest()->getPost('id_360set');
        $id360 = Mage::getModel('axzoom/ax360set')->load($id360set)->getId_360();
        $folder = $this->createProduct360Folder($productId, $id360set);
        
        if (isset($_FILES['file360']['name'][0]) && $_FILES['file360']['name'][0] != '') {
            try {
                $fileName       = $_FILES['file360']['name'][0];
                $fileExt        = strtolower(substr(strrchr($fileName, '.'), 1));
                $fileNamewoe    = $productId . '_' . $id360set . '_' . $this->imgNameFilter(rtrim($fileName, '.' . $fileExt));
                $fileName       = $fileNamewoe . '.' . $fileExt;

                $uploader       = new Varien_File_Uploader(array(
                    'name'    => $_FILES['file360']['name'][0],
                    'type'    => $_FILES['file360']['type'][0],
                    'tmp_name'    => $_FILES['file360']['tmp_name'][0],
                    'error'    => $_FILES['file360']['error'][0],
                    'size'    => $_FILES['file360']['size'][0]
                    ));
                $uploader->setAllowedExtensions(array('png', 'jpg')); //allowed extensions
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                
                $uploader->save($folder, $fileName );
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }

        die(Mage::helper('core')->jsonEncode(array(
            'file360' => array(array(
            'status' => 'ok',
            'name' => $fileName,
            'id' => $fileNamewoe,
            'id_product' => $productId,
            'id_360' => $id360,
            'id_360set' => $id360set,
            'path' => Mage::getBaseUrl('js') . 'axzoom/axZm/zoomLoad.php?qq=1&azImg=' . $this->rootFolder() . 'js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set . '/' . $fileName . '&width=100&height=100&qual=90'
            )))));
        exit;
    }

    public function DeleteProductImage360Action()
    {
        $get = Mage::app()->getRequest();
        $imageId = $get->getParam('id_image');
        $productId = $get->getParam('id_product');
        $id360set = $get->getParam('id_360set');
        $id360 = Mage::getModel('axzoom/ax360set')->load($id360set)->getId_360();
        $tmp = explode('&', $get->getParam('ext'));
        $ext = reset($tmp);
        $filename = $imageId . '.' . $ext;

        $dst = Mage::getBaseDir() . '/js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set . '/' . $filename;
        unlink($dst);

        Mage::getModel('axzoom/ax360')->deleteImageAZcache($filename);

        die(Mage::helper('core')->jsonEncode(array(
            'status' => 'ok',
            'content' => (object)array('id' => $imageId),
            'confirmations' => array('The image was successfully deleted.')
            )));
    }

    public function ClearAzImageCacheAction()
    {
        $filename = (int)(Mage::app()->getRequest()->getParam('deletedImgID')) . '.jpg';
        Mage::getModel('axzoom/ax360')->deleteImageAZcache($filename);
        die(Mage::helper('core')->jsonEncode(array(
            'status' => 'ok',
            'confirmations' => array('AJAX-ZOOM cache has been deleted for image with ID ' . (int)(Mage::app()->getRequest()->getParam('deletedImgID')))
            )));
    }

    public function GetImagesAction()
    {
        $get = Mage::app()->getRequest();
        $productId =  $get->getParam('id_product');
        $id360set =  $get->getParam('id_360set');
        $images = Mage::getModel('axzoom/ax360')->get360Images($productId, $id360set);

        die(Mage::helper('core')->jsonEncode(array(
            'status' => 'ok',
            'id_product' => $productId,
            'id_360set' => $id360set,
            'images' => $images
            )));
    }

    public function DeleteSetAction()
    {
        $get = Mage::app()->getRequest();
        $productId = $get->getParam('id_product');
        $id360set  = $get->getParam('id_360set');
        $id360     = Mage::getModel('axzoom/ax360set')->load($id360set)->getId_360();

        // clear AZ cache
        $images = Mage::getModel('axzoom/ax360')->get360Images($productId, $id360set);

        foreach ($images as $image) {
            Mage::getModel('axzoom/ax360')->deleteImageAZcache($image['filename']);
        }

        Mage::getModel('axzoom/ax360set')->setId($id360set)->delete();
        
        if (!Mage::getModel('axzoom/ax360set')->getCollection()->addFieldToFilter('id_360', $id360)->getData()) {
            Mage::getModel('axzoom/ax360')->setId($id360)->delete();
        }
        
        $path = Mage::getBaseDir() . '/js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set;
        $this->deleteDirectory($path);

        die(Mage::helper('core')->jsonEncode(array(
            'id_360set' => $id360set,
            'id_360' => $id360,
            'path' => $path,
            'removed' => (!Mage::getModel('axzoom/ax360')->load($id360)->getData() ? 1 : 0),
            'confirmations' => array('The 360 image set was successfully removed.')
            )));
    }

    public function Set360StatusAction()
    {
        $get = Mage::app()->getRequest();
        $productId =   $get->getParam('id_product');
        $id360     =   $get->getParam('id_360');
        $status     =   $get->getParam('status');

        Mage::getModel('axzoom/ax360')->load($id360)->addData(array('status' => $status))->setId($id360)->save();

        die(Mage::helper('core')->jsonEncode(array(
            'status' => 'ok',
            'confirmations' => array('The status has been updated.' . $status . '-' . $id360)
            )));
    }

    public function AddSetAction()
    {
        $get = Mage::app()->getRequest();
        $productId =   $get->getParam('id_product');
        $name       =   $get->getParam('name');
        $existing   =   $get->getParam('existing');
        $zip        =   $get->getParam('zip');
        $delete        =   $get->getParam('delete');
        $arcfile    =   $get->getParam('arcfile');
        $newId = '';
        $newName = '';
        $newSettings = '';
        $status = ($zip == 'true' ? 1 : 0);

        if (!empty($existing)) {
            $id360 = $existing;
            $name = Mage::getModel('axzoom/ax360')->load($id360)->getName();
        } else {
            $newSettings = $settings = '{"position":"first","spinReverse":"true","spinBounce":"false","spinDemoRounds":"3","spinDemoTime":"4500"}';
            $data = array(
                'id_product' => $productId,
                'name' => $name,
                'settings' => $settings,
                'status' =>  $status
                );
            $id360 = $newId = Mage::getModel('axzoom/ax360')->setData($data)->save()->getId();
            $newName = $name;
        }
        
        $id360set = Mage::getModel('axzoom/ax360set')->setData(array('id_360' => $id360, 'sort_order' => 0))->save()->getId();

        $sets = array();
        
        if ($zip == 'true') {
            $sets = $this->addImagesArc($arcfile, $productId, $id360, $id360set, $delete);
        }
                
        die(Mage::helper('core')->jsonEncode(array(
            'status' => $status,
            'name' => $name,
            'path' => Mage::getBaseUrl('js') . 'axzoom/no_image-100x100.jpg',
            'sets' => $sets,
            'id_360' => $id360,
            'id_product' => $productId,
            'id_360set' => $id360set,
            'confirmations' => array('The image set was successfully added.'),
            'new_id' => $newId,
            'new_name' => $newName,
            'new_settings' => urlencode($newSettings)
            )));        
    }


    public function addImagesArc($arcfile, $productId, $id360, $id360set, $delete = '')
    {
        set_time_limit(0);

        $baseDir = Mage::getBaseDir();
        $baseUrlJs = Mage::getBaseUrl('js');

        $path = $baseDir . '/js/axzoom/zip/' . $arcfile;
        $dst = is_dir($path) ? $path : $this->extractArc($path);
        @chmod($dst, 0777);
        $data = $this->getFolderData($dst);
        
        $name = Mage::getModel('axzoom/ax360')->load($id360)->getName();

        $sets = array(array(
                'name' => $name,
                'path' => $baseUrlJs . 'axzoom/axZm/zoomLoad.php?qq=1&azImg360=' . $this->rootFolder() . 'js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set . '&width=100&height=100&thumbMode=contain',
                'id_360set' => $id360set,
                'id_360' => $id360,
                'status' => '1'
                ));

        $move = is_dir($path) ? false : true;

        if (count($data['folders']) == 0) { // files (360)
            $this->copyImages($productId, $id360, $id360set, $dst, $move);
        } elseif (count($data['folders']) == 1) { // 1 folder (360)
            $this->copyImages($productId, $id360, $id360set, $dst . '/' . $data['folders'][0], $move);
        } else { // 3d
            $this->copyImages($productId, $id360, $id360set, $dst . '/' . $data['folders'][0], $move);
            for ($i=1; $i < count($data['folders']); $i++) { 
                $id360set = Mage::getModel('axzoom/ax360set')->setData(array('id_360' => $id360, 'sort_order' => 0))->save()->getId();
                $this->copyImages($productId, $id360, $id360set, $dst . '/' . $data['folders'][$i], $move);

                $sets[] = array(
                    'id_360' => $id360,
                    'name' => $name,
                    'path' => $baseUrlJs . 'axzoom/axZm/zoomLoad.php?qq=1&azImg360=' . $this->rootFolder() . 'js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set . '&width=100&height=100&thumbMode=contain',
                    'id_360set' => $id360set
                    );
            }
        }

        
        // delete temp directory which was created when zip extracted
        if(!is_dir($path)) {
            $this->deleteDirectory($dst);
        }

        // delete the sourece file (zip/dir) if checkbox is checked
        if($delete == 'true') {
            if(is_dir($path)) {
                $this->deleteDirectory($dst);
            } else {
                @unlink($path);
            }
        }        
        return $sets;
    }

    public function extractArc($file)
    {

        $baseDir = Mage::getBaseDir();

        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
            $folder = uniqid(getmypid());
            $path = $baseDir . '/js/axzoom/tmp/' . $folder;
            mkdir($path, 0777);
            $zip->extractTo($path);
            $zip->close();
            return $path; 
        } else {
            return false;
        }
    }

    public function getFolderData($path)
    {
        $files = array();
        $folders = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $entry != '__MACOSX') {
                    if (is_dir($path . '/' . $entry)) {
                        array_push($folders, $entry);
                    } else {
                        array_push($files, $entry);
                    }
                }
            }
            closedir($handle);
        }
        
        sort($folders);
        sort($files);

        return array(
            'folders' => $folders,
            'files' => $files
            );
    }

    public function copyImages($productId, $id360, $id360set, $path, $move)
    {
        $files = $this->getFilesFromFolder($path);
        $folder = $this->createProduct360Folder($productId, $id360set);

        foreach ($files as $file)
        {
            $name = $productId . '_' . $id360set . '_' . $this->imgNameFilter($file);
            $tmp = explode('.', $name);
            $ext = end($tmp);
            $dst = $folder . '/' . $name;

            if($move) {
                if(@!rename($path.'/'.$file, $dst)) {
                    copy($path.'/'.$file, $dst);
                }
            } else {
                copy($path.'/'.$file, $dst);
            }
        }
    }


    public function getFilesFromFolder($path)
    {
        $files = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $entry != '__MACOSX') {
                    $files[] = $entry;
                }
            }
            closedir($handle);
        }
        
        return $files;
    }

    public function createProduct360Folder($productId, $id360set)
    {
        $productId =  (int)($productId);
        $id360set =  (int)($id360set);
        $id360 = Mage::getModel('axzoom/ax360set')->load($id360set)->getId_360();
        
        $imgPath = Mage::getBaseDir() . '/js/axzoom/pic/360/';
        @chmod(rtrim($imgPath, '/'), 0777);

        if (!file_exists($imgPath . '.htaccess')) {
            file_put_contents($imgPath . '.htaccess', 'deny from all');
        }

        if (!file_exists($imgPath . $productId)) {
            mkdir($imgPath . $productId, 0777);
        }

        if (!file_exists($imgPath . $productId . '/' . $id360)) {
            mkdir($imgPath . $productId . '/' . $id360, 0777);
        }

        $folder = $imgPath . $productId . '/' . $id360 . '/' . $id360set;

        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        } else {
            chmod($folder, 0777);
        }

        return $folder;
    }

    public function deleteDirectory($dirname, $delete_self = true)
    {
        if(!isset($dirname) || empty($dirname) || $dirname == '/') {
            return false;
        }

        $dirname = rtrim($dirname, '/') . '/';
        if (file_exists($dirname))
            if ($files = scandir($dirname))
            {
                foreach ($files as $file)
                    if ($file != '.' && $file != '..' && $file != '.svn')
                    {
                        if (is_dir($dirname.$file))
                            $this->deleteDirectory($dirname.$file, true);
                        elseif (file_exists($dirname.$file))
                        {
                            @chmod($dirname.$file, 0777); // NT ?
                            unlink($dirname.$file);
                        }
                    }
                    if ($delete_self && file_exists($dirname))
                        if (!rmdir($dirname))
                        {
                            @chmod($dirname, 0777); // NT ?
                            return false;
                        }
                    return true;
            }
        return false;
    }
    
    public function imgNameFilter($filename)
    {
        $filename = preg_replace('/[^A-Za-z0-9_\.-]/', '-', $filename);
        return $filename;
    }
    
    public function rootFolder()
    {
        return preg_replace('|js/$|', '', parse_url(Mage::getBaseUrl('js'), PHP_URL_PATH));
    }
}