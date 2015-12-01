<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmBlockBundle\Entity;


/**
 * Class BlockRepositoryInterface
 * @package Positibe\Bundle\OrmBlockBundle\Entity
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
interface BlockRepositoryInterface
{
    public function findBlockByName($name);

    public function findBlocksByLocation($location);
}