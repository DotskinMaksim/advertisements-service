# README: Запуск контейнеров для проекта

Этот документ предоставляет пошаговую инструкцию по установке Docker, Docker Compose, запуску контейнеров и настройке проекта.

---

## Установка Docker

1. Скачайте и выполните скрипт для установки Docker:

    ```bash
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    ```

2. Проверьте, что Docker установлен корректно:

    ```bash
    docker --version
    ```

3. Добавьте текущего пользователя в группу Docker для выполнения команд без `sudo`:

    ```bash
    sudo usermod -aG docker $USER
    ```

    После этого выйдите из системы и войдите снова.

---

## Установка Docker Compose

1. Установите плагин Docker Compose:

    ```bash
    sudo apt-get update
    sudo apt-get install docker-compose-plugin
    ```

2. Проверьте установку Docker Compose:

    ```bash
    docker compose version
    ```

---

## Конфигурация проекта

Создайте файл `docker-compose.yml` в корне проекта со следующим содержимым:

```yaml
docker-compose.yml:
version: "3.8"

services:
  nginx:
    image: dotskinmaksim/nginx:latest
    container_name: nginx-container
    ports:
      - "80:8080"
    restart: unless-stopped
    dns:
      - 172.18.0.3
    networks:
      custom_network:
        ipv4_address: 172.18.0.2
    #working_dir: /var/www/advertisements-service
    #command: php -S 0.0.0.0:8000
  mysql:
    image: dotskinmaksim/mysql:latest
    container_name: mysql-container
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: 123
      MYSQL_DATABASE: ads_database
    restart: unless-stopped
    networks:
      custom_network:
        ipv4_address: 172.18.0.4

  dns:
    image: dotskinmaksim/dns:latest
    container_name: dns-container
    ports:
      - "5353:53/udp"
      - "5353:53/tcp"
    restart: unless-stopped
    networks:
      custom_network:
        ipv4_address: 172.18.0.3

networks:
  custom_network:
    driver: bridge
    ipam:
      config:
        - subnet: 172.18.0.0/24

```

---

## Сборка и запуск контейнеров



1. Запустите контейнеры с помощью Docker Compose:

    ```bash
    docker compose up -d
    ```

2. Проверьте работающие контейнеры:

    ```bash
    docker ps
    ```
3. Поставьте как dns сервер:

    ```bash
     172.18.0.3
    ```
4. Сайт будет доступно по адресу:
   ```bash
     ls2.myads.loc
    ```
---

## Остановка контейнеров

Для остановки контейнеров выполните:

```bash
docker compose down
```

---

## Устранение ошибок

1. Если вы видите ошибку сети, убедитесь, что ваша сеть корректно настроена в секции `networks` файла `docker-compose.yml`.
2. Проверьте логи контейнеров для диагностики:

    ```bash
    docker logs <container_name>
    ```

---

Теперь ваш проект готов к запуску в Docker. Удачной работы!

