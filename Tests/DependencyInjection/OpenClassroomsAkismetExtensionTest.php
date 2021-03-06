<?php

namespace OpenClassrooms\Bundle\AkismetBundle\Tests\DependencyInjection;

use OpenClassrooms\Bundle\AkismetBundle\OpenClassroomsAkismetBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Arnaud Lefèvre <arnaud.lefevre@openclassrooms.com>
 */
class OpenClassroomsAkismetExtensionTest extends TestCase
{
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
        $this->configLoader->load('config.yml');
        $this->container->compile();

        $expectedBaseUrl = 'https://key.rest.akismet.com/1.1/';
        $client = $this->container->get('openclassrooms.akismet.client');
        $rc = new \ReflectionClass($client);
        $rp = $rc->getProperty('client');
        $rp->setAccessible(true);
        /** @var \GuzzleHttp\Client $guzzle */
        $guzzle = $rp->getValue($client);

        $this->assertEquals($expectedBaseUrl, $guzzle->getConfig('base_uri'));
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
        $bundle = new OpenClassroomsAkismetBundle();

        $this->container = new ContainerBuilder();
        $this->container->set('request_stack', new RequestStack());
        $this->container->registerExtension($bundle->getContainerExtension());
        $this->container->loadFromExtension('openclassrooms_akismet');

        $this->configLoader = new YamlFileLoader(
            $this->container,
            new FileLocator(__DIR__ . '/Fixtures/Resources/config')
        );
        $bundle->build($this->container);
    }
}
