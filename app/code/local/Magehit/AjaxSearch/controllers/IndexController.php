<?php
class Magehit_AjaxSearch_IndexController  extends Mage_Core_Controller_Front_Action
{
    private function _sendJson(array $data = array())
    {
        @header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }

    private function _trim($text, $len, $delim = '...')
    {
        if (function_exists("mb_strstr")) {
            $strlen = 'mb_strlen';
            $strpos = 'mb_strpos';
            $substr = 'mb_substr';
        } else {
            $strlen = 'strlen';
            $strpos = 'strpos';
            $substr = 'substr';
        }

        if ($strlen($text) > $len) {
            $whitespaceposition = $strpos($text, " ", $len) - 1;
            if($whitespaceposition > 0) {
                $text = $substr($text, 0, ($whitespaceposition + 1));
            }
            return $text . $delim;
        }
        return $text;
    }

    protected function _getProductCollection($query, $store, Mage_Catalog_Model_Category $category = null)
    {
        if (class_exists('Mage_CatalogSearch_Model_Resource_Search_Collection')) {
            $collection = Mage::getResourceModel('ajaxsearch/product_collection');
            /* @var $collection Magehit_AjaxSearch_Model_Mysql4_Product_Collection */
            $collection->addSearchFilter($query);
        } else {
            $collection = Mage::getResourceModel('ajaxsearch/collection')
                ->getProductCollection($query);
            /* @var $collection Magehit_AjaxSearch_Model_Mysql4_Collection */
        }
        $collection->addStoreFilter($store)
            ->addUrlRewrite()
            ->addAttributeToSort(
                Mage::getStoreConfig('ajax_search/general/sortby'),
                Mage::getStoreConfig('ajax_search/general/sortorder')
            )
            ->setPageSize(Mage::getStoreConfig('ajax_search/general/productstoshow'));

        if (null !== $category) {
            $collection->addCategoryFilter($category);
        }

        Mage::getSingleton('catalog/product_status')
            ->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSearchFilterToCollection($collection);

        $collection->load();
        return $collection;
    }

    protected function _getCategoryCollection($query, $store)
    {
        return Mage::getResourceModel('ajaxsearch/collection')
            ->getCategoryCollection($query, $store);
    }

    protected function _getCmsCollection($query, $store)
    {
        return Mage::getResourceModel('ajaxsearch/collection')
                ->getCmsCollection($query, $store);
    }

    public function indexAction()
    {
        $query = $this->getRequest()->getParam('query', '');
        $_query = Mage::helper('core')->removeTags($query);
        $store = (int)Mage::app()->getStore()->getStoreId();

        $searchURL = Mage::helper('catalogsearch/data')->getResultUrl($query);

        $category = null;
        $suggestions = array();
		
		if(Mage::getStoreConfig('ajax_search/general/enableheadertext')){
        $suggestions[] = array('html' =>
            '<p class="headerajaxsearchwindow">' .
                Mage::getStoreConfig('ajax_search/general/headertext') .
                " <a href='{$searchURL}'>{$_query}</a>" .
            '</p>'
        );
		}

        $suggestions[] = array('html' =>
            '<p class="headercategorysearch">' . $this->__("Products") . '</p>'
        );

        $isEnabledImage = Mage::getStoreConfig('ajax_search/general/enableimage');
        $imageHeight    = Mage::getStoreConfig('ajax_search/general/imageheight');
        $imageWidth     = Mage::getStoreConfig('ajax_search/general/imagewidth');

        $isEnabledDescription = Mage::getStoreConfig('ajax_search/general/enabledescription');
        $lengthDescription = (int) Mage::getStoreConfig('ajax_search/general/descriptionchars');

        $category   = null;
        $categoryId = $this->getRequest()->getParam('category', '');
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if (!$category->getId()) {
                $category = null;
            }
        }

        $collection = $this->_getProductCollection($query, $store, $category);
		if(!$collection->count()){
			$suggestions[] = array('html' =>
				'<p class="note-msg">' . $this->__("There are no products matching the selection.") . '</p>'
			);
		}
        foreach($collection as $_row) {

            $_product = Mage::getModel('catalog/product')->load($_row->getId());

            $_image = $_description = '';

            if($isEnabledImage) {
                $_image = Mage::helper('catalog/image')->init($_product, 'thumbnail')
                        ->resize($imageWidth, $imageHeight)
                        ->__toString();
            }
            if($isEnabledDescription) {
                $_description = strip_tags($this->_trim(
                    $_product->getShortDescription(), $lengthDescription
                ));
            }

            $suggestions[] = array(
                'name'        => $_product->getName(),
                'url'         => $_product->getProductUrl(),
                'image'       => $_image,
                'description' => $_description
            );
        }

        /*
         *     category search
         */
        if (Mage::getStoreConfig('ajax_search/general/enablecatalog')) {
            $collection = $this->_getCategoryCollection($query, $store);
            if (count($collection)) {
                $suggestions[] = array('html' => '<p class="headercategorysearch">'
                    . $this->__("Categories")
                    . '</p><span class="hr"></span>'
                );
            }
            foreach ($collection as $_row) {
                $category = Mage::getModel("catalog/category")->load($_row['entity_id']);
                $suggestions[] = array(
                    'name' => $_row['name'],
                    'url'  => $category->getUrl()
                );
            }
        }
        /*
         * end category search
         */

        /*
         *     cms search
         */
        if (Mage::getStoreConfig('ajax_search/general/enablecms')) {

            $collection = $this->_getCmsCollection($query, $store);
            if (count($collection)) {
                $suggestions[] = array('html' => '<p class="headercategorysearch">'
                    . $this->__("Info Pages")
                    . '</p><span class="hr"></span>'
                );
            }
            foreach ($collection as $_page) {
                $suggestions[] = array(
                    'name' => $_page['title'],
                    'url'  => Mage::getBaseUrl() . $_page['identifier']
                );
            }
        }
        /*
         * end cms search
         */
        if (1 < count($suggestions)) {
			if(Mage::getStoreConfig('ajax_search/general/enablefootertext')){
            $suggestions[] = array('html' =>
                '<p class="headerajaxsearchwindow">' .
                    Mage::getStoreConfig('ajax_search/general/footertext') .
                " <a href='{$searchURL}'>{$_query}</a>" .
                '</p>'
            );
			}
        }

        $this->_sendJson(array(
            'query'       => $query,
            'category'    => $categoryId ? $categoryId : '',
            'suggestions' => $suggestions
        ));
    }
}
