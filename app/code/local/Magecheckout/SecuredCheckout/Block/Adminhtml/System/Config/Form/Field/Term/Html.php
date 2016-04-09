<?php


class Magecheckout_SecuredCheckout_Block_Adminhtml_System_Config_Form_Field_Term_Html extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _getPathToSetting()
    {
        return 'groups[terms_conditions][fields][term_html][value]';
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $config = array(
            'name'     => $this->_getPathToSetting(),
            'html_id'  => $element->getHtmlId(),
            'label'    => 'Content',
            'title'    => 'Content',
            'style'    => 'height:20em;width:550px',
            'required' => true,
            'config'   => $this->_getWysiwygConfig()
        );
        $element->addData($config);

        return $element->getElementHtml();
    }

    protected function _getWysiwygConfig()
    {
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $config->addData(array('hidden' => true, 'enabled' => false));
        $config = $this->_addVariablesButton($config);

        return $config;
    }


    private function _addVariablesButton($config)
    {
        $variablePlugin = null;
        $plugins               = $config->getData('plugins');
        foreach ($plugins as $key => $item) {
            if ($item['name'] === 'magentovariable') {
                $variablePlugin = array(
                    'key'  => $key,
                    'data' => $item
                );
                break;
            }
        }
        if (is_null($variablePlugin)) {
            return $config;
        }

        $options = $variablePlugin['data']['options'];

        $originalUrl = $options['url'];
        $newUrl      = Mage::getUrl('adminhtml/securedcheckout_system_config_ajax/getVariables');
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            $newUrl = Mage::getUrl(
				'adminhtml/securedcheckout_system_config_ajax/getVariables',
                array('_secure' => true)
            );
        }
        $options['url']                = $newUrl;
        $options['onclick']['subject'] = str_replace($originalUrl, $newUrl, $options['onclick']['subject']);

        $variablePlugin['data']['options'] = $options;


        $plugins[$variablePlugin['key']] = $variablePlugin['data'];
        $config->setData('plugins', $plugins);

        return $config;
    }
}

