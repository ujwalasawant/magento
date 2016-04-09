<?php


/**
 * Customer Dynamic attributes Form Block
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 */
class Magecheckout_SecuredCheckout_Block_Form extends Magecheckout_SecuredCheckout_Block_Eav_Form
{
    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'customer_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'customer/form';

}
