<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zsc
 * Date: 23.12.12
 * Time: 3:39
 * To change this template use File | Settings | File Templates.
 */

require_once 'Mage/Catalog/controllers/Product/CompareController.php';

class Ikantam_AjaxCart_CompareController extends Mage_Catalog_Product_CompareController
{

    /**
     * Add item to compare list
     */
    public function addAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();


        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId
            && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())
        ) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $ajaxResponse->addMessage(
                    $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()))
                );
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
            }

            Mage::helper('catalog/product_compare')->calculate();
        }


    }



    /**
     * Remove item from compare list
     */
    public function removeAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();

        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    $ajaxResponse->addMessage(
                        $this->__('The product %s has been removed from comparison list.', $product->getName())
                    );
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                }
            }
        }


    }

    /**
     * Remove all items from comparison list
     */
    public function clearAction()
    {
        $ajaxResponse = $this->_getAjaxResponse();

        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $ajaxResponse->addMessage($this->__('The comparison list was cleared.'));
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
            $ajaxResponse->addError($e->getMessage());
        } catch (Exception $e) {
            $ajaxResponse->addError($this->__('An error occurred while clearing comparison list.'));
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