<?php



class Ikantam_AjaxCart_Block_Js_Init extends Mage_Core_Block_Template
{
	private $_selector;
	
	public function _construct()
	{
		$this->_selector = new Ikantam_AjaxCart_Model_Frontend_Selector();
		parent::_construct();		
	}
	
	public function getSelector()
	{
		return $this->_selector;
	}
	
	public function wrapInQuotes( $str )
	{
		return "'" . $str . "'";
	}
	
	
	
}