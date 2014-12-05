<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterBookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', 'hidden')
            ->add('orderBy', 'hidden')
            ->add('orderDir', 'hidden')
            ->add('numPerPage', 'hidden')
            ->add('keywords', null, array(
                'label' => "Recherche"
                ))
            ->add('categories', 'entity', array(
                'label' => "Catégorie",
                'class' => 'BdlocAppBundle:Serie',
                'property' => 'style',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $r) {
                    return $r->getSelectList();
                }
                ))
            ->add('availability', null, array(
               'label' => "Disponibilité"
               ))
            /*->add('availability', 'entity', array(
                'label' => "Disponibilité",
                'class' => 'BdlocAppBundle:Book',
                'property' => 'stock',
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $r) {
                    return $r->getSelectDispo();
                }
                ))*/
            ->add('submit', 'submit', array(
                'label' => 'Filtrer'
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\FilterBook'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_filterbook';
    }
}
