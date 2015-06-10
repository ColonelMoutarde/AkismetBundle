<?php

namespace OpenClassrooms\Bundle\AkismetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Arnaud Lefèvre <arnaud.lefevre@openclassrooms.com>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_classrooms_akismet');
        $rootNode->children()
            ->scalarNode('api_key')->isRequired()->end()
            ->scalarNode('blog_url')->isRequired()->end()
            ->end();

        return $treeBuilder;
    }
}
