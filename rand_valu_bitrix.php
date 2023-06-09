<?php
use Bitrix\Main\Loader;
Loader::includeModule('iblock');

// Идентификатор инфоблока
$iblockId = 28;

// Получение списка активных элементов инфоблока
$elementList = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => $iblockId,
        'ACTIVE' => 'Y' // Фильтр по активности элементов
    ],
    false,
    false,
    ['ID']
);

// Цикл по элементам
while ($element = $elementList->Fetch()) {
    // Генерация уникального значения для свойства (7-значное число)
    $randomValue = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);

    // Обновление значения свойства для элемента
    CIBlockElement::SetPropertyValuesEx(
        $element['ID'],
        $iblockId,
        ['ARTIKUL_DLYA_SAYTA' => $randomValue]
    );
}
?>

