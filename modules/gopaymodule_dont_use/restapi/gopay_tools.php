<?php
/** ########################################################################### * 
 *                                                                             * 
 *                      Presta Module Shop | Copyright 2018                    * 
 *                           www.prestamoduleshop.com                          * 
 *                                                                             * 
 *             Please do not change this text, remove the link,                * 
 *          or remove all or any part of the creator copyright notice          * 
 *                                                                             * 
 *    Please also note that although you are allowed to make modifications     * 
 *     for your own personal use, you may not distribute the original or       * 
 *                 the modified code without permission.                       * 
 *                                                                             * 
 *                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 * 
 *             Download is allowed only from www.prestamoduleshop.com          * 
 *                                                                             * 
 *       This software is provided as is, without warranty of any kind.        * 
 *           The author shall not be liable for damages of any kind.           * 
 *               Use of this software indicates that you agree.                * 
 *                                                                             * 
 *                                    ***                                      * 
 *                                                                             * 
 *              Prosím, neměňte tento text, nemažte odkazy,                    * 
 *      neodstraňujte části a nebo celé oznámení těchto autorských práv        * 
 *                                                                             * 
 *     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    * 
 *        pro vlastní osobní potřebu, nesmíte distribuovat původní nebo        * 
 *                        upravený kód bez povolení.                           * 
 *                                                                             * 
 *                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          * 
 *          Stažení je povoleno pouze z www.prestamoduleshop.com               * 
 *                                                                             * 
 *   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    * 
 *          Autor nenese odpovědnost za škody jakéhokoliv druhu.               * 
 *                  Používáním tohoto softwaru znamená,                        * 
 *           že souhlasíte s výše uvedenými autorskými právy.                  * 
 *                                                                             * 
 * ########################################################################### **/
if (!defined('_PS_PRICE_COMPUTE_PRECISION_'))
	define('_PS_PRICE_COMPUTE_PRECISION_', 2);

class PMS_stdObjectes
{
    public function __construct(array $arguments = array()) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                if ($argument instanceOf Closure) {
                    $this->{$property} = $argument;
                } else {
                    $this->{$property} = $argument;
                }
            }
        }
    }

    public function __call($method, $arguments) {
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new Exception("Fatal error: Call to undefined method stdObject::{$method}()");
        }
    }
}

class Pms_GoPay_Extra_Tools
{
    const ROUND_ITEM = 1;
    const ROUND_LINE = 2;
    const ROUND_TOTAL = 3;

	public static function getErrors($error)
	{
		return new PMS_stdObjectes(array(
			"errors" => array(
					new PMS_stdObjectes(array(
									"scope"			 => '',
									"field"			 => '',
									"error_code"	 => isset($error['error_code']) ? $error['error_code'] : '',
									"error_name"	 => isset($error['error_name']) ? $error['error_name'] : '',
									"message"		 => isset($error['message']) ? $error['message'] : '',
									"description"	 => isset($error['description']) ? $error['description'] : ''
					))
			)
		));
	}

	public static function parse_csv($str, $options = null)
	{
		$res = array();
		$delimiter = empty($options['delimiter']) ? ";" : $options['delimiter'];
		$to_object = empty($options['to_object']) ? false : true;
		$lines = explode("\n", $str);
		//pr($lines);
		$field_names = explode($delimiter, array_shift($lines));
		foreach ($lines as $line)
		{
			// Skip the empty line
			if (empty($line)) continue;
			$fields = explode($delimiter, $line);
			$_res = $to_object ? new stdClass : array();
			foreach ($field_names as $key => $f)
			{
				$f = str_replace(
					array(' ', '/', '"', 'ě', 'š', 'ř', 'ž', 'ý', 'á', 'í', 'é', 'ú', 'ů', 'È', 'è', 'ì'),
					array('_', '_', '', 'e', 's', 'r', 'z', 'y', 'a', 'i', 'e', 'u', 'u', 'c', 'c', 'e'),
					utf8_encode(strtolower($f))
				);

				$field = str_replace('"', '', $fields[$key]);
				if ($to_object) {
					$_res->{$f} = $field;
				} else {
					$_res[$f] = $field;
				}
			}

			$res[] = $_res;
		}

		return $res;
	}

	public static function getConfirmations($paymentMessage, $repeatPayment)
	{
		return new PMS_stdObjectes(array(
			"confirms" => array(
					new PMS_stdObjectes(array(
									"paymentMessage"	 => $paymentMessage,
									"repeatPayment"		 => $repeatPayment
					))
			)
		));
	}

	/**
	 * názvy produktů z objednávky
	 **/
	public static function getItems($id, $operand = null)
	{
		$cart		 = false;
		$order		 = new Order($id);
		$carrier	 = new Carrier($order->id_carrier);
		$context	 = Context::getContext();
		$products	 = $order->getProducts();
		$discounts	 = $order->getCartRules();
		$vatRates	 = Pms_GoPay_Extra_Helper::getVatRates();
		$items		 = array();

		if (!Validate::isLoadedObject($order) && !isset($order->id))
		{
			$cart	 = true;
			$order	 = new Cart($id);
			$sumary	 = $order->getSummaryDetails();
			$carrier = new Carrier($sumary['carrier']->id);
			$products = $sumary['products'];
			$discounts = $sumary['discounts'];
		}

		foreach ($products as $key => $item)
		{
			if ($cart)
			{
				$product_url = $context->link->getProductLink($item['id_product'], null, null, null, null, $order->id_shop);
				$ean = $item['ean13'];
				$count = $item['cart_quantity'];
				$name = $item['name'].($item['attributes'] ? ' - '.$item['attributes'] : '');
				$amount = $item['total_wt'];
				if (isset($vatRates[$item['rate']]))
					$vat_rate = $item['rate'];
			} else
			{
				$product_url = $context->link->getProductLink($item['product_id'], null, null, null, null, $order->id_shop);
				$ean = $item['product_ean13'];
				$count = $item['product_quantity'];
				$name = $item['product_name'];
				$amount = $operand.$item['total_wt'];
				if (isset($vatRates[$item['tax_rate']]))
					$vat_rate = $item['tax_rate'];
			}

			$items[] = array(
				'type' => 'ITEM',
				'product_url' => $product_url,
				'ean' => $ean,
				'count' => $count,
				'name' => ''.mb_substr(trim(strip_tags($name)), 0, 255).'',
				'amount' => ''.Tools::ps_round($amount, 2) * 100 .'',
				'vat_rate' => ''.(isset($vatRates[$vat_rate]) ? Tools::ps_round($vat_rate, 0) : 0).''
			);
		}

		foreach ($discounts as $key => $discount)
		{
			if ($cart)
				$amount = $sumary['total_discounts'];
			else
				$amount = $discount['value'];

			if ($amount > 0)
				$items[] = array(
					'type' => 'DISCOUNT',
					'product_url' => '',
					'ean' => '',
					'count' => 1,
					'name' => ''.mb_substr(trim(strip_tags ($discount['name'])), 0, 255).'',
					'amount' => ''.($operand ? '+' : '-').Tools::ps_round($amount, 2) * 100 .'',
					'vat_rate' => 0
				);
		}

		if ($cart)
			$amount = $sumary['total_shipping'];
		else
			$amount = $order->total_shipping;

		$vat_rate = $carrier->getTaxesRate(new Address($order->id_address_delivery));

		if ($amount > 0)
			$items[] = array(
					'type' => 'DELIVERY',
					'product_url' => '',
					'ean' => '',
					'count' => 1,
					'name' => ''.mb_substr(trim(strip_tags ($carrier->name)), 0, 255).'',
					'amount' => ''.$operand.Tools::ps_round($amount, 2) * 100 .'',
					'vat_rate' => ''.isset($vatRates[$vat_rate]) ? $vat_rate : 0 .''
			);
/*
echo '$items<pre>+++';
print_r($items);
echo '</pre>';

exit;*/

		return $items;
	}

	/**
	 * pole pro EET
	 **/
	public static function getEET($id, $total_paid, $operand = null)
	{
		$order		 = new Order($id);
		$context	 = Context::getContext();
		$productTaxes = self::getProductTaxesBreakdown($order);
		$vatRates	 = Pms_GoPay_Extra_Helper::getVatRates();

		/* zatím nebude zprovozněno vytvoření objednávky po zaplacení, nesedí částky DPH
		if (!Validate::isLoadedObject($order) && !isset($order->id))
		{
			$cart	 = new Cart($id);
			$package_list	 = $cart->getPackageList();
 
			foreach ($package_list as $id_address => $packageByAddress)
                foreach ($packageByAddress as $id_package => $package)
                	foreach ($package['product_list'] as $product)
					{
						$tax = number_format ($product['rate'], 3);
						$productTaxes[$tax]['total_price_tax_excl'] = $productTaxes[$tax]['total_price_tax_excl'] + $product['total'];
						$productTaxes[$tax]['total_amount'] = $productTaxes[$tax]['total_amount'] + ($product['total_wt'] - $product['total']);
					}
		}
*/

		$currency = new Currency($order->id_currency);

		$items = array(
				//'dic_poverujiciho' => Configuration::get(GFIX.'_DIC'),
				'celk_trzba' => ''.$operand.$total_paid.'',
				'cest_sluz' => '',
				'urceno_cerp_zuct' => '',
				'cerp_zuct' => '',
				'mena' => ''.$currency->iso_code.''
		);

		foreach ($vatRates as $key => $tax)
		{
			$key = $key.'.000';
			if (!isset($productTaxes[$key]))
			{
				$productTaxes[$key]['total_price_tax_excl'] = 0;
				$productTaxes[$key]['total_amount'] = 0;
			}

			if ($key == $order->carrier_tax_rate)
			{
				$productTaxes[$key]['total_price_tax_excl'] += $order->total_shipping_tax_excl;
				$productTaxes[$key]['total_amount'] += ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl);
			}
		}

		foreach ($productTaxes as $key => $taxes)
		{
			$key = round($key);
			if($key == 0 && !Configuration::get(Pms_GoPay_Extra::$SFIX.'_VAT_OTHER'))
				$items = array_merge($items, array(
					'zakl_nepodl_dph' => ''.$operand.$taxes['total_price_tax_excl'] * 100 .''
				));
			else
			{
				if (isset($vatRates[$key]))
				{
					$items = array_merge($items, array(
						'zakl_dan'.$vatRates[$key] => ''.$operand.$taxes['total_price_tax_excl'] * 100 .'',
						'dan'.$vatRates[$key] => ''.$operand.$taxes['total_amount'] * 100 .''
					));
				}
			}
		}

		if(Configuration::get(Pms_GoPay_Extra::$SFIX.'_VAT_OTHER'))
			$items = array_merge($items, array(
				'zakl_nepodl_dph' => ''.$operand.$total_paid.''
			));

		$noNewProducts = self::getProductTaxesBreakdown($order, true);
		if (is_array($noNewProducts) && count($noNewProducts) > 0)
		{
			foreach ($noNewProducts as $key => $noNew)
			{
				$key = round($key);
				if (isset($vatRates[$key]))
				{
					$items = array_merge($items, array(
						'pouzit_zboz'.$vatRates[$key] => ''.$operand.$noNew['total_price_tax_excl'] * 100 .''
					));
				}
			}
		}
/*
echo '$items<pre>+++';
print_r($items);
echo '</pre>';

exit;*/
		return $items;
	}

	/**
	 * pole pro EET Refund
	 **/
	public static function getEETRefund(Order $order, $products_ref, $quantity, $pricing, $shipping, $discount)
	{
		$carrier		 = new Carrier($order->id_carrier);
		$currency		 = new Currency($order->id_currency);
		$context		 = Context::getContext();
		$products		 = $order->getProducts();
		$discounts		 = $order->getCartRules();
		$vatRates		 = Pms_GoPay_Extra_Helper::getVatRates();
		$tax_details	 = $order->getProductTaxesDetails();
		$total_paid		 = 0;
		$discount_amount = 0;
		$discount_wt	 = 0;
		$vatSumary		 = array();
		$rouding		 = 2;
		$items			 = array();

		foreach ($products as $key => $product)
		{
			$count = 0;
			$quantity_prod = $quantity[$product['id_order_detail']];

			if ($quantity_prod > 0 && $quantity_prod <= $product['product_quantity'])
				$count = $quantity_prod;

			if (is_array($products_ref) && in_array($product['id_order_detail'], $products_ref) && $count > 0)
			{
				$tax_incl = $product['unit_price_tax_incl'];
				$new_price = $pricing[$product['id_order_detail']];

				$ratio = Tools::ps_round($tax_incl, $rouding) / $new_price;
				$ratio = $ratio < 1 ? 1 : $ratio;

				$tax_excl = $product['unit_price_tax_excl'] / $ratio;
				$tax = $new_price - $tax_excl;

				$amount = Tools::ps_round($new_price * $count, $rouding);
				$product_url = $context->link->getProductLink($product['product_id'], null, null, null, null, $order->id_shop);

				$items[] = array(
					'type' => 'ITEM',
					'name' => ''.mb_substr(trim(strip_tags($product['product_name'])), 0, 255).'',
					'ean' => $product['product_ean13'],
					'product_url' => $product_url,
					'amount' => ''.-$amount * 100 .'',
					'count' => $count,
					'vat_rate' => ''.isset($vatRates[$product['tax_rate']]) ? $product['tax_rate'] : 0 .''
				);

				if ($discounts)
				{
					foreach ($tax_details as $key => $tax_detail)
					{
						$unit_tax_base = $tax_detail['total_tax_base'] / $product['product_quantity'];
						$unit_amount = $tax_detail['total_amount'] / $product['product_quantity'];

						if ($tax_detail['id_order_detail'] == $product['id_order_detail'])
						{
							$discount_amount += Tools::ps_round(($tax_excl - $unit_tax_base) * $count, $rouding);
							$discount_wt += Tools::ps_round(($tax - $unit_amount) * $count, $rouding);
							$tax_excl = $unit_tax_base;
							$tax = $unit_amount;
						}
					}
				}

				$total_paid += $amount;
				$vatSumary[$vat_rate]['tax_excl'] += Tools::ps_round($tax_excl * $count, $rouding);
				$vatSumary[$vat_rate]['tax'] += Tools::ps_round($tax * $count, $rouding);
			}
		}

		if ($discount)
		{
			foreach ($discounts as $key => $discount)
			{
				$amount = (float)$discount['value'];
				if ($discount_amount > 0)
					$amount = $discount_amount + $discount_wt;

				if ($amount > 0)
				{
					$items[] = array(
						'type' => 'DISCOUNT',
						'product_url' => '',
						'ean' => '',
						'count' => 1,
						'name' => ''.mb_substr(trim(strip_tags ($discount['name'])), 0, 255).'',
						'amount' => ''.Tools::ps_round($amount, $rouding) * 100 .'',
						'vat_rate' => 0
					);

					$total_paid -= $amount;
				}
			}
		}

		if ($shipping)
		{
			$amount = (float)$order->total_shipping_tax_incl;

			$vat_rate = $carrier->getTaxesRate(new Address($order->id_address_delivery));

			if ($amount > 0)
			{
				$items[] = array(
						'type' => 'DELIVERY',
						'product_url' => '',
						'ean' => '',
						'count' => 1,
						'name' => ''.mb_substr(trim(strip_tags ($carrier->name)), 0, 255).'',
						'amount' => ''.-Tools::ps_round($amount, $rouding) * 100 .'',
						'vat_rate' => ''.isset($vatRates[$vat_rate]) ? $vat_rate : 0 .''
				);

				$total_paid += $amount;
				$vatSumary[$vat_rate]['tax_excl'] += $order->total_shipping_tax_excl;
				$vatSumary[$vat_rate]['tax'] += ($amount - $order->total_shipping_tax_excl);
			}
		}

		$eet = array(
					'celk_trzba' => ''.-Tools::ps_round($total_paid, $rouding) * 100 .'',
					'mena' => ''.$currency->iso_code.''
		);

		foreach ($vatSumary as $key => $taxes)
		{
			$key = round($key);
			if($key == 0)
				$eet = array_merge($eet, array(
					'zakl_nepodl_dph' => ''.-Tools::ps_round($taxes['tax_excl'], $rouding) * 100 .''
				));
			else
			{
				if (isset($vatRates[$key]))
				{
					$eet = array_merge($eet, array(
							'zakl_dan'.$vatRates[$key] => ''.-Tools::ps_round($taxes['tax_excl'], $rouding) * 100 .'',
							'dan'.$vatRates[$key] => ''.-Tools::ps_round($taxes['tax'], $rouding) * 100 .''
					));
				}
			}
		}

		$data = array(
				'amount' => ''.Tools::ps_round($total_paid, $rouding) * 100 .'',
				'items' => $items,
				'eet' => $eet
		);

/*
echo '<pre>+++';
print_r($data);
echo '</pre>';
exit;
*/
		return $data;
	}

    private static function getProductTaxesBreakdown(Order $order, $noNew = false)
    {
        $sum_composite_taxes = !$order->useOneAfterAnotherTaxComputationMethod();

        // $breakdown will be an array with tax rates as keys and at least the columns:
        // 	- 'total_price_tax_excl'
        // 	- 'total_amount'
        $breakdown = array();

		$oldProducts = self::getOrderDetailListNoNew($order->id);

		$details =  array();
		if (!$noNew)
        	$details = self::getProductTaxesDetails($order);
		else
			if (count($oldProducts) > 0)
        		$details = self::getProductTaxesDetails($order, $oldProducts);


		if ($sum_composite_taxes) {
            $grouped_details = array();
            foreach ($details as $row) {
                if (!isset($grouped_details[$row['id_order_detail']])) {
                    $grouped_details[$row['id_order_detail']] = array(
                        'tax_rate' => 0,
                        'total_tax_base' => 0,
                        'total_amount' => 0,
                        'id_tax' => $row['id_tax'],
                    );
                }

                $grouped_details[$row['id_order_detail']]['tax_rate'] += $row['tax_rate'];
                $grouped_details[$row['id_order_detail']]['total_tax_base'] += $row['total_tax_base'];
                $grouped_details[$row['id_order_detail']]['total_amount'] += $row['total_amount'];
            }

            $details = $grouped_details;
        }

        foreach ($details as $detail) {
            $rate = sprintf('%.3f', $detail['tax_rate']);
            if (!isset($breakdown[$rate])) {
                $breakdown[$rate] = array(
                    'total_price_tax_excl' => 0,
                    'total_amount' => 0,
                    'id_tax' => $detail['id_tax'],
                    'rate' =>$rate,
                );
            }

            $breakdown[$rate]['total_price_tax_excl'] += $detail['total_tax_base'];
            $breakdown[$rate]['total_amount'] += $detail['total_amount'];
        }

		if (!isset($order->round_mode))
			$order->round_mode = 2;
 
		foreach ($breakdown as $rate => $data) {
            $breakdown[$rate]['total_price_tax_excl'] = Tools::ps_round($data['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
            $breakdown[$rate]['total_amount'] = Tools::ps_round($data['total_amount'], _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
        }

        ksort($breakdown);

        return $breakdown;
    }

	private static function getOrderDetailListNoNew($id_order)
    {
        return Db::getInstance()->executeS('
			SELECT od.*
			FROM `'._DB_PREFIX_.'order_detail` od
			LEFT JOIN `'._DB_PREFIX_.'product` p ON (od.`product_id` = p.`id_product`)
			WHERE od.`id_order` = '.(int)$id_order.'
			AND p.`condition` != \'new\''
		);
    }
 
	private static function getProductTaxesDetails($order, $limitToOrderDetails = false)
    {
		if (!isset($order->round_mode))
			$order->round_mode = 2;

		if (!isset($order->round_type))
			$order->round_type = 2;

        $round_type = $order->round_type;
        if ($round_type == 0) {
            // if this is 0, it means the field did not exist
            // at the time the order was made.
            // Set it to old type, which was closest to line.
            $round_type = self::ROUND_LINE;
        }

        // compute products discount
        $order_discount_tax_excl = $order->total_discounts_tax_excl;

        $free_shipping_tax = 0;
        $product_specific_discounts = array();

        $expected_total_base = $order->total_products - $order->total_discounts_tax_excl;

        foreach ($order->getCartRules() as $order_cart_rule) {
            if ($order_cart_rule['free_shipping'] && $free_shipping_tax === 0) {
                $free_shipping_tax = $order->total_shipping_tax_incl - $order->total_shipping_tax_excl;
                $order_discount_tax_excl -= $order->total_shipping_tax_excl;
                $expected_total_base += $order->total_shipping_tax_excl;
            }

            $cart_rule = new CartRule($order_cart_rule['id_cart_rule']);
            if ($cart_rule->reduction_product > 0) {
                if (empty($product_specific_discounts[$cart_rule->reduction_product])) {
                    $product_specific_discounts[$cart_rule->reduction_product] = 0;
                }

                $product_specific_discounts[$cart_rule->reduction_product] += $order_cart_rule['value_tax_excl'];
                $order_discount_tax_excl -= $order_cart_rule['value_tax_excl'];
            }
        }

        $products_tax    = $order->total_products_wt - $order->total_products;
        $discounts_tax    = $order->total_discounts_tax_incl - $order->total_discounts_tax_excl;

        // We add $free_shipping_tax because when there is free shipping, the tax that would
        // be paid if there wasn't is included in $discounts_tax.
        $expected_total_tax = $products_tax - $discounts_tax + $free_shipping_tax;
        $actual_total_tax = 0;
        $actual_total_base = 0;

        $order_detail_tax_rows = array();

        $breakdown = array();

        // Get order_details
        $order_details = $limitToOrderDetails ? $limitToOrderDetails : $order->getOrderDetailList();

        $order_ecotax_tax = 0;

        $tax_rates = array();

        foreach ($order_details as $order_detail) {
            $id_order_detail = $order_detail['id_order_detail'];
            $tax_calculator = OrderDetail::getTaxCalculatorStatic($id_order_detail);
            // TODO: probably need to make an ecotax tax breakdown here instead,
            // but it seems unlikely there will be different tax rates applied to the
            // ecotax in the same order in the real world
            $unit_ecotax_tax = $order_detail['ecotax'] * $order_detail['ecotax_tax_rate'] / 100.0;
            $order_ecotax_tax += $order_detail['product_quantity'] * $unit_ecotax_tax;

            $discount_ratio = 0;

            if ($order->total_products > 0) {
                $discount_ratio = ($order_detail['unit_price_tax_excl'] + $order_detail['ecotax']) / $order->total_products;
            }

            // share of global discount
            $discounted_price_tax_excl = $order_detail['unit_price_tax_excl'] - $discount_ratio * $order_discount_tax_excl;
            // specific discount
            if (!empty($product_specific_discounts[$order_detail['product_id']])) {
                $discounted_price_tax_excl -= $product_specific_discounts[$order_detail['product_id']];
            }

            $quantity = $order_detail['product_quantity'];

            foreach ($tax_calculator->taxes as $tax) {
                $tax_rates[$tax->id] = $tax->rate;
            }

            foreach ($tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $unit_amount) {
                $total_tax_base = 0;
                switch ($round_type) {
                    case self::ROUND_ITEM:
                        $total_tax_base = $quantity * Tools::ps_round($discounted_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
                        $total_amount = $quantity * Tools::ps_round($unit_amount, _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
                        break;
                    case self::ROUND_LINE:
                        $total_tax_base = Tools::ps_round($quantity * $discounted_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
                        $total_amount = Tools::ps_round($quantity * $unit_amount, _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
                        break;
                    case self::ROUND_TOTAL:
                        $total_tax_base = $quantity * $discounted_price_tax_excl;
                        $total_amount = $quantity * $unit_amount;
                        break;
                }

                if (!isset($breakdown[$id_tax])) {
                    $breakdown[$id_tax] = array('tax_base' => 0, 'tax_amount' => 0);
                }

                $breakdown[$id_tax]['tax_base'] += $total_tax_base;
                $breakdown[$id_tax]['tax_amount'] += $total_amount;

                $order_detail_tax_rows[] = array(
                    'id_order_detail' => $id_order_detail,
                    'id_tax' => $id_tax,
                    'tax_rate' => $tax_rates[$id_tax],
                    'unit_tax_base' => $discounted_price_tax_excl,
                    'total_tax_base' => $total_tax_base,
                    'unit_amount' => $unit_amount,
                    'total_amount' => $total_amount
                );
            }
        }

        if (!empty($order_detail_tax_rows)) {
            foreach ($breakdown as $data) {
                $actual_total_tax += Tools::ps_round($data['tax_amount'], _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
                $actual_total_base += Tools::ps_round($data['tax_base'], _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
            }

            $order_ecotax_tax = Tools::ps_round($order_ecotax_tax, _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);

            $tax_rounding_error = $expected_total_tax - $actual_total_tax - $order_ecotax_tax;
            if ($tax_rounding_error !== 0) {
                self::spreadAmount($tax_rounding_error, _PS_PRICE_COMPUTE_PRECISION_, $order_detail_tax_rows, 'total_amount');
            }

            $base_rounding_error = $expected_total_base - $actual_total_base;
            if ($base_rounding_error !== 0 && !$limitToOrderDetails) {
                self::spreadAmount($base_rounding_error, _PS_PRICE_COMPUTE_PRECISION_, $order_detail_tax_rows, 'total_tax_base');
            }
        }

        return $order_detail_tax_rows;
    }

	public static function spreadAmount($amount, $precision, &$rows, $column)
    {
        if (!is_array($rows) || empty($rows)) {
            return;
        }

        $sort_function = create_function('$a, $b', "return \$b['$column'] > \$a['$column'] ? 1 : -1;");

        uasort($rows, $sort_function);

        $unit = pow(10, $precision);

        $int_amount = (int)round($unit * $amount);

        $remainder = $int_amount % count($rows);
        $amount_to_spread = ($int_amount - $remainder) / count($rows) / $unit;

        $sign = ($amount >= 0 ? 1 : -1);
        $position = 0;
        foreach ($rows as &$row) {
            $adjustment_factor = $amount_to_spread;

            if ($position < abs($remainder)) {
                $adjustment_factor += $sign * 1 / $unit;
            }

            $row[$column] += $adjustment_factor;

            ++$position;
        }
        unset($row);
    }

	/**
	 * přiřazení kódů států
	 **/
	public static function getConvertedCountryCode($iso_code)
	{
		$countryCodeTable = array(
			'AF' => 'AFG', 
			'AE' => 'ARE', 
			'AG' => 'ATG', 
			'AI' => 'AIA', 
			'AL' => 'ALB', 
			'AM' => 'ARM', 
			'AO' => 'AGO', 
			'AQ' => 'ATA', 
			'AR' => 'ARG',
			'AS' => 'ASM', 
			'AT' => 'AUT', 
			'AU' => 'AUS', 
			'AW' => 'ABW', 
			'AX' => 'ALA',
			'AZ' => 'AZE', 
			'BA' => 'BIH', 
			'BB' => 'BRB', 
			'BD' => 'BGD', 
			'BE' => 'BEL',
			'BF' => 'BFA', 
			'BG' => 'BGR', 
			'BH' => 'BHR', 
			'BI' => 'BDI', 
			'BJ' => 'BEN', 
			'BM' => 'BMU', 
			'BN' => 'BRN',
			'BO' => 'BOL', 
			'BR' => 'BRA', 
			'BS' => 'BHS', 
			'BT' => 'BTN', 
			'BV' => 'BVT', 
			'BW' => 'BWA', 
			'BY' => 'BLR', 
			'BZ' => 'BLZ', 
			'CA' => 'CAN', 
			'CC' => 'CCK', 
			'CF' => 'CAF', 
			'CG' => 'COG', 
			'CH' => 'CHE',
			'CI' => 'CIV', 
			'CK' => 'COK', 
			'CL' => 'CHL',
			'CM' => 'CMR',
			'CN' => 'CHN', 
			'CO' => 'COL', 
			'CR' => 'CRI', 
			'CU' => 'CUB',
			'CV' => 'CPV', 
			'CX' => 'CXR', 
			'CY' => 'CYP', 
			'CZ' => 'CZE', 
			'DE' => 'DEU', 
			'DJ' => 'DJI', 
			'DK' => 'DNK', 
			'DM' => 'DMA', 
			'DO' => 'DOM', 
			'DZ' => 'DZA', 
			'EC' => 'ECU', 
			'EE' => 'EST', 
			'EG' => 'EGY', 
			'EH' => 'ESH', 
			'ER' => 'ERI', 
			'ES' => 'ESP', 
			'ET' => 'ETH', 
			'FI' => 'FIN', 
			'FJ' => 'FJI', 
			'FK' => 'FLK', 
			'FM' => 'FSM', 
			'FO' => 'FRO', 
			'FR' => 'FRA', 
			'GA' => 'GAB', 
			'GB' => 'GBR', 
			'GD' => 'GRD', 
			'GE' => 'GEO', 
			'GF' => 'GUF', 
			'GG' => 'GGY',
			'GH' => 'GHA', 
			'GI' => 'GIB', 
			'GL' => 'GRL', 
			'GM' => 'GMB', 
			'GN' => 'GIN',
			'GP' => 'GLP',
			'GQ' => 'GNQ',
			'GR' => 'GRC',
			'GS' => 'SGS', 
			'GT' => 'GTM', 
			'GU' => 'GUM', 
			'GW' => 'GNB', 
			'GY' => 'GUY', 
			'HK' => 'HKG', 
			'HM' => 'HMD', 
			'HN' => 'HND', 
			'HR' => 'HRV', 
			'HT' => 'HTI', 
			'HU' => 'HUN', 
			'CH' => 'CHE', 
			'ID' => 'IDN', 
			'IE' => 'IRL', 
			'IL' => 'ISR', 
			'IN' => 'IND', 
			'IO' => 'IOT', 
			'IQ' => 'IRQ', 
			'IR' => 'IRN', 
			'IS' => 'ISL',
			'IT' => 'ITA', 
			'JE' => 'JEY',
			'JM' => 'JAM', 
			'JO' => 'JOR', 
			'JP' => 'JPN', 
			'KE' => 'KEN', 
			'KG' => 'KGZ', 
			'KH' => 'KHM', 
			'KI' => 'KIR', 
			'KM' => 'COM', 
			'KN' => 'KNA', 
			'KP' => 'PRK',
			'KR' => 'KOR', 
			'KW' => 'KWT', 
			'KY' => 'CYM', 
			'KZ' => 'KAZ',
			'LA' => 'LAO', 
			'LB' => 'LBN', 
			'LC' => 'LCA', 
			'LI' => 'LIE', 
			'LK' => 'LKA', 
			'LR' => 'LBR',
			'LS' => 'LSO', 
			'LT' => 'LTU', 
			'LU' => 'LUX', 
			'LV' => 'LVA', 
			'LY' => 'LBY', 
			'MA' => 'MAR', 
			'MC' => 'MCO', 
			'MD' => 'MDA', 
			'ME' => 'MNE',
			'MG' => 'MDG', 
			'MH' => 'MHL', 
			'MK' => 'MKD', 
			'ML' => 'MLI',
			'MM' => 'MMR', 
			'MN' => 'MNG',
			'MO' => 'MAC', 
			'MP' => 'MNP', 
			'MQ' => 'MTQ', 
			'MR' => 'MRT', 
			'MS' => 'MSR', 
			'MT' => 'MLT', 
			'MU' => 'MUS', 
			'MV' => 'MDV', 
			'MW' => 'MWI', 
			'MX' => 'MEX', 
			'MY' => 'MYS', 
			'MZ' => 'MOZ', 
			'NA' => 'NAM', 
			'NC' => 'NCL', 
			'NE' => 'NER', 
			'NF' => 'NFK', 
			'NG' => 'NGA', 
			'NI' => 'NIC',
			'NL' => 'NLD', 
			'NO' => 'NOR', 
			'NP' => 'NPL', 
			'NR' => 'NRU', 
			'NU' => 'NIU', 
			'NZ' => 'NZL',
			'OM' => 'OMN', 
			'PA' => 'PAN',
			'PE' => 'PER', 
			'PF' => 'PYF',
			'PG' => 'PNG', 
			'PH' => 'PHL', 
			'PK' => 'PAK', 
			'PL' => 'POL', 
			'PM' => 'SPM', 
			'PN' => 'PCN', 
			'PR' => 'PRI', 
			'PT' => 'PRT', 
			'PW' => 'PLW', 
			'PY' => 'PRY', 
			'QA' => 'QAT', 
			'RE' => 'REU', 
			'RO' => 'ROU',
			'RS' => 'SRB',
			'RU' => 'RUS',
			'RW' => 'RWA', 
			'SA' => 'SAU', 
			'SB' => 'SLB', 
			'SC' => 'SYC',
			'SD' => 'SDN', 
			'SE' => 'SWE',
			'SG' => 'SGP',
			'SH' => 'SHN',
			'SI' => 'SVN', 
			'SJ' => 'SJM', 
			'SK' => 'SVK',
			'SL' => 'SLE', 
			'SM' => 'SMR', 
			'SN' => 'SEN', 
			'SO' => 'SOM',
			'SR' => 'SUR', 
			'ST' => 'STP', 
			'SV' => 'SLV', 
			'SY' => 'SYR', 
			'SZ' => 'SWZ', 
			'TC' => 'TCA', 
			'TD' => 'TCD', 
			'TF' => 'ATF', 
			'TG' => 'TGO', 
			'TH' => 'THA', 
			'TJ' => 'TJK',
			'TK' => 'TKL', 
			'TM' => 'TKM', 
			'TN' => 'TUN', 
			'TO' => 'TON', 
			'TR' => 'TUR', 
			'TT' => 'TTO', 
			'TV' => 'TUV', 
			'TW' => 'TWN', 
			'TZ' => 'TZA',
			'UA' => 'UKR', 
			'UG' => 'UGA',
			'UM' => 'UMI', 
			'US' => 'USA', 
			'UY' => 'URY', 
			'UZ' => 'UZB', 
			'VA' => 'VAT',
			'VC' => 'VCT', 
			'VE' => 'VEN', 
			'VG' => 'VGB',
			'VI' => 'VIR', 
			'VN' => 'VNM',
			'VU' => 'VUT', 
			'WF' => 'WLF', 
			'WS' => 'WSM', 
			'YE' => 'YEM', 
			'YT' => 'MYT', 
			'YU' => 'BIH',
			'ZA' => 'ZAF',
			'ZM' => 'ZMB', 
			'ZW' => 'ZWE'
		);

		if ($iso_code)
			return $countryCodeTable[$iso_code];
	}
}