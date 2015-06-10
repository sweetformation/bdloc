<?php

namespace Bdloc\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreditCardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('abonnement', 'choice', array(
                'choices'   => array('M' => 'Mensuel', 'A' => 'Annuel'),
                'mapped' => false,
                'expanded' => true,
                'data' => "A"
                ))
            ->add('creditCardType', null, array(
                "label" => "Type*",
                'attr' => array(
                        'value' => 'visa',
                    )
                ))
            ->add('creditCardNumber', null, array(
                "label" => "Numéro*",
                'attr' => array(
                        'value' => '4916549042671181',
                    )
                ))
            ->add('expirationDate', 'date', array(
                'label' =>"Date d'expiration*",
                'format' =>'MMM-yyyy  d',
                'years' => range(date('Y'), date('Y')+12),
                'days' => array(1),
                'empty_value' => array('year' => 'Année', 'month' => 'Mois', 'day' => false)
                ))
            ->add('codeCVC', null, array(
                "label" => "Code CVC*"
                ))
            ->add('creditCardLastName', null, array(
                "label" => "Nom figurant sur la carte*",
                'attr' => array(
                        'placeholder' => 'NOM',
                    )
                ))
            ->add('creditCardFirstName', null, array(
                "label" => " - ",
                'attr' => array(
                        'placeholder' => 'PRENOM',
                    )
                ))
            ->add('submit', 'submit', array(
                "label" => "Enregistrer"
                ))
            //->setAction($this->generateUrl("bdloc_app_payment_takesubscriptionpayment", array(), true))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\CreditCard'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_creditcard';
    }
}
