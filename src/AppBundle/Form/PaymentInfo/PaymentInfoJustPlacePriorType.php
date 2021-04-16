<?php

namespace AppBundle\Form\PaymentInfo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\PaymentInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class PaymentInfoJustPlacePriorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('place', EntityType::class,[ //Place
                    'label'=>'Place of payment information', 
                    'required'=>true,
                    'class' => 'AppBundle\Entity\Place',
                    'choice_label' => 'name',
                ])
                ->add('priority', IntegerType::class,[ //Small integer priopity value
                    'attr'=>['min'=>0, 'max'=>32767],
                    'label'=>'Priority', 
                    'required'=>true
                ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PaymentInfo::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }


}
