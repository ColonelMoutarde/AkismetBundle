<?php

namespace Tests\DependencyInjection;

use OpenClassrooms\Akismet\Client\Client;
use OpenClassrooms\Bundle\AkismetBundle\DependencyInjection\OpenClassroomsAkismetExtension;
use OpenClassrooms\Bundle\AkismetBundle\OpenClassroomsAkismetBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Arnaud Lefèvre <arnaud.lefevre@openclassrooms.com>
 */
class OpenClassroomsAkismetExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ExtensionInterface
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var YamlFileLoader
     */
    private $configLoader;

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function NoConfiguration_ThrowException()
    {
        $this->configLoader->load('empty_config.yml');
        $this->container->compile();
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function WithoutBlogUrlConfiguration_ThrowException()
    {
        $this->configLoader->load('without_blog_url_config.yml');
        $this->container->compile();
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function WithoutApiKeyConfiguration_ThrowException()
    {
        $this->configLoader->load('without_api_key_config.yml');
        $this->container->compile();
    }

    /**
     * @test
     */
    public function Configuration()
    {
        $expectedBaseUrl = 'https://api-key.rest.akismet.com/1.1/';
        $this->configLoader->load('config.yml');
        $this->container->compile();
        /** @var Client $client */
        $client = $this->container->get('openclassrooms.akismet.client');
        $rc = new \ReflectionClass($client);
        $rp = $rc->getProperty('guzzle');
        $rp->setAccessible(true);
        /** @var \GuzzleHttp\Client $guzzle */
        $guzzle = $rp->getValue($client);

        $this->assertEquals($expectedBaseUrl, $guzzle->getBaseUrl());
    }

    /**
     * @test
     */
    public function assertAkismetService()
    {
        $this->configLoader->load('config.yml');
        $this->container->compile();

        $this->assertTrue($this->container->has('openclassrooms.akismet.services.akismet_service'));
    }

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new OpenClassroomsAkismetExtension();
        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension('open_classrooms_akismet');
        $this->configLoader = new YamlFileLoader(
            $this->container,
            new FileLocator(__DIR__ . '/Fixtures/Resources/config')
        );
        $bundle = new OpenClassroomsAkismetBundle();
        $bundle->build($this->container);
    }
}
