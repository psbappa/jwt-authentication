<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Test\CustomApiTestCase;

class UserResourceTest extends CustomApiTestCase
{
    public function testCreateUser(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'u1@gmail.com',
                'password' => '123456'
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->logIn($client, 'u1@gmail.com', '123456');
    }

    public function testCreateLogin()
    {
        $client = self::createClient();

        $user = new User();
        $email = rand() . '@gmail.com';
        $user->setEmail('aqwsed@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setName('Test User');
        $user->setUsername('testUser');
        $encoded = self::$container->get('security.password_encoder')->encodePassword($user, '123456');
//        $user->setPassword('$2y$13$KnApzeic1kwGGl5wd6AmhOIar2i6niakSJLQbV.F/Ca.YbGCiv3wu');
        $user->setPassword($encoded);
        $user->setAdminOnlyProperty('false');
        $user->setPhone('9007279095');
//        dd($user);
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $client->request('POST', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'aqwsed@gmail.com',
                'password' => '123456'
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    public function testLogin()
    {
        $client = self::createClient();
        $this->logIn($client, '684494786@gmail.com', '123456');
        $this->assertResponseStatusCodeSame(200);

//        $this->inValidLogin($client, 'query@gmail.com', '1234567');
//        $this->assertResponseStatusCodeSame(401);
    }

    public function testListUsers()
    {
        $client = self::createClient();

        $client->request('GET', 'api/users', [
            'headers' => ['Content-Type' => 'application/json']
        ]);

//        dd($this->showAllUsers($client));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testUpdateUser()
    {
        $client = self::createClient();

//        Test by user permission
//        $user1 = $this->createUser('11111111@gmail.com', '123456');
//        $user2 = $this->createUser('22222222@gmail.com', '123456');
//
//        $this->logIn($client, '22222222@gmail.com', '123456');
//        $client->request('PUT', '/api/users/'.$user1->getId(), [
//            'headers' => ['Content-Type' => 'application/json'],
//            'json' => ['name' => 'name updated']
//        ]);
//        $this->assertResponseStatusCodeSame(403, 'only author can updated');
//        var_dump($client->getResponse()->getContent(true));
//
//        $this->logIn($client, '22222222@gmail.com', '123456');
//        $client->request('PUT', '/api/users/'.$user2->getId(), [
//            'headers' => ['Content-Type' => 'application/json'],
//            'json' => ['name' => 'name updated']
//        ]);
//        $this->assertResponseStatusCodeSame(401);

//        Single user testing
        $client = self::createClient();
        $user = $this->loginWithReturnUserdata($client, '684494786@gmail.com', '123456');
//        dd($user);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($user->username);

        $client->request('GET', '/api/users/'.$testUser->getId(), [
            'json' => [
                'username' => '684494786'
            ]
        ]);
        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains([
//            'username' => 'newusername'
//        ]);
    }

    public function testGetUser()
    {
        $client = self::createClient();
        $user = $this->loginWithReturnUserdata($client, '684494786@gmail.com', '123456');
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($user->username);
        $testUser->setPhone('55555555');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/users/'.$testUser->getId());
        $this->assertJsonContains([
            'username' => '684494786'
        ]);
    }


}
