<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Ikantam_AjaxCart_IndexController extends Mage_Checkout_CartController
{

    public function addAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();
    	
    	$cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $ajaxResponse->addError('Product don`t available');
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

               
           	if (!$cart->getQuote()->getHasError()){
           		$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                   $ajaxResponse->addMessage($message);
            }
            
        } catch (Mage_Core_Exception $e) {

            $ajaxResponse->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
            
        } catch (Exception $e) {

            $ajaxResponse->addError($this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            
        }
    	
    }

    public function deleteAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();

        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                    ->save();
            } catch (Exception $e) {
                $ajaxResponse->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }
    }
    
    public function updateItemOptionsAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();

        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    $ajaxResponse->addMessage($message);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $ajaxResponse->addError($e->getMessage());
        } catch (Exception $e) {
            $ajaxResponse->addError($this->__('Cannot update the item.'));
            Mage::logException($e);
        }

    }

    /**
     * Get ajax response
     * @return Ikantam_AjaxCart_Model_Response
     */
    private function _getAjaxResponse()
    {
        return Mage::getSingleton('iajaxcart/response');
    }
    
    public function postDispatch()
    {
        $this->loadLayout();
        Mage::helper('iajaxcart')->prepareResponse($this->getResponse());
    	parent::postDispatch();    	
    }

}