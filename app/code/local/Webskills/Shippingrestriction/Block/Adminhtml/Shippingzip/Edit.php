<?php
	
class Webskills_Shippingrestriction_Block_Adminhtml_Shippingzip_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "shippingrestriction";
				$this->_controller = "adminhtml_shippingzip";
				$this->_updateButton("save", "label", Mage::helper("shippingrestriction")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("shippingrestriction")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("shippingrestriction")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("shippingzip_data") && Mage::registry("shippingzip_data")->getId() ){

				    return Mage::helper("shippingrestriction")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("shippingzip_data")->getId()));

				} 
				else{

				     return Mage::helper("shippingrestriction")->__("Add Item");

				}
		}
}