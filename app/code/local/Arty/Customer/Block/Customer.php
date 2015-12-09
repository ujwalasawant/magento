<?

class Arty_Customer_Block_Customer extends Mage_Customer_Block_Account_Dashboard
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')
            ->setTitle(Mage::helper('customer')->__('Address Book'));

        return parent::_prepareLayout();
    }
}