<?php
 class Arty_Customer_IndexController extends Mage_Core_Controller_Front_Action
 {
  protected function _getSession()
     {
         return Mage::getSingleton('customer/session');
     }
      public function _getModel($path, $arguments = array())
     {
         return Mage::getModel($path, $arguments);
     }

public function indexAction()
 {
     $block = $this->getLayout()->createBlock('core/template');

     // Assign your template to it
     // This path is relative to your current theme (eg: rwd/default/template/...)
     $block->setTemplate('customer/accountpage/changepassword.phtml');

     // Render the template to the browser
     echo $block->toHtml();

     $this->renderLayout();
 }
     public function editPostAction()
     {

         if ($this->getRequest()->isPost()) {
             /** @var $customer Mage_Customer_Model_Customer */
             $customer = $this->_getSession()->getCustomer();

             /** @var $customerForm Mage_Customer_Model_Form */
             $customerForm = $this->_getModel('customer/form');
             $customerForm->setFormCode('customer_account_edit')
                 ->setEntity($customer);

             $customerData = $customerForm->extractData($this->getRequest());

             $errors = array();
             $customerErrors = $customerForm->validateData($customerData);
             if ($customerErrors !== true) {
                 $errors = array_merge($customerErrors, $errors);
             } else {
                 $customerForm->compactData($customerData);
                 $errors = array();

                 // If password change was requested then add it to common validation scheme
                 if ($this->getRequest()->getParam('change_password')) {
                     $currPass   = $this->getRequest()->getPost('current_password');
                     $newPass    = $this->getRequest()->getPost('password');
                     $confPass   = $this->getRequest()->getPost('confirmation');

                     $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                     if ( $this->_getHelper('core/string')->strpos($oldPass, ':')) {
                         list($_salt, $salt) = explode(':', $oldPass);
                     } else {
                         $salt = false;
                     }

                     if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                         if (strlen($newPass)) {
                             /**
                              * Set entered password and its confirmation - they
                              * will be validated later to match each other and be of right length
                              */
                             $customer->setPassword($newPass);
                             $customer->setConfirmation($confPass);
                         } else {
                             $errors[] = $this->__('New password field cannot be empty.');
                         }
                     } else {
                         $errors[] = $this->__('Invalid current password');
                     }
                 }

                 // Validate account and compose list of errors if any
                 $customerErrors = $customer->validate();
                 if (is_array($customerErrors)) {
                     $errors = array_merge($errors, $customerErrors);
                 }
             }

             if (!empty($errors)) {
                 $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                 foreach ($errors as $message) {
                     $this->_getSession()->addError($message);
                 }
                 $this->_redirect('custommodule');
                 return $this;
             }

             try {
                 $customer->setConfirmation(null);
                 $customer->save();
                 $this->_getSession()->setCustomer($customer)
                     ->addSuccess($this->__('The account information has been saved.'));

                 $this->_redirect('custommodule');
                 return;
             } catch (Mage_Core_Exception $e) {
                 $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                     ->addError($e->getMessage());
             } catch (Exception $e) {
                 $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                     ->addException($e, $this->__('Cannot save the customer.'));
             }
         }

         $this->_redirect('custommodule');
     }
 }