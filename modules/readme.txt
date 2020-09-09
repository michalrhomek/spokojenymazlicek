Instalace platebního modulu GoPay pro Prestashop
1. Nakopírujte obsah archívu prestashop_Gopay_module.zip do adresáře s nainstalovaným Prestashopem.
2. Po nainstalování Prestashopu přejděte do administračního rozhraní tzv. BackOffice, vyberte "Modules" a stiskněte tlačítko "Install" u položky GoPay.
3. Konfigurace
Po úspěšné instalaci platebního modulu GoPay klikněte na ">> Configure".

Položky konfigurace:
"GoID" je identifikátor (10ti místné číslo) eshopu v rámci GoPay peněženky. "GoID" je Obchodníkovi přidělěno v okamžiku integrace GoPay (více na podnikej@gopay.cz).
příklad: "1234567890"

"Gopay key" je tajný kód (24 znaků), tzv. secret v rámci systému Gopay. Obchodník jej získá v okamžiku 	integrace GoPay peněženky (více na podnikej@gopay.cz).
příklad: "abcdefgh12345678abcdefgh"

"Gopay header" je URL k logu Gopay.
příklad: "http://www.gopay.cz/images/logo.png" 

"Success URL" určuje cestu, kam bude provedeno přesměrování po úspěšné platbě. Validace platby je prezentována v souboru /modules/gopay/validation.php.
příklad: http://localhost/shop/modules/gopay/validation.php
	
"Failed URL" určuje cestu, kam bude provedeno přesměrování po neúspěšné platbě.
příklad: http://localhost/shop/modules/gopay/failed.php

"BaseIntegration URL" je cesta k platební bráně Gopay.
příklad: https://www.gopay.cz/zaplatit-jednoducha-integrace

"WebService URL"
příklad: https://www.gopay.cz/axis/EPaymentService?wsdl

„History URL“ je cesta, kam bude prestashop nasměrován v případě úspěšné platby a zároveň úspěšného ověření úhrady proti GoPay peněžence. Může být směrován například na historii objednávek. 												
příklad: http://localhost/shop/history.php

4. V případě nelokalizované verze nastavte měnu na CZK.

Prestashop a čestina
Platební modul Gopay má v adresáři /modules/gopay soubor s českou lokalizací cs.php.

Kompletní integrační manuál systému GoPay
https://www.gopay.cz/download/GoPay-integracni-manual_v_1_3p2.pdf

Kontaktní informace   
Technické informace, integrace integrace@gopay.cz
Podpora, provozní otázky uzivej@gopay.cz
Smluvní podpora podnikej@gopay.cz