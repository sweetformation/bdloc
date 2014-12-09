<?php

namespace Bdloc\AppBundle\Form;

use Doctrine\ORM\EntityRepository;
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
            ->add('numPerPage', 'hidden')
            ->add('orderBy', 'hidden')
            ->add('orderDir', 'hidden')
            ->add('keywords')
            ->add('categories', 'choice', array(
                    "choices" => array("Adaptation","Animalier","Aventure","Biographie","Comics","Divers","Drame","Erotique","Heroic Fantasy","Historique","Humour","Independant","Jeunesse","Manga","Policier-Thriller-Polar","Quotidien","Roman graphique","Science-fiction"),
                    "expanded" => true,
                    "multiple" => true,
                ))
            ->add('availability')
            ->add('submit', 'submit', array(
                    "label" => "Filter"
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
