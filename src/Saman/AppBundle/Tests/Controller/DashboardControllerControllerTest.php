<?php

namespace Saman\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerControllerTest extends WebTestCase
{
    public function testShowdashboard()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/dashboard');
    }

}
