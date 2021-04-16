<?php

namespace AppBundle\Form\Receipt;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Tariff;
use AppBundle\Entity\Receipt;
use AppBundle\Classes\CheckNewEditReceipt;
use AppBundle\Form\FormTrait;

class ReceiptType extends AbstractType{
    
    use FormTrait;
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tariff', EntityType::class, [
                    // looks for choices from this entity
                    'class' => 'AppBundle:Tariff',
                    // uses the User.username property as the visible option string
                    'choice_label' => function (Tariff $tariff) {
                            return $tariff->getService()->getName().' ( '.$tariff->getUnitValue().' per '.$tariff->getUnitName().' )'.
                                   ' at "'.$tariff->getPlace()->getName().'"';
                    }
                ])
                ->add('valueB',Type\TextType::class,['label'=>'Beginning value',
                    'required'=>false,
                    'empty_data'=>0,
                    'constraints'=>[
                        new Constraints\Length(['max'=>12]),
                        new Constraints\Type(['type'=>'numeric']),
                    ]])
                ->add('valueE', Type\TextType::class,['label'=>'Ending value',
                    'required'=>false,
                    'empty_data'=>0,
                    'constraints'=>[
                        new Constraints\Length(['max'=>12]),
                        new Constraints\Type(['type'=>'numeric']),
                    ]])
                ->add('value', Type\TextType::class,['label'=>'Value',
                    'required'=>false,
                    'constraints'=>[
                        new Constraints\NotBlank(),
                        new Constraints\Length(['max'=>12]),
                        new Constraints\Type(['type'=>'numeric']),
                        new Constraints\GreaterThan(['value'=>0]),
                    ]])
                ->add('total',Type\TextType::class,['label'=>'Total',
                    'required'=>false,
                    'constraints'=>[
                        new Constraints\NotBlank(),
                        new Constraints\Length(['max'=>15]),
                        new Constraints\Type(['type'=>'numeric']),
                        new Constraints\GreaterThan(['value'=>0]), 
                    ]])
                ->add('calculate', Type\CheckboxType::class,['label'=>'Auto calculate if empty','mapped'=>false, 'required'=>null])
                ->add($this->getDefaultDateTimeField('dateB', $builder,['label'=>'Date begin']))
                ->add($this->getDefaultDateTimeField('dateE', $builder,['label'=>'Date end']))
                ->add('save', Type\SubmitType::class, ['label'=>'Save data']);
                
        $builder->addEventListener(Form\FormEvents::PRE_SUBMIT, function (Form\FormEvent $event) {
            ( new CheckNewEditReceipt($event) )->preCheck();
        });
                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Receipt::class,
        ));
        
        $resolver->setRequired('entity_manager');
        
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_receipt';
    }

    
}
