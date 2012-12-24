<?php
/**
 * Ikantam
 *
 * @category    Ikantam
 * @package     Ikantam_AjaxCart
 * @copyright   Copyright (c) 2012 Ikantam LLC. (http://www.ikantam.com)
 */

/**
 * Response update block
 */
class Ikantam_AjaxCart_Model_Response_Block
{
	private $_id;
	private $_content;
	
	public function setId($id)
	{
		$this->_id = $id;
	}
	
	public function setContent($content)
	{
		$this->_content = $content;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function getContent()
	{
		return $this->_content;
	}	
	
	
}