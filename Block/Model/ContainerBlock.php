<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmBlockBundle\Block\Model;

use Sonata\BlockBundle\Model\Block;


/**
 * Class ContainerBlock
 * @package Positibe\Bundle\OrmBlockBundle\Block\Model
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class ContainerBlock extends Block
{
    public function getType()
    {
        return 'sonata.block.service.container';
    }

    /**
     * @param $blocks
     * @return $this
     */
    public function setChildren($blocks)
    {
        $this->children = $blocks;

        return $this;
    }
} 