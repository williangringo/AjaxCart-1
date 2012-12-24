<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zsc
 * Date: 23.12.12
 * Time: 20:07
 * To change this template use File | Settings | File Templates.
 */
class Ikantam_AjaxCart_Block_Adminhtml_System_Config_Form_Field_Imageselect
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{


    protected function _getPreloadersSrc()
    {
        $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)
            . 'frontend/default/default/iajaxcart/images/' ;
        return $src;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $preloadersSrc = $this->_getPreloadersSrc();

        $previewImgHtml = "<img id='iajaxcart-preview-img' src='' height='100px'/>";
        $jsInit = "
          <script>
            $('iajaxcart_setting_frontend_preloader_img_standart').observe('change', function(e){
                var imgSrc = this.value;
                $('iajaxcart-preview-img').setAttribute('src', '". $preloadersSrc ."' + 'preloader_' + imgSrc + '.gif' );
            });
          </script>
        ";

       $html = $previewImgHtml . parent::_getElementHtml($element) . $jsInit;
       return $html;
    }

}
