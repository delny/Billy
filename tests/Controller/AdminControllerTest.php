<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 09/02/19
 * Time: 10:59
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testfailed()
    {
        $client = $this->createClient();

        $client->request('GET', '/admin/import');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }

    protected static function getKernelClass()
    {
        return \App\Kernel::class;
    }
}
