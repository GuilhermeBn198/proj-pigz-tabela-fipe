# 🚗 Vehicle Marketplace API

API RESTful desenvolvida em Symfony para cadastro, listagem e transações de compra e venda de veículos, com base na Tabela FIPE.

---

## 📌 Funcionalidades

- Cadastro de usuários
- Autenticação via token
- Listagem e cadastro de veículos com informações da Tabela FIPE
- Solicitação de compra de veículos
- Aceitação ou rejeição de solicitações de compra
- Histórico de vendas
- População automática de categorias, marcas, modelos e anos a partir da API oficial da Tabela FIPE

---

## 🧱 Entidades principais

- **User**: Representa o usuário do sistema (comprador ou vendedor).
- **Vehicle**: Representa um veículo, com status de venda, dono e interessado.
- **Category**: Categoria do veículo (ex: carro, moto, caminhão).
- **Brand**: Marca do veículo.
- **Model**: Modelo do veículo.
- **Year**: Ano do veículo, com valor de tabela.

---

## 🧰 Tecnologias e Ferramentas

- **Symfony** (v6+)
- **Doctrine ORM**
- **MySQL** (Dockerizado)
- **PHPUnit** (testes unitários)
- **Faker** (população de dados)
- **API Tabela FIPE** (consumo externo)
- **Postman** (coleção de testes de API)

---

## 🔄 Fluxo de Compra e Venda

1. **Cadastro do veículo**:
   - O dono informa a categoria, marca, modelo e ano do veículo.
   - O sistema consulta a Tabela FIPE e vincula o valor médio ao veículo.
   - O veículo é salvo com status `for_sale`.

2. **Solicitação de compra**:
   - Um usuário que **não é o dono** pode solicitar a compra.
   - O veículo passa a ter status `pending` e o campo `requestedBy` é preenchido com o comprador.

3. **Resposta do vendedor**:
   - O dono pode **aceitar** ou **rejeitar** a solicitação.
     - Ao aceitar: status muda para `sold`, o comprador se torna o novo dono, e a data da venda é registrada.
     - Ao rejeitar: o status volta para `for_sale` e o comprador interessado é removido.

---

## 🧪 Testes e Banco de Testes

A aplicação possui testes unitários para os serviços de compra e venda.

### Criar banco de testes e desenvolvimento no MySQL container

#### Dentro do seu container do MySQL (ex: `projfipe-mysql`)

```bash
mysql -u root -p
CREATE DATABASE symfony CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE symfony_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Rodar migrações

```bash
# Banco de desenvolvimento
php bin/console doctrine:migrations:migrate --env=dev

# Banco de testes
php bin/console doctrine:migrations:migrate --env=test
```

Você também pode configurar os .env ou .env.test com a URL de conexão correta(mas eles estão disponíveis no repositório pré-configurados):

```bash
# .env
DATABASE_URL="mysql://root:root@127.0.0.1:3306/symfony?serverVersion=8.0"

# .env.test
DATABASE_URL="mysql://root:root@127.0.0.1:3306/symfony_test?serverVersion=8.0"
```

#### ⚙️ Comando para popular o banco com dados da FIPE

O projeto inclui um comando customizado para buscar dados reais da Tabela FIPE e popular o banco com:

- Categorias (carros, motos, caminhões)

- Marcas para cada categoria

- Modelos para cada marca

- Anos para cada modelo

##### Executar o comando (dentro do container projfipe_php)

```bash
php bin/console fipe:sync
```

Esse comando consome a API oficial da FIPE (via fipe.org.br) e popula as tabelas locais. Ideal para rodar após criar e migrar o banco.

### 🚀 Instruções rápidas

#### Subir containers (MySQL, etc.)

```bash
docker-compose up -d --build
```

#### Entrar no container do projeto e instalar dependências

```bash
docker exec -it projfipe_php bash
composer install
```

#### Rodar testes dentro do container

temos 3 tipos de testes que podemos fazer

- `composer test` para rodar todos os testes da aplicação
- `composer test:unit` para rodar os testes unitários isoladamente
- `composer test:integration` para rodar os testes de integração

## 📫 Contato

Caso tenha dúvidas ou sugestões, sinta-se à vontade para abrir uma issue ou contribuir com pull requests.
