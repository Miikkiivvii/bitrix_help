				<?php
					use Bitrix\Main\Loader;
					use Bitrix\Main\LoaderException;
					use Bitrix\Main\Context;
					use Bitrix\Iblock\ElementTable;

					try {
						// Проверяем, что модуль "Инфоблоки" установлен
						if (!Loader::includeModule('iblock')) {
							throw new LoaderException('Модуль "Инфоблоки" не установлен');
						}

						// ID инфоблока
						$iblockId = 27;
						// Значение свойства HARDWARE_TYPE
						$propertyValue = $arResult['PROPERTIES']['HARDWARE_TYPE']['VALUE'];

						// Получаем список элементов инфоблока
						$elements = CIBlockElement::GetList(
							array('rand'),
							array('IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y', 'PROPERTY_HARDWARE_TYPE' => $propertyValue),
							false,
							array('nTopCount' => 2), 
							array('ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL')
						);
						if (!empty($propertyValue)){
						// Выводим список товаров
						while ($element = $elements->GetNextElement()) {
							$fields = $element->GetFields();

							// Получаем путь к картинке
							$picture = CFile::GetPath($fields['PREVIEW_PICTURE']);

							// Получаем ссылку на детальную страницу элемента
							$detailUrl = $fields['DETAIL_PAGE_URL'];

							// Выводим картинку, название товара и ссылку на детальную страницу
							echo '<div>';
							echo '<a href="'.$detailUrl.'">';
							echo '<img src="'.$picture.'" alt="'.$fields['NAME'].'" />';
							echo '<p>'.$fields['NAME'].'</p>';
							echo '</a>';
							echo '</div>';
						}
					}else{
						echo '<div>';
						echo '</div>';
					}
					} catch (Exception $e) {
						echo 'Ошибка: ' . $e->getMessage();
					}
				?>
