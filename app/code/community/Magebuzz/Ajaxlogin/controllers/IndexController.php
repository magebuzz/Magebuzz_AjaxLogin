<?php
/*
 * @copyright   Copyright ( c) 2014 www.magebuzz.com
 */
class Magebuzz_Ajaxlogin_IndexController extends Mage_Core_Controller_Front_Action {
  public function loginAction(){
    //require_once(Mage::getBaseDir('js').DS.'mage'.DS.'captcha.js');
    $this->loadLayout();
    $this->renderLayout();
  }
  public function forgotpasswordAction() {
    $email = $this->getRequest()->getParam('email');
    
    if ($email) {
      if (!Zend_Validate::is($email, 'EmailAddress')) {
        $this->_getCustomerSession()->setForgottenEmail($email);
        $this->_getCustomerSession()->addError($this->__('Invalid email address.'));
        echo $this->__('Invalid email address.');
        return;
      }
      $customer = Mage::getModel('customer/customer')
      ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
      ->loadByEmail($email);
      if ($customer->getId()) {
        try {
          $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
          $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
          $customer->sendPasswordResetConfirmationEmail();
          echo "okforgot";
          return;
        } catch (Exception $exception) {
          $this->_getCustomerSession()->addError($exception->getMessage());
          echo $this->__('Your request is failed.');
          return;
        }
      }else
      {
        echo $this->__('Email is not exist');
        return;
      }     
    } else {
      echo $this->__('Please enter your email.');            
      return;
    }
  }

  private function _getCustomerSession() {
    return Mage::getSingleton('customer/session');
  }

  public function _loginPostRedirect()
  {
    $session = $this->_getCustomerSession();

    if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
      // Set default URL to redirect customer to
      $session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
      // Redirect customer to the last page visited after logging in
      if ($session->isLoggedIn()) {        
        if (!Mage::getStoreConfig(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
          $referer = $_SERVER['HTTP_REFERER'];
          if ($referer) {                    
            $session->setBeforeAuthUrl($referer);          
          }
        } else if ($session->getAfterAuthUrl()) {
            $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
          }
      } else {
        $session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
      }
    } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('customer')->getLogoutUrl()) {
        $session->setBeforeAuthUrl( $this->_getHelper('customer')->getDashboardUrl());
      } else {
        if (!$session->getAfterAuthUrl()) {
          $session->setAfterAuthUrl($session->getBeforeAuthUrl());
        }
        if ($session->isLoggedIn()) {
          $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
        }
    }
    return $session->getBeforeAuthUrl(true); 
  }

  public function registerpostAction() {
    $data = $this->getRequest()->getParams();   
    $customer = Mage::getSingleton('customer/customer');    
    $customerbyemail = $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
    ->loadByEmail($data['email']);
    if(!$customerbyemail->getId())
    {        
      $customer->setId(null)
      ->setSkipConfirmationIfEmail($data['email'])
      ->setFirstname($data['firstname'])
      ->setLastname($data['lastname'])
      ->setEmail($data['email'])
      ->setPassword($data['password'])
      ->setConfirmation($data['confirmation']);
      if (isset($data['newletter']) && $data['newletter']==1) {
        $customer->setIsSubscribed(1);
      }
      $errors = array();
      $validationCustomer = $customer->validate();
      if (is_array($validationCustomer)) {
        $errors = array_merge($validationCustomer, $errors);
        echo $validationCustomer;
        return;
      }
      $validationResult = true;
      if (true === $validationResult) {
        $customer->save();

        if ((bool)Mage::getStoreConfig('customer/create_account/confirm')==true){
          $customer->sendNewAccountEmail(
          'confirmation',
          $this->_getCustomerSession()->getBeforeAuthUrl(),
          Mage::app()->getStore()->getId()
          );      
          $customerHelper = Mage::helper('customer');          
          echo 'confirmation';  
          return;
        } else {
          $this->_getCustomerSession()->addSuccess(
          $this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
          '. ' .
          $this->__('You will receive welcome email with registration info in a moment.')
          );
          $customer->sendNewAccountEmail();  
          $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);  

          $redirect = $this->_loginPostRedirect();
          echo $redirect;
          return;
        }
      } else {
        if (is_array($errors)) {
          foreach ($errors as $errorMessage) {
            echo $errorMessage;
          }
        }        
        return;
      }
    }else{
      echo $this->__('Email address already exists');
      return;
    }
  }

  public function loginpostAction() {
    $session = $this->_getCustomerSession();
    $login['username'] = $this->getRequest()->getParam('username');     
    $login['password'] = $this->getRequest()->getParam('password');     
    try {
      $session->login($login['username'], $login['password']);
      echo $redirect = $this->_loginPostRedirect(); 
      return;    
    } catch (Mage_Core_Exception $e) {
      switch ($e->getCode()) {
        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
          $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
          echo $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
          return;
          break;
        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
          echo $message = Mage::helper('customer')->__('Invalid Email Address or Password');
          break;
        default:
          echo $message = $e->getMessage();
      }
      $session->setUsername($login['username']);
    }   
    exit();
  }

  protected function _getHelper($path)
  {
    return Mage::helper($path);
  }

  protected function _getApp()
  {
    return Mage::app();
  }

  public function _getModel($path, $arguments = array())
  {
    return Mage::getModel($path, $arguments);
  }
}
