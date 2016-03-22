PositibeOrmBlockBundle
======================

This bundle provides some feature that allow you to store the block using Doctrine ORM. This bundle is based on Symfony CmfBlockBundle

Installation
------------

To install the bundle just add the dependent bundles:

    php composer.phar require positibe/orm-block-bundle

Next, be sure to enable the bundles in your application kernel:

    <?php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            // Dependency (check that you don't already have this line)
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            // Vendor specifics bundles
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Positibe\Bundle\OrmBlockBundle\PositibeOrmBlockBundle(),

            // ...
        );
    }

Configuration
=============

Add to your app/config/config.yml the basic configuration:

    # app/config/config.yml
    sonata_block:
        default_contexts: ~
        blocks:
            sonata.block.service.text:
            sonata.block.service.menu:
            # Here you must put all your active block services

Add to your parameters the `locales` and `block_locations` available for your applications.

    # app/config/parameters.yml and app/config/parameters.yml.dist
    parameters:
        locales: [es, en, fr]
        block_locations: [article-1, article-2]

**Caution:**: This bundle use the timestampable, sluggable and translatable and sortable extension of GedmoDoctrineExtension. Be sure that you have the listeners for this extensions enable. You can also to use StofDoctrineExtensionBundle.

For more information see the Check out the documentation on [http://sonata-project.org/bundles/block/master/doc/index.html](http://sonata-project.org/bundles/block/master/doc/index.html) and Symfony CmfBlockBundle to have an idea.