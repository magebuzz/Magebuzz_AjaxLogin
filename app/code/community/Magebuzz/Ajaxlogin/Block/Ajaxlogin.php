<?php
/*
 * @copyright   Copyright ( c) 2014 www.magebuzz.com
 */
class Magebuzz_Ajaxlogin_Block_Ajaxlogin extends Mage_Core_Block_Template
{
	public function _prepareLayout() {
		return parent::_prepareLayout();
  }    
  public function getAjaxlogin() { 
		if (!$this->hasData('ajaxlogin')) {
				$this->setData('ajaxlogin', Mage::registry('ajaxlogin'));
		}
		return $this->getData('ajaxlogin');
	}
}