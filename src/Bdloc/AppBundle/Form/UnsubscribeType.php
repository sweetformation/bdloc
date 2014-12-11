<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UnsubscribeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'textarea', array(
                'label'=>'Message',
                'mapped' => false,
                'constraints' => array(
                    new Length(array('min' => 20, 'minMessage' => "trop court")),
                    new NotBlank(array('message'=>"???"))
                ),
            ))
            ->add('captcha', 'captcha', array(
                'invalid_message' => 'Captcha invalide',
                ))
            ->add('submit', 'submit', array(
                "label" => "Merci"
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
        return 'bdloc_appbundle_unsubscribe';
    }
}
