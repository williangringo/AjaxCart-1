<?php

class Ikantam_AjaxCart_Model_Observer
{
    private $_allowForSaveHandles = array(
        array('checkout','cart','index')
    );


    public function onControllerActionPostDispatch( $observer )
    {

        $request = Mage::app()->getRequest();
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getModel('core/session');

        if( !$request->isAjax() ){
            $session->unsetData('iajaxcart_handles');
        }

        if( !$this->_isSelfModule($request) && $this->_isSaveHandlesAllow($request) ){

            $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
            $session->setData('iajaxcart_handles', $handles);
        }

    }

    public function onControllerActionPreDispatch( $observer )
    {
        $request = Mage::app()->getRequest();
        if( $this->_isSelfModule($request) ){

            /** @var $session Mage_Core_Model_Session */
            $session = Mage::getModel('core/session');
            $handles = $session->getData('iajaxcart_handles');
            if($handles){
                Mage::app()->getLayout()->getUpdate()->addHandle($handles);
            }
        }

    }

    private function _isSelfModule($request)
    {
        return $request->getModuleName() === 'iajaxcart';
    }

    /**
     *
     * @param $request Mage_Core_Controller_Request_Http
     * @return bool true if allow, false if disallow
     */
    private function _isSaveHandlesAllow($request)
    {
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();

        foreach($this->_allowForSaveHandles as $allow){
            if( $allow[0] === $module && $allow[1] === $controller && $allow[2] === $action ){
                return true;
            }
        }
        return false;

    }



}
