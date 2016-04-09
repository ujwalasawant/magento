<?php

/**
 * Magecheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magecheckout.com license that is
 * available through the world-wide-web at this URL:
 * http://wiki.magecheckout.com/general/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @copyright   Copyright (c) 2015 Magecheckout (http://www.magecheckout.com/)
 * @license     http://wiki.magecheckout.com/general/license.html
 */
class Magecheckout_SecuredCheckout_Block_Adminhtml_System_Config_Form_Field_Notification
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * renderer notification config callback api
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId      = $element->getHtmlId();
        $html        = '<tr id="row_' . $htmlId . '">'
            . '<td class="label" colspan="3">';

        $marginTop   = $fieldConfig->margin_top ? (string)$fieldConfig->margin_top : '0px';
        $customStyle = $fieldConfig->style ? (string)$fieldConfig->style : '';

        $html .= '<ul style="margin-top: ' . $marginTop
            . '" class="messages'
            . $customStyle . '">';
        $html .= '<li class="notice-msg">' . $element->getLabel() . '</li>';
        $html .= '</ul></td></tr>';

        return $html;
    }
}
