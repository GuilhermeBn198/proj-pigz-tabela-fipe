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
    docker exec -it projfipe_php bash
    ```

4. Instalar dependências symfony e criar o banco:

    ```bash
    composer install
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

5. Gerar chaves de criptografia AES

    ```bash
    mkdir -p config/jwt
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    ```

6. Sair do container e abrir o navegador

    ```bash
    exit
    http://localhost:8080
    ```
