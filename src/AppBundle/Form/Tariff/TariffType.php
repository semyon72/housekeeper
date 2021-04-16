<?php

namespace AppBundle\Form\Tariff;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;
use AppBundle\Form\FormTrait;


class TariffType extends AbstractType{
    
    use FormTrait;
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('unitName',null,['label'=>'Unit'])
                ->add('unitValue',Type\TextType::class,['label'=>'Price by unit',
                    'constraints'=>[
                        new Constraints\Length(['max'=>8]),
                        new Constraints\Type(['type'=>'numeric']),
                    ]] )
                ->add($this->getDefaultDateTimeField('dateB', $builder, ['label'=>'Date begin']))
                ->add($this->getDefaultDateTimeField('dateE', $builder, ['label'=>'Date end', 'required'=> false]))
                ->add('place', EntityType::class, ['class'=>'AppBundle:Place', 'choice_label'=>'name', 'label'=>'Place description', 'required'=>'required'])
                ->add('service', EntityType::class, ['class'=>'AppBundle:Service', 'choice_label'=>'name', 'label'=>'Service description', 'required'=>'required'])
                ->add('save', Type\SubmitType::class,['label'=>'Save data']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tariff'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tariff';
    }


}
