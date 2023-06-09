<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle("");
?>

<?php
    CModule::IncludeModule("iblock");

    $arOrder = array(
        'NAME' => "ASC",
    );

    $arFilter = array(
        'IBLOCK_ID' => 33,
        'ACTIVE' => 'Y',
        '!PROPERTY_FILES' => false, // Фильтр: свойство FILES не должно быть пустым
    );

    $arSelectFields = array(
        'ID',
        'NAME',
        'ACTIVE',
        'PROPERTY_FILES', // Добавляем свойство FILES в выбранные поля
    );

    // Добавляем параметры пагинации
    $nPageSize = 10; // Количество элементов на странице
    $nNavPage = (isset($_GET['PAGEN_1'])) ? $_GET['PAGEN_1'] : 1; // Текущая страница

    $res = CIBlockElement::GetList(
        $arOrder,
        $arFilter,
        false,
        array('nPageSize' => $nPageSize, 'iNumPage' => $nNavPage),
        $arSelectFields
    );
    
    echo '<style>pre { background-color: transparent; }</style>';
    while ($element = $res->GetNext()) {
        echo '<pre>';
        $link = CFile::GetPath($element['PROPERTY_FILES_VALUE']);
        echo '<a href="'. $link . '">' . $element['NAME'] . '</a>';
        echo '</pre>';
    }
    
    $arResult['NAV_STRING'] = $res->GetPageNavStringEx(
        $navComponentObject,
        '',
        '.default',
        false,
        null,
        array('PAGEN' => $nNavPage),
        false
    );

    echo $arResult['NAV_STRING'];
?>


<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>