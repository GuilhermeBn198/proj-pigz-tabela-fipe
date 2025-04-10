# Projeto Dockerizado Symfony + Nginx

## Pré-requisitos

- Docker
- Docker Compose

## Inicialização

1. Criar o projeto Symfony (se ainda não existir):

    ```bash
        composer create-project symfony/website-skeleton symfony
    ```

2. Build e up:

    ```bash
    docker-compose up -d --build
    ```

3. acessar container PHP:

    ```bash
    docker exec -it php_fpm bash
    ```

4. Instalar dependências symfony e criar o banco:

    ```bash
        composer install
        php bin/console doctrine:database:create
        php bin/console doctrine:migrations:migrate
    ```

5. Abra no navegador:

    ```bash
    http://localhost:8080
    ```
