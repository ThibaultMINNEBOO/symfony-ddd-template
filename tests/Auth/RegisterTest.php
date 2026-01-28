<?php

namespace App\Tests\Auth;

use App\Domain\Entity\User;
use App\Infrastructure\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RegisterTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testUserCanRegister(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $buttonCrawlerNode = $crawler->selectButton('Register');

        $form = $buttonCrawlerNode->form();

        $form['registration_form[email]'] = 'root@example.com';
        $form['registration_form[password]'] = 'testpassword';

        $client->submit($form);

        $this->assertQueuedEmailCount(1);
        $message = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($message, 'To', 'root@example.com');

        $this->assertResponseRedirects('/');
        $client->followRedirect();

        $token = $this->getContainer()->get('security.token_storage')->getToken();
        $this->assertNotNull($token);
        $this->assertEquals('root@example.com', $token->getUserIdentifier());
    }

    public function testUserCannotRegisterWithExistingEmail(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'root@example.com',
            'password' => 'testpassword',
        ]);

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $buttonCrawlerNode = $crawler->selectButton('Register');

        $form = $buttonCrawlerNode->form();

        $form['registration_form[email]'] = 'root@example.com';
        $form['registration_form[password]'] = 'anotherpassword';

        $client->submit($form);

        $this->assertSelectorTextContains('li', 'There is already an account with this email');
    }

    public function testUserCannotRegisterWithUnprotectedPassword(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $buttonCrawlerNode = $crawler->selectButton('Register');

        $form = $buttonCrawlerNode->form();

        $form['registration_form[email]'] = 'root@example.com';
        $form['registration_form[password]'] = '123';

        $client->submit($form);

        $this->assertSelectorTextContains('li', 'Your password should be at least 6 characters');
    }

    public function testUserCanVerifyMail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $buttonCrawlerNode = $crawler->selectButton('Register');

        $form = $buttonCrawlerNode->form();

        $form['registration_form[email]'] = 'root@example.com';
        $form['registration_form[password]'] = 'testpassword';

        $client->submit($form);
        $this->assertQueuedEmailCount(1);
        $message = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($message, 'To', 'root@example.com');
        $body = $message->getHtmlBody() ?? $message->getBody();

        preg_match('/https?:\/\/[^"\']+\/verify\/email\?[^"\']+/', $body, $matches);

        $this->assertNotEmpty($matches);
        $verificationUrl = $matches[0];

        $client->request('GET', $verificationUrl);

        $this->assertResponseRedirects('/');
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'root@example.com']);

        $this->assertTrue($user->isVerified());
    }
}
