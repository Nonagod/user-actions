# "Действия пользователя" (`php`)
Реализация серверной части абстракции "user actions", на языке `php`.

1. [Установка](#install)
2. [Использование](#using)
3. [Подключение](#connection)
4. [Форматы](#formats)

## <a name="install"></a>Установка
> composer require nonagod/user-actions

## <a name="connection"></a>Инициализация
composer require nonagor/user_actions

## <a name="using"></a>Использование
Примеры использования можно посмотреть в папке [`examples`](examples).

### Инициализация
Объект класса должен быть создан **до вывода** како-го либо контента и **после определения** всех нужных конструкций.

 ```php
use \Nonagod\UserActions\Manager;
global $UAManager;
$UAManager = new Manager( $_SERVER['DOCUMENT_ROOT'] . '/examples/_resources/UAM');
```

#### `__constructor( $path )`:
- `string $path` - абсолютный путь до папки с обработчиками действий

> **Замечание:** Директорию с обработчиками желательно выносить за пределы сайта или закрывать от доступа пользователей.

### Обработчики
```php
<?php
/**
 * @var Nonagod\UserActions\Manager $this
 */

$this->succeed("Выполнено");
$this->failed("ERROR", "Ошибка");
```
> **Замечание:** Каждый обработчик обязательно должен вызывать соответствующий метод завершения.

> **Замечание:** `Nonagod\UserActions\Manager` отлавливает `\Nonagod\Exceptions\UserException` и самостоятельно 
> вызывает `failed`. В таком случае, явный вызов в обработчике не требуется.

#### `succeed( $answer_data = null )`:
Отправляет ответ об успешном завершение обработчика.
- `?mixed $answer_data` - произвольные данные, для дополнительной обработке на клиенте

#### `failed( string $code, string $msg = null, $error_info = null )`:
Отправляет ответ об ошибке обработчика.
- `string $code` - код ошибки
- `?string $msg` - краткое описание ошибки
- `?mixed $error_info` - дополнительные данные для доп. обработки ошибки на клиенте

### Запрос контента
```php
<?php
// ... some code
global $UAManager
$UAManager->defineStartOfContentPart('<part_name>');
// требуемый кусок страницы
$UAManager->defineEndOfContentPart('<part_name>');
// ... more code
```
> **Замечание:** `defineStartOfContentPart` и `defineEndOfContentPart` парные методы.

#### `defineStartOfContentPart( $name )`:
Опеределяет начало запрашиваемой части контента.
- `string $name` - кодовое обозначение части страницы

#### `defineEndOfContentPart( $name )`:
Опеределяет конец запрашиваемой части контента.
- `string $name` - кодовое обозначение части страницы

## <a name="formats"></a>Форматы
**Запрос:**
```javascript
{
    user_action: "<название_действия>|buffer", // обязательный, действия которое нужно выполнить
    part: "<название_запрашиваемой_части_контента>", // обязательный, если действие buffer 
    "<дополнительные_параметры>": "<значение>" // дополнительные, произвольные параметры требуемые для действий
}
```

**Успешный ответ:**
```javascript
{
    status: true,
    result: "mixed", // произвольный, зависит от выполненного действия
}
```

**Ответ с ошибкой:**
```javascript
{
    status: false,
    result: {
        code: "string", // обязательный, код ошибки
        msg: "string", // текстовое пояснение ошибки
        info: "mixed" // произвольный, дополнительные данные ошибки, если требуются
    }
}
```
## Дополнительные утверждения
- Логирование, капча и прочее, делается в обработчиках, где это необходимо.

## ToDo
- [ ] Сделать отлов прочих (помимо `UserExceptions`) ошибок, их логирование и отдачу пользователя обобщающей ошибки 
  ("Что-то пошло не так")
- [ ] Подумать, может можно сделать для обработчиков свой класс, чтобы убрать рутину из обработчиков. (+ управлять 
  валидацией и откатом)
- [ ] Заменить очистку активных буферов в методе `defineStartOfContentPart`
```php
/*for( $i = 0; $i < ob_get_level()+1; $i++ ) {
    $trashed_content = ob_get_clean();
    unset($trashed_content);
}*/
while( ob_get_length( )) {
    $trashed_content = ob_get_clean();
    unset($trashed_content);
}
```
