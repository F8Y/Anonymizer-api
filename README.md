# Sirius 27 Anonymizer API

REST API-модуль на PHP для обезличивания персональных данных участников.

Проект реализован как самостоятельный stateless-сервис: он принимает JSON с персональными данными, применяет правила обезличивания и возвращает обезличенный результат. Сервис не хранит входные данные, не использует базу данных и не поддерживает обратное восстановление исходных значений.

## Назначение

Модуль предназначен для интеграции с существующей системой Регионального центра «Сириус 27» через HTTP API.

Основной сценарий:

```text
Moodle / legacy-система
        ↓ HTTP POST
Anonymizer API
        ↓ JSON response
обезличенные данные
```

## Текущий функционал

- прием данных участника через `POST /v1/anonymize`;
- обезличивание логина;
- генерация публичного идентификатора участника;
- удаление имени, отчества и фамилии из публичного представления;
- маскирование email;
- маскирование телефона при наличии;
- сокращение даты рождения до года при наличии;
- единый формат ошибок в стиле Problem Details;
- health endpoint для проверки состояния сервиса;
- Docker-first запуск;
- OpenAPI-контракт.

## API endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/health` | Проверка состояния сервиса |
| `POST` | `/v1/anonymize` | Обезличивание данных участника |

## Входной формат

```json
{
  "login": "ivanov_ii",
  "first_middle_name": "Иван Иванович",
  "last_name": "Иванов",
  "email": "ivanov@example.com",
  "phone": "+79991234567",
  "birth_date": "2010-04-12"
}
```

### Обязательные поля

| Field | Type | Description |
|---|---|---|
| `login` | `string` | Логин участника в исходной системе |
| `first_middle_name` | `string` | Имя и отчество одним полем |
| `last_name` | `string` | Фамилия |
| `email` | `string` | Адрес электронной почты |

### Необязательные поля

| Field | Type | Description |
|---|---|---|
| `phone` | `string \| null` | Номер телефона при наличии |
| `birth_date` | `string \| null` | Дата рождения в формате `YYYY-MM-DD` при наличии |

## Выходной формат

```json
{
  "public_id": "USER-88CF2D01BD41",
  "login": "LOGIN-680B0200B387",
  "first_middle_name": "[обезличено]",
  "last_name": "[обезличено]",
  "email": "i****v@***.com",
  "phone": "+7********67",
  "birth_date": "2010"
}
```

Если `phone` или `birth_date` не были переданы, они возвращаются как `null`.

```json
{
  "public_id": "USER-88CF2D01BD41",
  "login": "LOGIN-680B0200B387",
  "first_middle_name": "[обезличено]",
  "last_name": "[обезличено]",
  "email": "i****v@***.com",
  "phone": null,
  "birth_date": null
}
```

## Принцип обезличивания

### `public_id`

`public_id` — детерминированный публичный идентификатор участника.

Он формируется на основе входных данных и секретного ключа `APP_ANONYMIZATION_SECRET` через HMAC-SHA256.

```text
исходные данные + secret → USER-XXXXXXXXXXXX
```

Это означает:

- одинаковые входные данные при одинаковом секрете дают одинаковый `public_id`;
- при смене секрета значения `public_id` изменятся;
- сервис не хранит таблицу соответствий `ID ↔ реальные данные`;
- восстановить исходные данные по `public_id` средствами сервиса невозможно.

### `login`

Логин также псевдонимизируется:

```text
ivanov_ii → LOGIN-XXXXXXXXXXXX
```

Это важно, потому что логин часто содержит фамилию, инициалы или другой идентификатор.

### ФИО

Имя, отчество и фамилия не маскируются частично, а полностью удаляются из публичного представления:

```text
Иван Иванович → [обезличено]
Иванов → [обезличено]
```

### Email

Email маскируется с сохранением минимальной структуры:

```text
ivanov@example.com → i****v@***.com
```

### Phone

Телефон маскируется с сохранением кода страны и последних двух цифр:

```text
+79991234567 → +7********67
```

### Birth date

Дата рождения сокращается до года:

```text
2010-04-12 → 2010
```

## Ошибки API

Ошибки возвращаются в формате `application/problem+json; charset=utf-8`.

### Невалидный JSON

Status: `400 Bad Request`

```json
{
  "type": "https://sirius27.local/problems/invalid-json",
  "title": "Invalid JSON",
  "status": 400,
  "detail": "Request body must be valid JSON"
}
```

### Ошибка валидации

Status: `422 Unprocessable Entity`

```json
{
  "type": "https://sirius27.local/problems/validation-error",
  "title": "Validation error",
  "status": 422,
  "detail": "Request validation failed",
  "errors": {
    "email": [
      "email must be valid"
    ],
    "last_name": [
      "last_name is required"
    ]
  }
}
```

### Внутренняя ошибка сервера

Status: `500 Internal Server Error`

```json
{
  "type": "https://sirius27.local/problems/internal-server-error",
  "title": "Internal server error",
  "status": 500,
  "detail": "Unexpected server error"
}
```

## OpenAPI

Формальный API-контракт находится в файле:

```text
docs/openapi.yaml
```

Он описывает:

- доступные endpoint'ы;
- формат входных данных;
- формат успешных ответов;
- формат ошибок;
- обязательные и необязательные поля.

## Стек

- PHP 8.4
- Slim Framework
- PHP-DI
- Symfony Validator
- vlucas/phpdotenv
- PHPUnit
- Docker
- Composer

## Структура проекта

```text
anonymizer-api/
├── config/
│   ├── app.php
│   ├── container.php
│   └── routes.php
├── docs/
│   └── openapi.yaml
├── public/
│   └── index.php
├── src/
│   ├── Application/
│   │   └── UseCase/
│   ├── Domain/
│   │   └── Anonymization/
│   │       ├── Contract/
│   │       ├── DTO/
│   │       ├── Rule/
│   │       └── Service/
│   ├── Http/
│   │   ├── Action/
│   │   ├── Exception/
│   │   ├── Middleware/
│   │   └── Response/
│   ├── Infrastructure/
│   │   └── Validation/
│   └── Support/
│       └── Exception/
├── tests/
│   ├── Integration/
│   ├── Support/
│   └── Unit/
├── .dockerignore
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
├── docker-compose.yml
├── Dockerfile
├── phpunit.xml
└── README.md
```

## Архитектура

Проект разделен на несколько слоев.

### `Domain`

Содержит бизнес-логику обезличивания:

- DTO;
- правила обезличивания;
- сервис `Anonymizer`;
- контракты.

### `Application`

Содержит сценарии использования. Сейчас основной сценарий — `AnonymizeDataUseCase`.

### `Http`

Содержит HTTP-слой:

- actions;
- middleware;
- response factories;
- HTTP-исключения.

### `Infrastructure`

Содержит технические реализации:

- валидация DTO;
- маппинг имен полей ошибок во внешний API-контракт.

### `Support`

Содержит общие вспомогательные классы и исключения.

## Переменные окружения

Создай `.env` на основе `.env.example`.

```env
APP_ENV=dev
APP_ANONYMIZATION_SECRET=change-me-in-real-environment
```

### `APP_ANONYMIZATION_SECRET`

Секрет используется для генерации `public_id` и псевдонимизированного `login`.

Важно:

- не коммитить `.env`;
- не использовать одинаковый секрет для всех окружений;
- не менять секрет в рабочем окружении без необходимости;
- при смене секрета старые и новые идентификаторы перестанут совпадать.

## Генерация секрета в PowerShell

```powershell
$bytes = New-Object 'Byte[]' 32
$rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
$rng.GetBytes($bytes)
[Convert]::ToBase64String($bytes)
```

Полученную строку нужно записать в `.env`:

```env
APP_ANONYMIZATION_SECRET=<generated-secret>
```

## Запуск через Docker

### Установка зависимостей

```powershell
docker compose run --rm composer install
```

### Запуск сервиса

```powershell
docker compose up --build app
```

Сервис будет доступен по адресу:

```text
http://localhost:8080
```

### Проверка health endpoint

```powershell
curl.exe -i http://localhost:8080/health
```

Ожидаемый ответ:

```json
{
  "status": "ok",
  "service": "anonymizer-api"
}
```

## Пример запроса через PowerShell

```powershell
$body = @{
  login = "ivanov_ii"
  first_middle_name = "Иван Иванович"
  last_name = "Иванов"
  email = "ivanov@example.com"
  phone = "+79991234567"
  birth_date = "2010-04-12"
} | ConvertTo-Json -Compress

Invoke-RestMethod `
  -Uri "http://localhost:8080/v1/anonymize" `
  -Method Post `
  -ContentType "application/json; charset=utf-8" `
  -Body $body
```

## Пример запроса через curl.exe

```powershell
curl.exe -i -X POST "http://localhost:8080/v1/anonymize" `
  -H "Content-Type: application/json; charset=utf-8" `
  --data-raw '{"login":"ivanov_ii","first_middle_name":"Иван Иванович","last_name":"Иванов","email":"ivanov@example.com","phone":"+79991234567","birth_date":"2010-04-12"}'
```

## Запуск тестов

```powershell
docker compose exec app ./vendor/bin/phpunit
```

или:

```powershell
docker compose exec app composer test
```

## Composer autoload

Если были добавлены новые классы и автозагрузка их не видит:

```powershell
docker compose run --rm composer dump-autoload
```

## Что не хранится в репозитории

Не должны попадать в git:

```text
.env
vendor/
.phpunit.cache/
.phpunit.result.cache
```

Должны попадать в git:

```text
.env.example
composer.json
composer.lock
docs/openapi.yaml
```

## Ограничения текущей версии

- сервис не хранит соответствие между исходными и обезличенными данными;
- сервис не выполняет деобезличивание;
- сервис не отправляет уведомления;
- сервис не индексирует классы учащихся;
- сервис не содержит интеграции с Moodle API;
- сервис не использует базу данных.

Текущая версия отвечает только за обособленное обезличивание входного JSON.

## Возможные направления развития

- добавление batch endpoint для пакетного обезличивания;
- подключение Swagger UI для просмотра `docs/openapi.yaml`;
- добавление Docker healthcheck;
- разделение dev/prod Docker-окружений;
- добавление request id и безопасного логирования без записи персональных данных;
- добавление версии сервиса в `/health`.