<?php

namespace App\Tests\App\Auth;

use App\Infrastructure\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function Symfony\Component\String\s;

class LoginTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function testUserCanLogin(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'root@example.com',
            'password' => 'test'
        ]);

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');

        $buttonCrawlerNode = $crawler->selectButton('Sign in');

        $form = $buttonCrawlerNode->form();

        $form['email'] = 'root@example.com';
        $form['password'] = 'test';

        $client->submit($form);

        $this->assertResponseRedirects('/');
        $token = $this->getContainer()->get('security.token_storage')->getToken();

        $this->assertNotNull($token);
        $this->assertEquals('root@example.com', $token->getUserIdentifier());
    }

    public function testUserWithBadCredentialsCannotLogin(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');

        $buttonCrawlerNode = $crawler->selectButton('Sign in');

        $form = $buttonCrawlerNode->form();

        $form['email'] = 'invalid@email.com';
        $form['password'] = 'invalidpassword';

        $client->submit($form);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();

        $this->assertAnySelectorTextContains('div.alert.alert-danger', s('Invalid credentials.')->ignoreCase());
    }

    public function testWhenAlreadyLoggedInUserAccessLoginPageThenRedirectToHome(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne([
            'email' => 'root@example.com',
            'password' => 'test'
        ]);

        $client->loginUser($user);

        $client->request('GET', '/login');

        $this->assertResponseRedirects('/');
    }
}
