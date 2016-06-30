<?php
/*
 * @copyright   Copyright ( c) 2014 www.magebuzz.com
 */
class Magebuzz_Ajaxlogin_Helper_Data extends Mage_Core_Helper_Abstract {
	const XML_PATH_ENABLED  = 'ajaxlogin/general/enable_loginpopup';
	const XML_PATH_INCLUDE_JS  = 'ajaxlogin/general/include_js';
  const XML_PATH_SHOW_NEWSLETTER_SUBSCRIPTION  = 'ajaxlogin/general/show_newsletter_subscription';
  const XML_PATH_SHOW_TERMS_AND_CONDITIONS  = 'ajaxlogin/general/show_terms_and_conditions';
  const XML_PATH_SHOW_POPUP_ON_HOMEPAGE  = 'ajaxlogin/general/enable_showhomepage';
	
	public function isEnabled() {
		$storeId = Mage::app()->getStore()->getId();
		return (int)Mage::getStoreConfig(self::XML_PATH_ENABLED, $storeId);
	}
	
	public function includeJs() {
		$storeId = Mage::app()->getStore()->getId();
		return (int)Mage::getStoreConfig(self::XML_PATH_INCLUDE_JS, $storeId);
	}
	
  public function getLoginUrl() {
    return Mage::getUrl('ajaxlogin/index/login');
  }
	
  public function getAccountUrl() {
    $login = Mage::getSingleton('customer/session')->isLoggedIn() ;
    if ($login) {
      return Mage::getUrl('customer/account');
    }
    return Mage::getUrl('ajaxlogin/index/login');
  }
	
	public function showNewsletterSubscription() {
		$storeId = Mage::app()->getStore()->getId();
	  return (int)Mage::getStoreConfig(self::XML_PATH_SHOW_NEWSLETTER_SUBSCRIPTION, $storeId);
	}
	
  public function showTermsAndConditions() {
		$storeId = Mage::app()->getStore()->getId();
	  return (int)Mage::getStoreConfig(self::XML_PATH_SHOW_TERMS_AND_CONDITIONS, $storeId);
	}
	
  public function showPopupOnHomePage() {
		$storeId = Mage::app()->getStore()->getId();
	  return (int)Mage::getStoreConfig(self::XML_PATH_SHOW_POPUP_ON_HOMEPAGE, $storeId);
	}
}