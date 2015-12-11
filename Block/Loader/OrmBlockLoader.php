<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmBlockBundle\Block\Loader;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Positibe\Bundle\OrmBlockBundle\Block\Model\ContainerBlock;
use Positibe\Bundle\OrmBlockBundle\Entity\Block;
use Psr\Log\LoggerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Model\EmptyBlock;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;


/**
 * Class OrmBlockLoader
 * @package Positibe\Bundle\OrmBlockBundle\Block
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class OrmBlockLoader implements BlockLoaderInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PublishWorkflowChecker
     */
    private $authorizationChecker;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManager $entityManager
     * @param PublishWorkflowChecker $authorizationChecker
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManager $entityManager,
        PublishWorkflowChecker $authorizationChecker,
        LoggerInterface $logger = null
    ) {
        $this->em = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->logger = $logger;
    }

    /**
     * @param mixed $configuration
     * @return BlockLoaderInterface|void
     *
     * @throws \Sonata\BlockBundle\Exception\BlockNotFoundException if no block with that name is found
     */
    public function load($configuration)
    {
        if (!$block = $this->findBlock($configuration)) {
            $block = new EmptyBlock();
            $block->setId(uniqid());
            $block->setType('sonata.block.service.empty');
            $block->setEnabled(true);
            $block->setCreatedAt(new \DateTime);
            $block->setUpdatedAt(new \DateTime);
        }

        if (isset($configuration['settings'])) {
            $block->setSettings(array_merge($block->getSettings(), $configuration['settings']));
        }

        return $block;
    }

    /**
     * @param $configuration
     * @return null|BlockInterface
     */
    public function findBlock($configuration)
    {
        if (isset($configuration['name'])) {
            return $this->findBlockByName($configuration['name'], $configuration);
        } elseif ($configuration['location']) {
            return $this->findBlockByLocation($configuration['location'], $configuration);
        } else {
            return null;
        }
    }

    /**
     * @param $name
     * @param $configuration
     * @return null|Block
     */
    public function findBlockByName($name, $configuration)
    {
        if (empty($name)) {
            $this->log("The name passed to the block render is empty");

            return null;
        }
        /** @var Block $block */
        $block = $this->getRepository()->findOneBy(array('name' => $name));

        if (!$block) {
            $this->log(sprintf("The block with name '%s' was not found", $name));

            return null;
        }

        if (!$this->authorizationChecker->isGranted('VIEW', $block)) {
            $this->log(sprintf("Block '%s' is not published", $block->getName()));

            return null;
        }
        $block->setSettings(isset($configuration['settings']) ? $configuration['settings'] : array());

        return $block;
    }

    /**
     * @param $message
     */
    public function log($message)
    {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }

    /**
     * @param string $blockClassName
     * @return EntityRepository
     */
    public function getRepository($blockClassName = 'PositibeOrmBlockBundle:Block')
    {
        return $this->em->getRepository($blockClassName);
    }

    /**
     * @param $location
     * @param $configuration
     * @return null|Block
     */
    public function findBlockByLocation($location, $configuration)
    {
        if (empty($location)) {
            $this->log("The location passed to the block render is empty");

            return null;
        }

        $blocks = $this->getRepository()->findBy(
            array('blockLocation' => $location),
            array('position' => 'ASC', 'updatedAt' => 'DESC')
        );

        if (count($blocks) === 0) {
            $this->log(sprintf("A block with location '%s' was not found", $location));

            return null;
        }

        if (isset($configuration['multiple']) && $configuration['multiple']) {
            $containerBlock = new ContainerBlock();
            /** @var Block $block */
            foreach ($blocks as $block) {
                if (!$this->authorizationChecker->isGranted('VIEW', $block)) {
                    $this->log(sprintf("Block '%s' for the location '%s' is not published", $block->getName(), $location));
                    continue;
                }
                $containerBlock->addChildren($block);
            }

            return $containerBlock;
        }

        /** @var Block $block */
        foreach ($blocks as $block) {
            if (!$this->authorizationChecker->isGranted('VIEW', $block)) {
                $this->log(sprintf("Block '%s' for the location '%s' is not published", $block->getName(), $location));
                continue;
            }
            $block->setSettings(isset($configuration['settings']) ? $configuration['settings'] : array());

            return $block;
        }
        $this->log(sprintf("There is not Block for the location '%s' published", $block->getName(), $location));

        return null;

    }

    /**
     * @param mixed $configuration
     * @return bool
     */
    public function support($configuration)
    {
        if (!is_array($configuration)) {
            return false;
        }

        if (!(isset($configuration['name']) || isset($configuration['location']))) {
            return false;
        }

        return true;
    }

} 