<?php

namespace Saman\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppApiControllerTest extends WebTestCase
{
    public function testDisplayflashbag()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/app/api/flashbag');
    }

}
