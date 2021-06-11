<?php

use PHPUnit\Framework\TestCase;
use T3Monitor\T3monitoringClient\Client;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClientTest extends TestCase
{

    /**
     * @var Client
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var Client $client */
        $this->client = GeneralUtility::makeInstance(Client::class);
    }

    /**
     * @test
     */
    public function testInputIpsAndDomainsStarsReturnOneStarOnly()
    {
        $allowedIps = '*';
        $allowedDomains = '*';
        $this->assertEquals('*', $this->client->getAllowedIps($allowedIps, $allowedDomains));

        $allowedIps = '*';
        $allowedDomains = '';
        $this->assertEquals('*', $this->client->getAllowedIps($allowedIps, $allowedDomains));

        $allowedIps = '';
        $allowedDomains = '*';
        $this->assertEquals('*', $this->client->getAllowedIps($allowedIps, $allowedDomains));
    }

    /**
     * @test
     */
    public function testValuesHasPriorityOverStarOverEmpty()
    {
        $allowedIps = '78.47.171.202, 142.250.184.206';
        $allowedDomains = '*';
        /* Expect the IPs to be returned */
        $this->assertCount(
            2,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );

        $allowedIps = '78.47.171.202, 142.250.184.206';
        $allowedDomains = '';
        /* Expect the IPs to be returned */
        $this->assertCount(
            2,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );

        $allowedIps = '*';
        $allowedDomains = 'www.google.com, www.beech.it';
        /* Expect the IPs from the domains to be returned */
        $this->assertGreaterThanOrEqual(
            2,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );

        $allowedIps = '';
        $allowedDomains = 'www.google.com, www.beech.it';
        /* Expect the IPs from the domains to be returned */
        $this->assertGreaterThanOrEqual(
            2,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );
    }

    /**
     * @test
     */
    public function testInputDomainsOnlyReturnsIPv4AndIPv6()
    {
        $allowedIps = '';
        $allowedDomains = 'www.google.com';

        /* Expect the IPs to be returned */
        $this->assertGreaterThanOrEqual(
            1,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );
    }

    /**
     * @test
     */
    public function testInputIpsAndDomainsReturnsBothConcatenated()
    {
        $allowedIps = '78.47.171.202, 142.250.184.206';
        $allowedDomains = 'www.google.com, www.beech.it';

        /* Expect 4 or more IPs (depending on IPv6 implementation and ) to be returned */
        $this->assertGreaterThanOrEqual(
            4,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );
    }

    /**
     * @test
     */
    public function testExpectTypeErrorOnNull()
    {
        $allowedIps = null;
        $allowedDomains = null;

        $this->expectException(TypeError::class);
        $this->client->getAllowedIps($allowedIps, $allowedDomains);
    }

    /**
     * @test
     */
    public function testExpectZeroIpsWithEmptyParameters()
    {
        $allowedIps = '';
        $allowedDomains = '';

        $this->assertEquals(
            '',
            $this->client->getAllowedIps($allowedIps, $allowedDomains)
        );
    }

    /**
     * @test
     */
    public function testExpectBogusDomainNamesToNotBeProcessed()
    {
        $allowedIps = '78.47.171.202, 142.250.184.206';
        $allowedDomains = 'com.google.www, it.beech.www, beech@beech.com, www,beech,it, www/beech/it, jksdafjsdnidmfhsnbsdfkjnjuhasghdsn.nl';

        $this->assertCount(
            2,
            explode(',', $this->client->getAllowedIps($allowedIps, $allowedDomains))
        );
    }

}
