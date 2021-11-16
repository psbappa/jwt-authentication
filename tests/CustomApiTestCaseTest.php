<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CustomApiTestCaseTest extends ApiTestCase
{
    public function createNewUser(string $email, string $password): User
    {
        $user = new User();
//        $email = rand() . '@gmail.com';
        $email = 'admin@gmail.com';
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setName('Admin');
        $user->setUsername(substr($email, 0, strpos($email, '@')));
        $encoded = static::getContainer()->get('security.password_encoder')->encodePassword($user, $password);
//        $user->setPassword('$2y$13$KnApzeic1kwGGl5wd6AmhOIar2i6niakSJLQbV.F/Ca.YbGCiv3wu');
        $user->setPassword($encoded);
        $user->setPlainPassword($encoded);
        $user->setAdminOnlyProperty('false');
        $user->setPhone('9007279095');

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function loginWithUserInfo(Client $client, string $email, string $password)
    {
        $client = static::createClient();
        $user = $client->request('POST', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ],
        ]);

        return json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $user->getContent("token"))[1]))));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function loginUser(Client $client, string $email, string $password)
    {
        $client->request('GET', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function getLoginUserID(Client $client, string $email, string $password)
    {
        $user = $client->request('POST', 'api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ],
        ]);

        return json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $user->getContent("token"))[1]))));
    }
}
