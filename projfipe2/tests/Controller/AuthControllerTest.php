<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class AuthControllerTest extends WebTestCase
{
    private $client;
    
    protected function setUp(): void
    {
        $this->client = static::createClient();
        \Doctrine\ORM\Tools\SchemaTool($entityManager)
        ->dropSchema($metadata);
        \Doctrine\ORM\Tools\SchemaTool($entityManager)
        ->createSchema($metadata);
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
        // Cria usuário direto no banco
        $userRepo = static::$container->get(UserRepository::class);
        // ... criar e persistir user1@example.com / senha 123456 ...
        
        $payload = ['email'=>'user1@example.com','password'=>'123456'];
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE'=>'application/json'],
            json_encode($payload)
        );
        
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }
    
    public function testAccessProtectedRoute(): void
    {
        // Simula login de um usuário já persistido
        $userRepo = static::$container->get(UserRepository::class);
        $testUser = $userRepo->findOneByEmail('user1@example.com');
        
        // Symfony 5.1+ tem loginUser() para acelerar login em testes :contentReference[oaicite:1]{index=1}
        $this->client->loginUser($testUser);
        
        $this->client->request('GET','/api/users');
        $this->assertResponseStatusCodeSame(200);
    }
}