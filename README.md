# Files

Модель файлы, позволяет прикреплять файлы к модели.

Прописаны трейты для добавления изображения и галереи изображений.

Есть VueJs компомент для управления галереей.

Есть генерация миниатюр изображений по имени пути `thumb-img`:{template}{file}. Миниатюры сохраняются отдельными файлами.

## Install

    php artisan migrate
    php artisan vendor:publish --provider="MBober35\Fileable\ServiceProvider" --tag=public
    php artisan fileable
    php artisan storage:link
    FILESYSTEM_DRIVER=public - что бы был доступ к файлам из паблика

Публикация конфигурации:

    php artisan vendor:publish --provider="MBober35\Fileable\ServiceProvider" --tag=config

### Commands

`thumb:clear { --template= : clear only for template } { --all : clear all }`: очистка миниатюр изображений.

### Traits

`ShouldImage` трейт для добавления одного изображения в модель. В таблице должно быть поле `image_id`, либо можно переопределить добавлением переменной `imageKey` в класс модели.

После подключения у модели появляются методы:
- `image`: связь belongsTo
- `uploadImage($path = false, $inputName = "image", $field = "title")`: загрузка изображения из `request`
- `clearImage($deleted = false)`: удалить изображение

`ShouldGallery` трейт для добавления галереи изображений в модель. В конфигурацию необходимо добисать название модели и класс. `"user" => \App\Models\User::class`

После подключения у модели появляются методы:
- `images`: связь morphMany
- `cover`: связь morphOne, для получения первого изображения
- `clearImages`: удалить все изображения

`ShouldDocument` трейт для добавлений файла в модель. В таблице должно быть поле `document_id`, либо можно переопределить добавлением переменной `docKey` в класс модели.

После подключения у модели появляются методы:
- `document`: связь belongsTo
- `uploadDocument($path = false, $inputName = "document", $field = "title")`: загрузка файла из `request`
- `clearDocument`: удалить документ

### Configuration

- `models`: список моделей у которых есть галерея
- `templates`: список фильтров для миниатюр
- `imageResource`: вывод изображений для редактирования изображений

### Components

Вывод галереи `x-gallery`
Параметры: `model`, `id`

    <x-gallery model="user" id="{{ \Illuminate\Support\Facades\Auth::id() }}"></x-gallery>

Вывод документов `x-documents`
Параметры: `model`, `id`

    <x-documents model="user" id="{{\Illuminate\Support\Facades\Auth::id() }}"></x-documents>

Вывод тега picture `x-picture`
Параметры: `template`, `:image`, `:grid`, `class`

    @php($image = ["file_name" => "unsplash.jpg", "name" => "unsplash"])
    @php($grid = [1400 => "large", 992 => "medium"])
    <x-picture template="small" :image="$image" :grid="$grid" class="hello"></x-picture>