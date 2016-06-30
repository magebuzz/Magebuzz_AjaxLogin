<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Ajaxlogin_Model_Observer {
  public function checkCaptchaLoginpost($observer) {
	 
    $formId = 'user_login';
    $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
    if ($captchaModel->isRequired()) {
        $controller = $observer->getControllerAction();
        if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
            echo Mage::helper('captcha')->__('Incorrect CAPTCHA.');
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
    }

    return;
      
	}
	public function checkCaptcharegisterpost($observer) {
	 
    $formId = 'user_create';
    $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
    if ($captchaModel->isRequired()) {
        $controller = $observer->getControllerAction();
        if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
            echo Mage::helper('captcha')->__('Incorrect CAPTCHA.');
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
    }

    return;
      
	}
  public function checkCaptchaforgotpassword($observer) {
	 
    $formId = 'user_forgotpassword';
    $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
    if ($captchaModel->isRequired()) {
        $controller = $observer->getControllerAction();
        if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
            echo Mage::helper('captcha')->__('Incorrect CAPTCHA.');
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
    }

    return;
      
	}
  protected function _getCaptchaString($request, $formId)
    {   
      
        $params = $request->getParams();
        return $params[Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE];
    }
}