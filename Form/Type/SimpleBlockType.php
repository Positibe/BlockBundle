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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class SimpleBlockType
 * @package Positibe\Bundle\OrmBlockBundle\Form\Type
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class SimpleBlockType extends AbstractType
{
    private $locales;

    public function __construct($locales)
    {
        $this->locales = $locales;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'title',
                null,
                array(
                    'label' => 'simple_block.form.title_label'
                )
            )
            ->add(
                'body',
                null,
                array(
                    'label' => 'simple_block.form.body_label',
                    'attr' => array(
                        'rows' => 12,
                        'class' => 'inbox-editor inbox-wysihtml5'
                    )
                )
            )
            ->add(
                'locale',
                'choice',
                array(
                    'label' => 'simple_block.form.locale_label',
                    'choices' => array_combine($this->locales, $this->locales)
                )
            )

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Positibe\Bundle\OrmBlockBundle\Entity\SimpleBlock',
                'translation_domain' => 'PositibeOrmBlockBundle'
            )
        );
    }

    public function getParent()
    {
        return 'positibe_abstract_block';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'positibe_simple_block';
    }


} 