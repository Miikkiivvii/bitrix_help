<?php
CModule::IncludeModule("iblock"); 

$iblock_id = 33;   // Цифровое значение ID - Вашего Инфоблока - каталога товаров

$arFilter = Array("IBLOCK_ID" => $iblock_id, "ACTIVE"=>"Y");

$count_goods = CIBlockElement::GetList(Array(), $arFilter, Array(), false, Array());

?>
Количество товаров: <? echo $count_goods; ?>