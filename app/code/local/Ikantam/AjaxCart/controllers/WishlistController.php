<?php

require_once 'Mage/Wishlist/controllers/IndexController.php';

class Ikantam_AjaxCart_WishlistController extends Mage_Wishlist_IndexController
{

    /**
     * Adding new item
     */
    public function addAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $ajaxResponse->addError($this->__('Cannot specify product.'));
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.', $product->getName(), Mage::helper('core')->escapeUrl($referer));
            $ajaxResponse->addMessage($message);
        }
        catch (Mage_Core_Exception $e) {
            $ajaxResponse->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $ajaxResponse->addError($this->__('An error occurred while adding item to wishlist.'));
        }

    }


    /**
     * Remove item
     */
    public function removeAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();

        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);
        if (!$item->getId()) {
            return $this->norouteAction();
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->norouteAction();
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (Mage_Core_Exception $e) {
            $ajaxResponse->addError(
                $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
            );
        } catch(Exception $e) {
            $ajaxResponse->addError(
                $this->__('An error occurred while deleting the item from wishlist.')
            );
        }

        Mage::helper('wishlist')->calculate();

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
