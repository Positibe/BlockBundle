<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmBlockBundle\Form\Type;

use Positibe\Bundle\OrmBlockBundle\Entity\Block;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractBlockType
 * @package Positibe\Bundle\OrmBlockBundle\Form\Type
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class AbstractBlockType extends AbstractType
{
    protected $templatePositions;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'templatePosition',
                'choice',
                array(
                    'label' => 'abstract_block.form.block_location_label',
                    'choices' => array_combine($this->templatePositions, $this->templatePositions),
                    'translation_domain' => 'PositibeOrmBlockBundle',
                    'required' => false
                )
            )
            ->add(
                'publishable',
                null,
                array(
                    'label' => 'abstract_block.form.publishable_label',
                    'required' => false,
                    'translation_domain' => 'PositibeOrmBlockBundle'
                )
            )
            ->add(
                'publishStartDate',
                'sonata_type_datetime_picker',
                array(
                    'dp_side_by_side' => true,
                    'dp_use_seconds' => false,
                    'required' => false,
                    'label' => 'abstract_block.form.publish_start_label',
                    'format' => 'EE, dd/MM/yyyy HH:mm',
                    'dp_language' => 'es',
                    'translation_domain' => 'PositibeOrmBlockBundle'
                )
            )
            ->add(
                'publishEndDate',
                'sonata_type_datetime_picker',
                array(
                    'dp_side_by_side' => true,
                    'dp_use_seconds' => false,
                    'required' => false,
                    'label' => 'abstract_block.form.publish_end_label',
                    'format' => 'EE, dd/MM/yyyy HH:mm',
                    'dp_language' => 'es',
                    'translation_domain' => 'PositibeOrmBlockBundle'
                )
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Block $block */
                $block = $event->getData();
                $form = $event->getForm();;
                if (!$block || null === $block->getId()) {
                    $form->add(
                        'name',
                        null,
                        array(
                            'label' => 'abstract_block.form.name_label',
                            'translation_domain' => 'PositibeOrmBlockBundle'
                        )
                    );
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Positibe\Bundle\OrmBlockBundle\Entity\Block'
            )
        );
    }


    /**
     * @return mixed
     */
    public function getTemplatePositions()
    {
        return $this->templatePositions;
    }

    /**
     * @param mixed $templatePositions
     */
    public function setTemplatePositions($templatePositions)
    {
        $this->templatePositions = $templatePositions;
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'positibe_abstract_block';
    }

} 