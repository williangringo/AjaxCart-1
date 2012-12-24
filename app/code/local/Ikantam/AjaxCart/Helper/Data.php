<?php

class Ikantam_AjaxCart_Helper_Data extends Mage_Core_Helper_Abstract
{


    public function prepareResponse( $response )
    {
        $responseAjax =  Mage::getSingleton('iajaxcart/response');
        $this->_prepareUpdatedBlocks($responseAjax);
        $this->_returnResult($response, $responseAjax->toArray());
    }

    private function _prepareUpdatedBlocks( $response )
    {
        $blocks = Mage::getModel('iajaxcart/config')->getBlockNames();
        $layout = Mage::app()->getLayout();
        foreach ($blocks as $id => $name){
            $block = $layout->getBlock($name);
            if($block){
                $responseBlock = new Ikantam_AjaxCart_Model_Response_Block();
                $responseBlock->setId($id);
                $responseBlock->setContent($block->toHtml());
                $response->addBlock($responseBlock);
            }
        }
    }

    private function _returnResult($response, $result)
    {
        $body = Zend_Json_Encoder::encode($result);
        $response->setBody($body)->setHeader('Content-Type', 'application/json');
        return;
    }

}