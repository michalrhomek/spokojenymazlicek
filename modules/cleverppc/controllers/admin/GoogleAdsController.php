<?php
/**

* NOTICE OF LICENSE

*

* This file is licenced under the Software License Agreement.

* With the purchase or the installation of the software in your application

* you accept the licence agreement.

*

* You must not modify, adapt or create derivative works of this source code

*

*  @author    Carlos García Vega

*  @copyright 2010-2018 CleverPPC

*  @license   LICENSE.txt

*/

class GoogleAdsController extends ModuleAdminController
{
    public function init()
    {

        parent::init();
        Tools::redirect($this->module->iframe);
    }
}
