# anotações de techs etc aprendidas/pontuadas durante o desafio

- verificar dependências com **composer install + symfony check:req(caso tenha instalado o symfony cli**
- doctrine
- doctrine orm
- packages/security.yaml para configurar rotas seguras
- middleware de auth é automática no symfony, tudo configurado pelo security.yaml
- lexik:jwt:generate-keypair faz o trabalho de gerar as chaves privada e pública de forma automática.
- por que joguei as rotas de usuario dentro de authcontroller?
- rota api/login_check pelo lexikJWT é automática e ñ precisa de mt config
- autowiring
- sudo chown -R $(whoami) $(pwd) para atribuir todos os arquivos de uma pasta para o meu usuario atual
- **DOCTRINEFIXTURES --> doctrine:fixtures:load**  faz com que possamos criar "mocks" reais no banco de dados(o comando puro apaga todas as entradas do banco, se passar a flag --append, evita isso)
- função dump e dd equivalem a função log do js
- TEM DIFERENÇA ENTRE **use Symfony\Component\Routing\Route;** E **use Symfony\Component\Routing\ANNOTATION\Route;** !!!!! O segundo é usado quando geramos as rotas de forma automática pelo cli!!!
- as nomenclaturas são estritas, não posso nomear algo que não condiz com a classe ou tipo do que estou tentando fazer. Ex: fiz um UserFixtures e nomeei o arquivo apenas User dentro da pasta DataFixtures. O symfony simplesmente não conseguia identificar ele. Parece com Java...
- voteOnAttribute do Voter do symfony é o coração dessa parte de validação, é ele que determina se o usuário atual autenticado tem ou não permissão para realizar tal ação.
- Services: extraímos lógica de negócio para AuthService e UserService, deixando controllers finos e testáveis.
- Testes unitários: escrevemos testes PHPUnit para AuthService e UserService, mockando repositórios, hasher e EntityManager.
- Cobertura de código: configuramos Xdebug e phpunit.xml.dist para gerar relatórios HTML e texto, e adicionamos scripts Composer (test, test:Unit, Test:Integration, coverage, coverage:open).
- Configuramos o banco de testes no mesmo container do banco de dados de dev, ele se chama symfony_teste devemos nos conectar com -> symfony_test_user:symfony_test_password.
- TENTEI INSTALAR o nelmio/api-doc-bundle que dá suporte relativamente simples de configurar para o swagger de forma que a gnt possa documentar toda a api na rota /docs e depois configuramos o secutiry para permitir apenas acesso de usuários admin para a rota.
  - o swagger, no symfony precisa estar instalado junto ao twig-bundle e o symfony/asset, pois é a partir deles que a rota html bonitinha é gerada. depois de instalada, temos q configurar o security para fazer a rota docs ser disponível apenas para quem deve ter acesso(admins) e instalar os assets através do comando **php bin/console assets:install --symlink**
  - simplesmente precisa fazer configurações demais pra poder funcionar legal.



## Em geral, testes unitários devem focar em lógica pura, sem depender de infraestrutura externa (DB, HTTP, etc.). Nesse projeto, isso significa

- Services (e.g. AuthService, UserService) → contêm regras de negócio e hashing, geram tokens, lançam exceções
- Voters (e.g. UserVoter, VehicleVoter) → encapsulam regras de autorização (admin vs. dono)
- Value Objects / Helpers → qualquer classe que implemente lógica independente (p.ex. formatação, cálculos)
- Entidades apenas se tiverem métodos com lógica (não só getters/setters)
- DTOs só se implementarem lógica (conversões, validações custom)
