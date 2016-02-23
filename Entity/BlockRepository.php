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

use Doctrine\ORM\EntityRepository;


/**
 * Class BlockRepository
 * @package Positibe\Bundle\OrmBlockBundle\Entity
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class BlockRepository extends EntityRepository implements BlockRepositoryInterface
{
    public function findByTemplatePosition($configuration)
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('position', 'ASC')
            ->orderBy('updatedAt', 'DESC');

        if (isset($configuration['template_position'])) {
            $qb->where('o.templatePosition = :templatePosition')->setParameter(
                'templatePosition',
                $configuration['template_position']
            );
        }

        return $qb->getQuery()->getResult();
    }
} 