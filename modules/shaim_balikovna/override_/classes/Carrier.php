<?php


class Carrier extends CarrierCore
{

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
        $context = Context::getContext();

        $id_carrier = false;
        $id_cart = false;

        // pagenotfound = kompatiblita napriklad s modulem pana Mrozka dm_adminorder
        if ((Tools::getValue('controller') == "orderdetail" || (Tools::getValue('controller') == "pagenotfound" && preg_match("/dm_adminorder/", $_SERVER['PHP_SELF'])) || Tools::getValue('controller') == "AdminPdf") && Tools::getValue('id_order') > 0) { // detail objednávky u zákazníka na FO,
            $tmp = Db::getInstance()->executeS('SELECT `id_carrier`, `id_cart` FROM `' . _DB_PREFIX_ . 'orders` WHERE id_order = ' . (int)Tools::getValue('id_order'));
            $id_carrier = (int)$tmp[0]['id_carrier'];
            $id_cart = (int)$tmp[0]['id_cart'];
        } elseif (Tools::getValue('controller') == 'validation' && isset($context->cart->id_carrier) && !empty($context->cart->id_carrier)) { // validation tam nemuze byt, protoze prave tam se resi to, co chceme
            $id_carrier = (int)$context->cart->id_carrier;
            $id_cart = (int)$context->cart->id;
        }

        if ($id_carrier > 0 && $id_cart > 0) {

            $tmp = DB::getInstance()->ExecuteS("SELECT b.naz_prov, b.adresa FROM `" . _DB_PREFIX_ . "shaim_balikovna_data` as a
             INNER JOIN `" . _DB_PREFIX_ . "shaim_balikovna` as b ON (a.psc = b.psc)
            WHERE a.id_cart = $id_cart;");

            if (isset($tmp[0]['naz_prov']) && !empty($tmp[0]['naz_prov'])) {
                $this->name .= " ({$tmp[0]['naz_prov']}, {$tmp[0]['adresa']})";
            }
        }
    }
}
