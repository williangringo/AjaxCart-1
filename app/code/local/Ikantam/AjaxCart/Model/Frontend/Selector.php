<?php



class Ikantam_AjaxCart_Model_Frontend_Selector
{
	// @TODO selectors from db (set in admin panel)
	
	
	private $_selectors;
	
	public function __construct()
	{
		
		$this->_initSelectors();
		
	}
	
	private function _initSelectors()
	{
		$this->_selectors = array();
		
		// cart
		$this->_selectors['cart_sidebar'] = '.block.block-cart';
		
		//wishlist
		$this->_selectors['wishlist_sidebar'] = '.block.block-wishlist';
		
		//compare
		$this->_selectors['compare_sidebar'] = '.block.block-list.block-compare';
		
		// header links 
		$this->_selectors['toplinks'] = '.links';

        	$this->_selectors['cart'] = '.cart';
		
	}
	
	public function getSelector( $blockId )
	{
		if( isset( $this->_selectors[$blockId] ) ){
			return $this->_selectors[$blockId];
		}		
		return null;		
	}
	
	public function getSelectors()
	{
		return $this->_selectors;	
	}
	
	
	
}
