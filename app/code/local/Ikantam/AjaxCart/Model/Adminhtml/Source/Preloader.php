<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zsc
 * Date: 23.12.12
 * Time: 20:15
 * To change this template use File | Settings | File Templates.
 */
class Ikantam_AjaxCart_Model_Adminhtml_Source_Preloader
{

    private $_defaultPreloaders = array(
        array('path' => 'preloader_1.gif', 'value' => '1', 'label' => 'Ikantam Default'),
        array('path' => 'preloader_3.gif', 'value' => '3', 'label' => 'Circle 1'),
        array('path' => 'preloader_8.gif', 'value' => '8', 'label' => 'Circle 2'),
        array('path' => 'preloader_4.gif', 'value' => '4', 'label' => 'Recycling'),
        array('path' => 'preloader_5.gif', 'value' => '5', 'label' => 'Filled gears'),
        array('path' => 'preloader_6.gif', 'value' => '6', 'label' => 'Book'),
        array('path' => 'preloader_2.gif', 'value' => '2', 'label' => 'Hourglass'),
    );

    public function getPreloaderPath($value)
    {
        foreach($this->_defaultPreloaders as $preloader){
            if($preloader['value'] == $value){
                return $preloader['path'];
            }
        }
        return $this->_defaultPreloaders[0]['path'];
    }


    public function toOptionArray()
    {

        $options = array();

        foreach($this->_defaultPreloaders as $preloader){
            $options[] = array(
                'value' => $preloader['value'],
                'label' => Mage::helper('iajaxcart')->__($preloader['label'])
            );
        }
        return $options;
    }

}
