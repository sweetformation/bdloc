<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterBookPaginationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', null, array(
                'label' => "N° de page"
                ))
            ->add('orderBy', 'choice', array(
                'choices' => array('dateCreated' => 'date de création'),
                'label' => "Classé par"
                ))
            ->add('orderDir', 'choice', array(
                'choices' => array('asc' => 'Ascendant', 'desc' => 'Descendant'),
                'label' => "Ordonné par ordre"
                ))
            ->add('numPerPage', 'choice', array(
                'choices' => array(10 => 10, 20 => 20),
                'label' => "Affichage par page"
                ))
            ->add('keywords', 'hidden')
            ->add('categories', 'hidden')
            ->add('availability', 'hidden')
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
        return 'bdloc_appbundle_filterbookpagination';
    }
}
