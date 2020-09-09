<?php

class AuthController extends AuthControllerCore
{

    protected function processSubmitAccount()
    {
        // guest_email proto, aby to nebylo v nakupu bez registrace a to same is_new_customer
        if (Configuration::get('shaim_recaptcha_register') && !Tools::getValue('guest_email') && Tools::getValue('is_new_customer') == 1 && !Tools::getValue('ajax')) {
            $shaim_recaptcha = Module::getInstanceByName('shaim_recaptcha');
            if ($shaim_recaptcha->active == 1) {
                $response = $shaim_recaptcha->TestReCaptcha();
                if (!$response->success) {
                    $this->errors[] = $shaim_recaptcha->ErrorReCaptcha();
                    // Tady to uz neni treba, to uz staci, ze naplnime promennou errors, aspon u 1.6. Jinak by doslo k tomu, ze by to hodilo zpet pri registraci a zakaznik by vse musel vyplnit znovu apod.
                    // return;
                }
            }
        }
        return parent::processSubmitAccount();
    }
}