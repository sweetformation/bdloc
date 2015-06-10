<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditInfoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                "label" => "Pseudo*"
                ))
            ->add('email', 'email', array(
                "label" => "Email*"
                ))
            ->add('firstName', 'text', array(
                "label" => "Nom*"
                ))
            ->add('lastName', 'text', array(
                "label" => "Prénom*"
                ))
            ->add('address', 'text', array(
                "label" => "Adresse (num, rue, app)*"
                ))
            ->add('zip', 'text', array(
                "label" => "Code Postal*"
                ))
            ->add('phone', 'text', array(
                "label" => "Téléphone*"
                ))
            ->add('submit', 'submit', array(
                "label" => "Modifier"
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_editinfo';
    }
}
