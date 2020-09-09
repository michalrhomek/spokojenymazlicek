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
class Mail extends MailCore
{
    private $verification_keys = 'MK##PMS_Mail';

    public static function Send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null
	)
	{
		if(!empty($fileAttachment) || is_array($fileAttachment))
			$fileAttachment = array('order_conf' => $fileAttachment);

		Hook::exec('actionGetMailFileAttachments', array(
				'template' => $template,
				'template_vars' => $templateVars,
				'file_attachment' => &$fileAttachment,
				'id_lang' => (int)$idLang
			), null, true);

		if (version_compare(_PS_VERSION_, '1.6.1', '<') === true)
		{
			$extra_template_vars = array();
			Hook::exec('actionGetExtraMailTemplateVars', array(
					'template' => $template,
					'template_vars' => $templateVars,
					'extra_template_vars' => &$extra_template_vars,
					'id_lang' => (int)$idLang
			), null, true);

			$templateVars = array_merge($templateVars, $extra_template_vars);
		}

		return parent::Send(
			$idLang,
			$template,
			$subject,
			$templateVars,
			$to,
			$toName,
			$from,
			$fromName,
			$fileAttachment,
			$mode_smtp,
			$templatePath,
			$die,
			$idShop,
			$bcc,
			$replyTo,
			$replyToName
		);
	}
}