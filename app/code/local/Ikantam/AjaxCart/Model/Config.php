<?php



class Ikantam_AjaxCart_Model_Config
{
	
	private $_blockNames = array(
		'toplinks'			=> 'top.links',
		'cart_sidebar'		=> 'cart_sidebar',
		'wishlist_sidebar'	=> 'wishlist_sidebar',
		'compare_sidebar'	=> 'catalog.compare.sidebar',
        'cart'              => 'checkout.cart'
	);

	
	public function getBlockNames()
	{
		return $this->_blockNames;
	}

    public function getPreloaderImage()
    {
        $imageCustom = Mage::getStoreConfig('iajaxcart_setting/frontend/preloader_img');

        if($imageCustom){
            $imageSrc = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'iajaxcart/' .  $imageCustom;
            return $imageSrc;
        }

        $imageStandartValue = Mage::getStoreConfig('iajaxcart_setting/frontend/preloader_img_standart');

        $path = Mage::getModel('iajaxcart/adminhtml_source_preloader')->getPreloaderPath($imageStandartValue);
        $fullSrc = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)
            . 'frontend/default/default/iajaxcart/images/' . $path ;
        return $fullSrc;

    }
	
}