<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\SchemaTool;

class AuthControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        // Obtem o entity manager
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Pega os metadados das entidades
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        // Se tiver metadados (entidades mapeadas), recria o schema
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        }
    }

    public function testRegisterSuccess(): void
    {
        $payload = [
            'name'     => 'Teste',
            'email'    => 'test@example.com',
            'password' => '123456',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testRegisterDuplicateEmail(): void
    {
        // Primeiro registro
        $this->testRegisterSuccess();

        // Tenta registrar de novo
        $payload = [
            'name'     => 'Teste',
            'email'    => 'test@example.com',
            'password' => '123456',
        ];
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(409);
    }

    public function testLoginSuccess(): void
    {
        $userRepo = self::getContainer()->get(UserRepository::class);
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $passwordHasher = self::getContainer()->get('security.password_hasher');

        // Cria o usuário
        $user = new \App\Entity\User();
        $user->setEmail('user1@example.com');
        $user->setName('Usuário Teste');

        // Hash da senha
        $hashedPassword = $passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);

        // Persistir no banco
        $entityManager->persist($user);
        $entityManager->flush();

        // Realiza o login
        $payload = ['email' => 'user1@example.com', 'password' => '123456'];
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testAccessProtectedRoute(): void
    {
        $userRepo = self::getContainer()->get(UserRepository::class);
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $passwordHasher = self::getContainer()->get('security.password_hasher');

        // Cria o usuário
        $user = new \App\Entity\User();
        $user->setEmail('user1@example.com');
        $user->setName('Usuário Teste');

        // Define a senha
        $hashedPassword = $passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // Faz login com o usuário
        $this->client->loginUser($user);

        // Acessa a rota protegida
        $this->client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(403);
    }
}