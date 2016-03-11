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
use Positibe\Bundle\OrmBlockBundle\Entity\BlockRepositoryInterface;
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

    protected $blockClass = 'PositibeOrmBlockBundle:Block';

    /**
     * @param EntityManager $entityManager
     * @param PublishWorkflowChecker $authorizationChecker
     */
    public function __construct(
        EntityManager $entityManager,
        PublishWorkflowChecker $authorizationChecker
    ) {
        $this->em = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
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
        } elseif ($configuration['template_position']) {
            return $this->findBlockByTemplatePosition($configuration['template_position'], $configuration);
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
        /** @var Block $block */
        $block = $this->getRepository()->findOneByName($name);

        if (!$block) {
            //The block with name '%s' was not found", $name

            return null;
        }

        if (!$this->authorizationChecker->isGranted('VIEW', $block)) {
            //Block '%s' is not published", $block->getName()

            return null;
        }

        $block->setSettings(isset($configuration['settings']) ? $configuration['settings'] : array());

        return $block;
    }

    /**
     * @param $template_position
     * @param $configuration
     * @return null|Block
     */
    public function findBlockByTemplatePosition($template_position, $configuration)
    {
        if (empty($template_position)) {
            //The template_position passed to the block render is empty
            return null;
        }

        $repository = $this->getRepository();
        if ($repository instanceof BlockRepositoryInterface) {
            $blocks = $repository->findByTemplatePosition($configuration);
        } else {
            $blocks = $repository->findBy(
                array('position' => 'ASC', 'updatedAt' => 'DESC')
            );
        }

        if (count($blocks) === 0) {
            //A block with template_position '%s' was not found", $template_position
            return null;
        }

        if (isset($configuration['multiple']) && $configuration['multiple']) {
            $containerBlock = new ContainerBlock();
            /** @var Block $block */
            foreach ($blocks as $block) {
                if (!$this->authorizationChecker->isGranted('VIEW', $block)) {
                    //Block '%s' for the template_position '%s' is not published"
                    continue;
                }
                $containerBlock->addChildren($block);
            }

            return $containerBlock;
        }

        /** @var Block $block */
        foreach ($blocks as $block) {
            if (!$this->authorizationChecker->isGranted('VIEW', $block)) {
                //Block '%s' for the template_position '%s' is not published"
                continue;
            }
            $block->setSettings(isset($configuration['settings']) ? $configuration['settings'] : array());

            return $block;
        }
        ///There is not Block for the template_position '%s' published"
        return null;

    }

    /**
     * @return string
     */
    public function getBlockClass()
    {
        return $this->blockClass;
    }

    /**
     * @param string $blockClass
     */
    public function setBlockClass($blockClass)
    {
        $this->blockClass = $blockClass;
    }

    /**
     * @param string $blockClassName
     * @return EntityRepository
     */
    public function getRepository($blockClassName = null)
    {
        return $this->em->getRepository($blockClassName !== null ? $blockClassName : $this->blockClass);
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

        if (!(isset($configuration['name']) || isset($configuration['template_position']))) {
            return false;
        }

        return true;
    }

} 