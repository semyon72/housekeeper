<?php

namespace AppBundle\Form\Counter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;
use AppBundle\Form\FormTrait;


class CounterType extends AbstractType{
    
    use FormTrait;
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', Type\TextType::class,['label'=>'Value',
                    'constraints'=>[
                        new Constraints\Length(['max'=>12]),
                        new Constraints\Type(['type'=>'numeric']),
                    ]])
                ->add('valueName',null,['label'=>'Value description'])
                ->add($this->getDefaultDateTimeField('onDate', $builder))
                ->add('recalculate', Type\CheckboxType::class,['mapped'=>false,'required'=>false, 'label'=>'Recalculat at the end of month'])
                ->add('service', EntityType::class, [
                        // looks for choices from this entity
                        'class' => 'AppBundle:Service',
                        // uses the User.username property as the visible option string
                        'choice_label' => 'name'
                        ])
                ->add('place', EntityType::class, [
                        // looks for choices from this entity
                        'class' => 'AppBundle:Place',
                        // uses the User.username property as the visible option string
                        'choice_label' => 'name'
                        ])
                ->add('save', Type\SubmitType::class, ['label'=>'Save data']);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Counter'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_counter';
    }


}
