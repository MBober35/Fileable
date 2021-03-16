# ChangeLog

## [1.3.0]
- Добавлен компонет `x-picture`
- Изменен путь, вместо парметра `file` теперь `filename`
- Добавлен пакет `imagecache`, что бы выводить изображения, которых нет в базе данных

## [1.2.0]
- Добавлен трейт `ShouldDocuments`
- Добавлен компонент `x-documents`
- Добавлена возможность валидации расширения загружаемых файлов в галерею и документы

### Обновление
- php artisan fileable --no-replace
- php artisan vendor:publish --provider="MBober35\Fileable\ServiceProvider" --tag=public

## [1.1.0]
- Добавлен трейт `ShouldDocument`
- Изменено сохранение изображений