<?php


class Ikantam_AjaxCart_Model_Response
{
	
	private $_errors;
	private $_messages;
	private $_blocks;

    private $_redirect;
	
	public function __construct()
	{
		$this->_errors = array();
		$this->_messages = array();
		$this->_blocks = array();		
	}

    public function setRedirect($redirectUrl)
    {
        $this->_redirect = $redirectUrl;
    }
	
	public function addMessage($message)
	{
		$this->_messages[] = $message;
	}
	
	public function addError($error)
	{
		$this->_errors[] = $error;
	}
	
	public function addBlock($block)
	{
		if( is_array($block) ){
			$blockObj = new Ikantam_AjaxCart_Model_Response_Block();
			$blockObj->setId($block['id']);
			$blockObj->setContent($block['content']);
			$block = $blockObj;
		}
		
		if( !($block instanceof Ikantam_AjaxCart_Model_Response_Block) ){
			throw new Exception("block don't instance of Ikantam_AjaxCart_Model_Response_Block ");
		}
		
		$this->_blocks[] = $block;
		
	}
	
	public function toArray()
	{
		$response = array();


		$response['errors'] = $this->_errors;
		$response['messages'] = $this->_messages;
		$response['blocks'] = array();
		
		/* @var $_block Ikantam_AjaxCart_Response_Block */
		foreach($this->_blocks as $_block){
			$response['blocks'][ $_block->getId() ] = $_block->getContent();
		}

        if($this->_redirect){
            $response['redirect'] = $this->_redirect;
        }
		
		return $response;
	}
	
	
	
	
	
}