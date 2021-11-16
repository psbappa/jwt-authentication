<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CustomApiTestCase extends ApiTestCase
{
//    private TokenStorage $tokenStorage;
//
//    public function __construct(TokenStorage $tokenStorage)
//    {
//        $this->tokenStorage = $tokenStorage;
//    }

    protected function createUser(string $email, string $password): User
    {
        $user = new User();
        $email = rand() . '@gmail.com';
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setName('Test User');
        $user->setUsername(substr($email, 0, strpos($email, '@')));
        $encoded = self::$container->get('security.password_encoder')->encodePassword($user, $password);
//        $user->setPassword('$2y$13$KnApzeic1kwGGl5wd6AmhOIar2i6niakSJLQbV.F/Ca.YbGCiv3wu');
        $user->setPassword($encoded);
        $user->setAdminOnlyProperty('false');
        $user->setPhone('9007279095');
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password)
    {
        $client->request('POST', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ],
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    protected function loginWithReturnUserdata(Client $client, string $email, string $password)
    {
        $user = $client->request('POST', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ],
        ]);
//        dd($user->getContent("token"));
        return json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $user->getContent("token"))[1]))));
    }

    protected function inValidLogin(Client $client, string $email, string $password)
    {
        $client->request('POST', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    protected function createUserAndLogIn(Client $client, string $email, string $password): User
    {
        $user = $this->createUser($email, $password);

        $this->logIn($client, $email, $password);

        return $user;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    protected function showAllUsers(Client $client)
    {
        $client->request('GET', 'api/users', [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $em = self::$container->get('doctrine')->getManager();
        return $em->getRepository(User::class)->findAll();
    }
}