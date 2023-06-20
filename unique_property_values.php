<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');


CModule::IncludeModule("iblock");

$mainIblockId = 27; // ID основного инфоблока

$arSelect = array(
    "ID",
    "PROPERTY_LENGTH",
);

$arFilter = array(
    "IBLOCK_ID" => $mainIblockId,
    "ACTIVE" => "Y",
    "!PROPERTY_LENGTH" => false,
);

$res = CIBlockElement::GetList(
    array("PROPERTY_LENGTH" => "ASC"),
    $arFilter,
    false,
    false,
    $arSelect
);

$uniquePropertyValues = array();

while ($element = $res->Fetch()) {
    $propertyValue = $element["PROPERTY_LENGTH_VALUE"];
    if (!in_array($propertyValue, $uniquePropertyValues)) {
        $uniquePropertyValues[] = $propertyValue;
    }
}
echo '<pre>';
print_r($uniquePropertyValues);
echo '</pre>';


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>
