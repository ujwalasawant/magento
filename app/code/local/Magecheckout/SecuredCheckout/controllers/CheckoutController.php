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
class Magecheckout_SecuredCheckout_CheckoutController extends Mage_Checkout_Controller_Action
{

    protected $_result = array(
        'success'  => true,
        'messages' => array(),
        'blocks'   => array(),
    );

    /**
     * Predispatch: should set layout area
     *
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        /*Ref Mage_Checkout_OnepageController class*/
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $quote           = Mage::getSingleton('checkout/session')->getQuote();
        $isMultiShipping = $quote->getIsMultiShipping();
        if ($isMultiShipping) {
            $quote->setIsMultiShipping(false);
            $quote->removeAllAddresses();
        }

        if (!$this->_canShowForUnregisteredUsers()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);

            return;
        }

        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     *
     */
    protected function _expireAjax()
    {
        if (!$this->getRequest()->isAjax()) {
            $router = Mage::helper('securedcheckout/config')->getRouterName();
            $this->getResponse()->setRedirect(Mage::getUrl($router, array('_secure' => true)));

            return true;
        }
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()
        ) {
            $this->_ajaxRedirectResponse();

            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))
        ) {
            $this->_ajaxRedirectResponse();

            return true;
        }

        return false;
    }

    public function bodyResponse($result = array('success' => true))
    {
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * customer login
     */
    public function loginAction()
    {
        $session = Mage::getSingleton('customer/session');

        if ($this->_expireAjax() || $session->isLoggedIn()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $login  = $this->getRequest()->getParam('login');
            $result = $this->_processLogin($login, $session);
        }
        $this->bodyResponse($result);
    }


    /**
     * Loss pass
     */
    public function forgotPasswordAction()
    {

        $session = Mage::getSingleton('customer/session');
        if ($this->_expireAjax() || $session->isLoggedIn()) return;
        $result = array('success' => false, 'messages' => array());
        $email  = $this->getRequest()->getPost('email');
        if (!empty($email) AND $this->isValidEmail($email)) {
            $formId       = 'user_forgotpassword';
            $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
            if ($captchaModel->isRequired()) {
                if (!$captchaModel->isCorrect($this->_getCaptchaString($this->getRequest(), $formId))) {
                    $result = array(
                        'success' => false,
                        'error'   => Mage::helper('captcha')->__('Incorrect CAPTCHA.'),
                        'captcha' => 'user_forgotpassword'
                    );
                    $this->bodyResponse($result);

                    return;
                }
            }
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {

                try {
                    $customerHelper = Mage::helper('customer');
                    if (method_exists($customerHelper, 'generateResetPasswordLinkToken')) {
                        $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                        $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                        $customer->sendPasswordResetConfirmationEmail();
                    } else {
                        // 1.6.0.x and earlier
                        $newPassword = $customer->generatePassword();
                        $customer->changePassword($newPassword, false);
                        $customer->sendPasswordReminderEmail();
                    }
                    $result['message'] = Mage::helper('customer')->__('We have sent a new password to your email.');
                    $result['success'] = true;
                } catch (Exception $e) {
                    $result['error'] = $e->getMessage();
                }
            }
            if (!isset($result['message']) && !$result['success'] && !$customer->getId()) {
                $result['error'] = Mage::helper('customer')->__('Invalid email %s', Mage::helper('customer')->htmlEscape($email));
            }

        } else {
            $session->setForgottenEmail($email);
            $result['error'] = Mage::helper('checkout')->__('Please enter valid email address.');

        }

        $this->bodyResponse($result);
    }

    protected function _processLogin($data, $session)
    {
        $result = array(
            'success' => false
        );
        /**
         * reference from Mage_Checkout
         */
        if (!isset($data['username']) || !isset($data['password'])) {
            $result['error'] = Mage::helper('customer')->__('Username and password are required.');

        } else {
            try {
                $session->login($data['username'], $data['password']);
                $result['success'] = true;
                $result['message'] = Mage::helper('customer')->__('Login successfully. Hold tight...');

            } catch (Mage_Core_Exception $e) {
                switch ($e->getCode()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                        $result['error'] = $e->getMessage();
                        break;
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                        $result['error'] = Mage::helper('customer')->__('You should confirm your email address first. <a href="%s">Click here</a> to resend confirmation email.', Mage::helper('customer')->getEmailConfirmationUrl($data['username']));
                        break;
                    default:
                        $result['error'] = $e->getMessage();
                }
                $session->setUsername($data['username']);
            } catch (Exception $e) {
                // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
            }
        }

        return $result;
    }

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @param string        $formId
     * @return string
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);

        return $captchaParams[$formId];
    }

    public function saveFormAction()
    {
        if ($this->getRequest()->isPost()) {
            $newData     = $this->getRequest()->getPost();
            $currentData = is_array($this->getFormData()) ? $this->getFormData() : array();
            $this->setFormData(array_merge($currentData, $newData));
        }
        $this->bodyResponse();
    }

    /**
     * get One Step Checkout Form Data
     *
     * @return array
     */
    public function getFormData()
    {
        return Mage::getSingleton('checkout/session')->getData('securedcheckout_form_values');
    }

    /**
     * Set One Step Checkout Form Data
     *
     * @param $data
     */
    public function setFormData($data)
    {
        Mage::getSingleton('checkout/session')->setData('securedcheckout_form_values', $data);
    }


    /**
     * Save shipping method action response
     *
     * @return json
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = $this->_result;
        if (!$this->getRequest()->isPost()) {
            $result['success']    = false;
            $result['messages'][] = $this->__('Please select a shipping method.');

        } else {

            $data = $this->getRequest()->getPost('shipping_method', false);
            /**
             * Compatible  Storepickup
             * */
            if ($data != 'storepickup_storepickup') {
                Mage::getSingleton('checkout/session')->setData('storepickup_session', array());
            }
            Mage::dispatchEvent(
                'checkout_controller_onepage_save_shipping_method',
                array(
                    'request' => $this->getRequest(),
                    'quote'   => $this->getOnepage()->getQuote()));
            $saveShippingResult = $this->saveShippingMethod($data);
            if (!empty($saveShippingResult) && isset($saveShippingResult['error'])) {
                $result['success'] = false;
                if (isset($saveResult['message']))
                    $result['messages'][] = $saveShippingResult['message'];
            }
            $this->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
            $result['blocks'] = $this->getBlockHelper()->getActionBlocks();

        }
        $this->bodyResponse($result);
    }

    public function saveGiftMessageAction()
    {
        if ($this->getRequest()->isPost()) {
            $newData     = $this->getRequest()->getPost();
            $currentData = is_array($this->getFormData()) ? $this->getFormData() : array();
            $this->setFormData(array_merge($currentData, $newData));
            Mage::dispatchEvent(
                'checkout_controller_onepage_save_shipping_method',
                array(
                    'request' => $this->getRequest(),
                    'quote'   => $this->getOnepage()->getQuote()));
        }
        $this->bodyResponse();
    }

    /**
     * Save Shipping Method Data
     *
     * @param $data
     * @return array
     */
    public function saveShippingMethod($data)
    {
        return $this->getOnepage()->saveShippingMethod($data);
    }


    /**
     * Save Address Action Response
     *
     * @regurn json
     */
    public function saveAddressTriggerAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = $this->_result;
        if ($this->getRequest()->isPost()) {
            $newData     = $this->getRequest()->getPost();
            $currentData = is_array($this->getFormData()) ? $this->getFormData() : array();
            $this->setFormData(array_merge($currentData, $newData));


            $dataBilling       = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($dataBilling['email'])) {
                $dataBilling['email'] = trim($dataBilling['email']);
            }
            if (!isset($dataBilling['country_id'])) {
                $dataBilling['country_id'] = Mage::helper('securedcheckout/config')->getDefaultCountryId();
            }
            $saveBilling    = Mage::helper('securedcheckout/checkout_address')->saveBilling($dataBilling, $customerAddressId);
            $useForShipping = isset($dataBilling['use_for_shipping']) ? $dataBilling['use_for_shipping'] : 0;
            if ($useForShipping == 0) {
                Mage::getSingleton('checkout/session')->setData('same_as_billing', 0);
                $data_shipping                    = $this->getRequest()->getPost('shipping', array());
                $data_shipping['same_as_billing'] = 0;
                $customerAddressId                = $this->getRequest()->getPost('shipping_address_id', false);
                $saveShipping                     = Mage::helper('securedcheckout/checkout_address')->saveShipping($data_shipping, $customerAddressId);
            } else if ($useForShipping == 2) {
                if (Mage::helper('core')->isModuleOutputEnabled('Pook_CollectInStore') && Mage::getStoreConfigFlag('carriers/collectinstore/active')) {
                    $carrier = Mage::getModel('pook_collectinstore/carrier_collectInStore');
                    /* Set shipping address to configured store address... */
                    $data_shipping = array(
                        'address_id'           => null,
                        'firstname'            => $carrier->getConfigData('address_firstname'),
                        'lastname'             => $carrier->getConfigData('address_lastname'),
                        'company'              => $carrier->getConfigData('address_company'),
                        'street'               => array(
                            $carrier->getConfigData('address_line1'),
                            $carrier->getConfigData('address_line2')
                        ),
                        'city'                 => $carrier->getConfigData('address_city'),
                        'region_id'            => 1,
                        'region'               => $carrier->getConfigData('address_region'),
                        'postcode'             => $carrier->getConfigData('address_postcode'),
                        'country_id'           => $carrier->getConfigData('address_country'),
                        'telephone'            => $carrier->getConfigData('address_telephone'),
                        'save_in_address_book' => 0,
                        'same_as_billing'      => 2
                    );

                    $this->getQuote()->setTotalsCollectedFlag(true);
                    $saveShipping = $this->getOnepage()->saveShipping($data_shipping, false);
                    /* Set shipping method to collectinstore... */
                    $method = $carrier->getCode() . '_' . $carrier->getCode();
                    $this->getQuote()->setTotalsCollectedFlag(false);
                    /* Now reset TotalsCollectedFlag so the Shipping/shippingMethod totals are calculated. */
                    $this->getShippingAddress()->setShippingMethod($method);
                    Mage::getSingleton('checkout/session')->setData('same_as_billing', 2);
                }
            } else {
                Mage::getSingleton('checkout/session')->setData('same_as_billing', 1);
            }
            if (isset($saveShipping)) {
                $saveResult = array_merge($saveBilling, $saveShipping);
            } else {
                $saveResult = $saveBilling;
            }

            if (is_array($saveResult) && isset($saveResult['error'])) {
                if (is_array($saveResult['message'])) {
                    $result['messages'] = array_merge($result['messages'], $saveResult['message']);
                } else {
                    $result['messages'] = array_merge($result['messages'], $saveResult['message']);

                }
                $result['success'] = false;
            }

            /**
             * Set Default Shipping Method
             **/
            Mage::helper('securedcheckout/checkout_address')->setDefaultShippingMethod($this->getShippingAddress());
            $this->collectQuote();
            $result['blocks'] = $this->getBlockHelper()->getActionBlocks();
        } else {
            $result['messages'][] = $this->__('Please enter billing address information.');
            $result['success']    = false;
        }
        $this->bodyResponse($result);
    }

    public function collectQuote()
    {
		$this->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
    }

    /**
     * Enabled Grand Total in Place Order Button
     *
     * @return mixed
     */
    protected function _isEnabledGrandTotal()
    {
        return Mage::helper('securedcheckout/config')->showGrandTotal();
    }

    /**
     * get quote Grand Total
     *
     * @return mixed
     */
    public function getGrandTotal()
    {
        return Mage::helper('securedcheckout')->getGrandTotal($this->getOnepage()->getQuote());
    }

    /**
     * Get Checkout Onepage Quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getOnepage()->getQuote();
    }

    /**
     * Get Billing Address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    /**
     * get Shipping Address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }


    /**
     * Apply Coupon Action Response
     *
     * @return json
     */
    public function saveCouponAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if (!$this->getRequest()->isPost()) {
            $this->_ajaxRedirectResponse();

            return;
        }
        $result    = $this->_result;
        $code      = (string)$this->getRequest()->getParam('coupon_code');
        $oldCode   = $this->getQuote()->getCouponCode();
        $isApplied = false;
        $isSuccess = true;
        if (empty($code) && empty($oldCode) && $code !== $oldCode) {
            $isSuccess = false;
        } else {
            try {

                $this->getShippingAddress()->setCollectShippingRates(true);
                $this->getQuote()->setCouponCode(strlen($code) ? $code : '')
                    ->collectTotals()
                    ->save();

                if ($code == $this->getQuote()->getCouponCode()) {

                    $this->getShippingAddress()->setCollectShippingRates(true);
                    $this->getQuote()->setTotalsCollectedFlag(false);
                    $this->getQuote()->collectTotals()->save();

                    Mage::getSingleton('checkout/session')->getMessages(true);

                    /**
                     * validate
                     */
                    if (empty($code)) {
                        $result['messages'][] = $this->__('Coupon has been canceled.');
                        $isApplied            = false;

                    } else {
                        $result['messages'][] = $this->__('Coupon has been applied.');
                        $isApplied            = true;

                    }

                } else {
                    $result['messages'][] = $this->__('Coupon is invalid.');
                    $isSuccess            = false;
                }
                $result['blocks'] = $this->getBlockHelper()->getActionBlocks();
                if ($this->_isEnabledGrandTotal()) {
                    $result['grand_total'] = $this->getGrandTotal();
                }
            } catch (Mage_Core_Exception $e) {
                $result['messages'][] = $e->getMessage();
                $isSuccess            = false;
            } catch (Exception $e) {
                $result['messages'][] = $this->__('Error! cannot apply this coupon.');
                $isSuccess            = false;
            }
        }
        $result['success']        = $isSuccess;
        $result['coupon_applied'] = $isApplied;
        $this->bodyResponse($result);
    }

    /**
     * Save payment method action response
     *
     * @return json
     */
    public function savePaymentAction()
    {
        $isSuccess = false;
        if ($this->_expireAjax()) return;
        $result = $this->_result;
        $data   = $this->getRequest()->getPost('payment');
        if (!$data) {
            $errMsg               = $this->__('Please specify a payment method.');
            $result['messages'][] = $errMsg;
            $isSuccess            = true;
        } else {

            $saveResult = $this->savePaymentData($data);
            if (isset($saveResult['error'])) {
                $result['messages'][] = $saveResult['message'];
                $isSuccess            = false;
            }

            try {
                $this->getQuote()->collectTotals()->save();
                $result['blocks'] = $this->getBlockHelper()->getActionBlocks();
            } catch (Exception $e) {
                $errMsg            = $this->__('Cannot set Payment Method.');
                $result['error'][] = $errMsg;
                $isSuccess         = false;
            }
        }

        $result['success'] = $isSuccess;
        $this->bodyResponse($result);
    }

    public function savePaymentData($data)
    {
        return $this->getOnepage()->savePayment($data);
    }


    /**
     * Save order
     *
     * @return json
     */
    public function saveOrderAction()
    {

        if ($this->_expireAjax()) return;

        /*
       if (version_compare(Mage::getVersion(), '1.8.0.0') >= 0) {
                   if (!$this->_validateFormKey()) {
                       $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                           'success'        => false,
                           'error'          => true,
                           'error_messages' => Mage::helper('securedcheckout')->__('Invalid Form Key. Please refresh the page.')
                       )));

                       return;
                   }
               }
       */


        try {
            if ($this->getRequest()->isPost()) {
                $billingData  = $this->getRequest()->getPost('billing', array());
                $shippingData = $this->getRequest()->getPost('shipping', array());
                $result       = $this->createAccountWhenCheckout($billingData);

                if ($result['success']) {
                    $saveResult = $this->saveBillingShippingAddress($billingData, $shippingData);
                    if (isset($saveResult['error'])) {

                        $saveResult['message'] = is_array($saveResult['message']) ? $saveResult['message'] : array($saveResult['message']);
                        $result['messages']    = array_merge($result['messages'], $saveResult['message']);
                        $result['success']     = false;
                    } else {

                        $postedAgreements = array_keys($this->getRequest()->getPost('one_step_checkout_agreement', array()));
                        if ($diff = array_diff(
                                Mage::helper('securedcheckout/config')->getRequiredAgreementIds(),
                                $postedAgreements
                            )
                            && Mage::helper('securedcheckout/config')->isEnabledTerm()
                        ) {
                            $result['success']    = false;
                            $result['messages'][] = $this->__('You should agree to the terms and conditions first.');
                        } else {
                            if ($data = $this->getRequest()->getPost('payment', false)) {
                                $this->getOnepage()->getQuote()->getPayment()->importData($data);
                            }


                            $this->_saveSessionData();

                            // Authorize.Net
                            if (@class_exists('Mage_Authorizenet_Model_Directpost_Session')) {
                                Mage::getSingleton('authorizenet/directpost_session')->setQuoteId(
                                    $this->getOnepage()->getQuote()->getId()
                                );
                            }
                            /**
                             * 3D Secure
                             */
                            $paypalSecured = $this->paypal3DSecured();
                            if (is_array($paypalSecured) && $paypalSecured['success'] == false) {
                                $this->bodyResponse($paypalSecured);

                                return;
                            }
                            //Sage Pay Suite
                            $paymentRedirectUrl = $this->_sagePay();
                        }
                    }
                }
            } else {
                $result['success'] = false;
            }
        } catch (Exception $e) {
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getQuote(), $e->getMessage());
            $msg                  = $this->__('There was an error processing your order. Please contact us or try again later.');
            $result['messages'][] = $msg;
            $result['messages'][] = $e->getMessage();
            $result['success']    = false;
        }
        $isSuccess = $result['success'];
        if ($isSuccess) {
            $this->getQuote()->save();
            if ($paymentRedirectUrl) {
                $result['redirect'] = $paymentRedirectUrl;
            }
        }

        $this->bodyResponse($result);
    }

    /**
     * Paypal 3D secured
     *
     * @return mixed
     */
    public function paypal3DSecured()
    {
        $method = $this->getOnepage()->getQuote()->getPayment()->getMethodInstance();
        $result = $this->_result;
        if ($method->getIsCentinelValidationEnabled()) {
            $centinel = $method->getCentinelValidator();
            if ($centinel && $centinel->shouldAuthenticate()) {
                $layout = $this->getLayout();
                $update = $layout->getUpdate();
                $update->load('securedcheckout_index_saveorder');
                $this->_initLayoutMessages('checkout/session');
                $layout->generateXml();
                $layout->generateBlocks();
                $html                     = $layout->getBlock('centinel.frame')->toHtml();
                $result['is_centinel']    = true;
                $result['update_section'] = array(
                    'name' => 'paypaliframe',
                    'html' => $html
                );
                $result['success']        = false;
            }
        }

        return $result;
    }


    /**
     * 3D Secure, Sage Pay Suite
     */
    protected function _sagePay()
    {
        $paymentHelper = Mage::helper('securedcheckout/checkout_payment');
        $paymentMethod = $this->getQuote()->getPayment()->getMethod();
        if ($paymentHelper->isSagePaySuiteMethod($paymentMethod)) {
            $paymentRedirectUrl = $this->_sagePaySuiteProcess($this->getQuote()->getPayment()->getMethod());
        } else {
            $paymentRedirectUrl = $this
                ->getQuote()
                ->getPayment()
                ->getCheckoutRedirectUrl();
            if (!$paymentRedirectUrl) {
                $this->getOnepage()->saveOrder();
                /**
                 * Compatible with Authorizenet
                 */
                if ($paymentMethod == 'authorizenet_directpost') {
                    $directPost      = Mage::helper('securedcheckout/checkout_payment_authorizenet_directpost');
                    $directPostError = $directPost->process(
                        $this->getRequest()->getPost('payment', false)
                    );
                    if ($directPostError) {
                        throw new Exception($directPostError);
                    }
                }
                $paymentRedirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            }
        }

        return $paymentRedirectUrl;
    }


    public function saveBillingShippingAddress($billingData, $shippingData)
    {
        $addressId = $this->getRequest()->getPost('billing_address_id', false);
        if (isset($billingData['email'])) {
            $billingData['email'] = trim($billingData['email']);
        }
        $saveBilling = $this->getOnepage()->saveBilling($billingData, $addressId);

        // Save shipping address
        if (!isset($billingData['use_for_shipping'])) {
            $addressId    = $this->getRequest()->getPost('shipping_address_id', false);
            $saveShipping = $this->getOnepage()->saveShipping($shippingData, $addressId);
        }
        if (isset($saveShipping)) {
            $saveResult = array_merge($saveBilling, $saveShipping);
        } else {
            $saveResult = $saveBilling;
        }

        return $saveResult;
    }

    /**
     * Create accont when customer checkout
     *
     * @return array
     */
    public function createAccountWhenCheckout($billingData)
    {
        $result = array(
            'success'  => true,
            'messages' => array(),
        );
        if (!$this->getOnepage()->getCustomerSession()->isLoggedIn()) {
            if (isset($billingData['create_account'])) {
                $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
            } else {
                $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_GUEST);
            }
        }

        if (!$this->getQuote()->getCustomerId() &&
            Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()
        ) {
            if ($this->_customerEmailExists($billingData['email'], Mage::app()->getWebsite()->getId())) {
                $result['success']    = false;
                $result['messages'][] = $this->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
            }
        }

        return $result;
    }


    /**
     * @return Magecheckout_SecuredCheckout_Model_Updater
     */
    public function getBlockHelper()
    {
        return Mage::helper('securedcheckout/block');
    }

    /**
     * @reference Mage_Checkout_OnepageController
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }


    /**
     * @reference Mage_Checkout_OnepageController
     * @return boolean
     */
    protected function _canShowForUnregisteredUsers()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn()
        || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())
        || !Mage::helper('checkout')->isCustomerMustBeLogged();
    }


    /**
     * @reference Mage_Checkout_OnepageController
     * @param string $email
     * @param int    $websiteId
     * @return false|Mage_Customer_Model_Customer
     */
    protected function _customerEmailExists($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    protected function _sagePaySuiteProcess($method)
    {

        if ($method == 'sagepaypaypal')
            return Mage::getModel('core/url')->addSessionParam()->getUrl('sgps/paypalexpress/go', array('_secure' => true));


        $paymentMethods = array(
            'sagepaydirectpro' => 'directPayment',
            'sagepayform'      => 'formPayment',
            'sagepayserver'    => 'serverPayment',
        );
        if (array_key_exists($method, $paymentMethods)) {
            $this->_forward('saveOrder', $paymentMethods[$method], 'sgps', $this->getRequest()->getParams());
        }

        return null;

    }

    /**
     *
     */
    public function ajaxCartItemAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $action = $this->getRequest()->getParam('action');
        $id     = (int)$this->getRequest()->getParam('id');
        switch ($action) {
            case 'plus':
            case 'minus':
            case 'update':
                $this->_updateCartItem($action, $id);
                break;
            default:
                $this->_removeCartItem($id);
        }
    }

    /**
     * @param $action
     * @param $id
     */
    protected function _updateCartItem($action, $id)
    {
        $cart      = $this->_getCart();
        $quoteItem = $cart->getQuote()->getItemById($id);
        $qty       = $quoteItem->getQty();
        $result    = array();
        if ($id) {
            try {
                if (isset($qty)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $qty    = $filter->filter($qty);
                }
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }
                if ($action == 'update') {
                    $qty = $this->getRequest()->getParam('qty');
                } else if ($action == 'plus')
                    $qty++;
                else $qty--;
                if ($qty == 0) {
                    $cart->removeItem($id);
                } else {
                    $quoteItem->setQty($qty)->save();
                }
                $this->_getCart()->save();
                $message = $cart->getQuote()->getMessages();
                if ($message) {
                    $result['error']   = $message['qty']->getCode();
                    $result['success'] = 0;
                    $quoteItem->setQty($qty - 1)->save();
                    $this->_getCart()->save();
                }
                if (!$quoteItem->getHasError()) {
                    $result['success'] = 1;
                } else {
                    $result['success'] = 0;
                }
            } catch (Mage_Core_Exception $e) {
                $result['success'] = 0;
                $result['error']   = Mage::helper('core')->escapeHtml($e->getMessage());
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error']   = $this->__('Can not save item.');
            }
            if (array_key_exists('error', $result)) {
                $result['success'] = 0;
                $this->getResponse()->setHeader('Content-type', 'application/json');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            } else {
                $this->_updateOrderReview();
            }
        }
    }

    /**
     * @param $id
     */
    protected function _removeCartItem($id)
    {
        $result = array();
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();
                $result['qty']     = $this->_getCart()->getSummaryQty();
                $result['success'] = 1;
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error']   = $e->getMessage();
            }
            if (array_key_exists('error', $result)) {
                $this->getResponse()->setHeader('Content-type', 'application/json');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            } else {
                $this->_updateOrderReview();
            }
        }
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _updateOrderReview()
    {
        if ($this->_expireAjax()) return;

        $result = array(
            'success'     => true,
            'messages'    => array(),
            'blocks'      => array(),
            'grand_total' => ''
        );
        try {
            if ($this->getRequest()->isPost()) {
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $result['blocks'] = $this->getBlockHelper()->getActionBlocks();
                if ($this->_isEnabledGrandTotal()) {
                    $result['grand_total'] = $this->getGrandTotal();
                }
            } else {
                $result['success']    = false;
                $result['messages'][] = $this->__('Please specify a payment method.');
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $result['success'] = false;
            $result['error'][] = $this->__('Unable to update cart item');
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     *
     */
    public function addGiftWrapAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $is_used_giftwrap = $this->getRequest()->getParam('is_used_giftwrap', false);
        if ($is_used_giftwrap) {
            Mage::getSingleton('checkout/session')->setData('is_used_giftwrap', 1);
        } else {
            Mage::getSingleton('checkout/session')->setData('is_used_giftwrap', 0);
        }
        $this->_updateOrderReview();
    }

    /**
     * Support reward points
     */
    public function applyRewardPointsAction()
    {
        if ($this->_expireAjax()) return;

        $session = Mage::getSingleton('checkout/session');
        $session->setData('is_used_point', $this->getRequest()->getParam('is_used_point'));
        $session->setRewardSalesRules(array(
            'rule_id'      => $this->getRequest()->getParam('reward_sales_rule'),
            'point_amount' => $this->getRequest()->getParam('reward_sales_point'),
        ));
        $result = array(
            'success'  => true,
            'messages' => array(),
            'blocks'   => array(),
        );
        try {
            if ($this->getRequest()->isPost()) {
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $result['blocks'] = $this->getBlockHelper()->getActionBlocks();
            } else {
                $result['success']    = false;
                $result['messages'][] = $this->__('Please specify a payment method.');
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $result['success'] = false;
            $result['error'][] = $this->__('Unable to update payment method');
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * save to securedcheckout_order_data
     */
    protected function _saveSessionData()
    {
        Mage::getSingleton('checkout/session')->setData('securedcheckout_order_data',
            array(
                'comments'      => $this->getRequest()->getPost('comments', false),
                'is_subscribed' => $this->getRequest()->getPost('is_subscribed', false),
                'billing'       => $this->getRequest()->getPost('billing', array()),
            )
        );
        Mage::dispatchEvent('securedcheckout_save_order_session_data_after',
            array(
                'request' => $this->getRequest()
            )
        );

    }

    /**
     * @reference Mage_Checkout_OnepageController
     * @return Magecheckout_SecuredCheckout_AjaxController
     */
    protected function _ajaxRedirectResponse()
    {

        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();

        return $this;
    }

    public function isValidEmail($email)
    {
        return Zend_Validate::is($email, 'EmailAddress');
    }


}