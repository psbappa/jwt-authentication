<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserResourceTest extends CustomApiTestCaseTest
{
    /**
     * @var Client
     */
    protected Client $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateUser()
    {
        $this->createNewUser('admin@gmail.com', '123456');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/');

        $this->assertArrayHasKey('foo', ['foo' => 'baz']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testVisitingWhileLoggedIn()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $this->loginWithUserInfo($this->client, '473285981@gmail.com', '123456');
        // retrieve the test user

        $testUser = $userRepository->findOneByEmail($user->username);

        $this->loginUser($this->client, $testUser->getEmail(), $testUser->getPassword());

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testListUsers()
    {
        $this->client->request('GET', 'api/users', [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $userRepository = static::getContainer()->get(UserRepository::class)->findAll();
        dd($userRepository);
        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testUpdateUsers()
    {
    //  Test by user permission
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user1 = $this->getLoginUserID($this->client, 'testuser@gmail.com', '123456');    //1
        $user2 = $this->getLoginUserID($this->client, '1@gmail.com', '123456');           //4

        $findUserId1 = $userRepository->findOneByEmail($user1->username);
        $findUserId2 = $userRepository->findOneByEmail($user2->username);

        $this->loginUser($this->client, $user1->username, '123456');
        $this->client->request('PUT', '/api/users/'.$findUserId2->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['name' => 'name updated']
        ]);

        $this->assertResponseStatusCodeSame(401, 'only author can updated');

        $this->loginUser($this->client, $user1->username, '123456');
        $this->client->request('PUT', '/api/users/'.$findUserId1->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['name' => 'name updated']
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUpdateUserOnlyByAdmin()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $adminUser = $this->getLoginUserID($this->client, 'testuser@gmail.com', '123456');    //1
        $normalUser = $this->getLoginUserID($this->client, '1@gmail.com', '123456');           //4

        $adminUserId = $userRepository->findOneByEmail($adminUser->username);
        $normalUserId = $userRepository->findOneByEmail($normalUser->username);

        $this->loginUser($this->client, $adminUser->username, '123456');
        $this->client->request('PUT', '/api/users/'.$normalUserId->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['role' => 'ROLE_USER_UPDATE_BY_ADMIN']
        ]);

        $this->assertResponseStatusCodeSame(401, 'Successfully user updated by admin user');

        $this->loginUser($this->client, $normalUser->username, '123456');
        $this->client->request('PUT', '/api/users/'.$adminUserId->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['role' => 'ROLE_ADMIN_UPDATED_BY_NORMAL_USER']
        ]);

        $this->assertResponseStatusCodeSame(401, 'Only admin have permission to change ROLE');
    }

    public function testDataBaseName()
    {
        $em = static::getContainer()->get('doctrine')->getManager()->getConnection()->getDatabase();

        $this->assertEquals('jwt-auth_test', $em);
    }
}
