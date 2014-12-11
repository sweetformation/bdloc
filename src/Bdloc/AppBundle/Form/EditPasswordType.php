<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

//use Bdloc\AppBundle\Form\ChangePasswordType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class EditPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('oldPassword', new ChangePasswordType(), array(
            //    'label'=>'Mot de passe actuel',
            //    'mapped' => false
            //    ))
            ->add('currentpassword', 'password', array(
                'label'=>'Mot de passe actuel',
                'mapped' => false, 
            ))
            /*->add('currentpassword', 'password', array(
                'label'=>'Mot de passe actuel',
                'mapped' => false,
                'constraints' => new UserPassword(array('message' => 'Mot de passe invalide')),
                'validation_groups' => array('Default') 
            ))*/
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options'  => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Nouveau mot de passe (confirmation)'),
                ))
            ->add('submit', 'submit', array(
                "label" => "Enregistrer"
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
        return 'bdloc_appbundle_editpassword';
    }
}
