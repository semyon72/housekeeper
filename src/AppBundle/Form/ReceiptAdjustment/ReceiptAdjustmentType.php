<?php

namespace AppBundle\Form\ReceiptAdjustment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\Validator\Constraints;
use AppBundle\Entity\Tariff;
use AppBundle\Form\FormTrait;
use AppBundle\Entity\ReceiptAdjustment;
use AppBundle\Repository\TariffRepository;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Description of ReceiptAdjustmentType
 *
 * Generated: Mar 09, 2020 11:16:15 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReceiptAdjustmentType extends AbstractType{
    
    use FormTrait;
    
    protected $currentReceipt = null;
    
    protected function getTariffQueryBuilder(TariffRepository $er){
        return $er->createQueryBuilder('t')
                ->andWhere('t.place = :place')
                ->andWhere('t.service = :service')
                ->setParameters([
                    'place'=>$this->currentReceipt->getTariff()->getPlace()->getId(),
                    'service'=>$this->currentReceipt->getTariff()->getService()->getId()
                ]);
    } 
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->currentReceipt = $options['current_receipt'];
         
        $builder
            ->add('tariff', EntityType::class, [
                'required'=> false,
                // looks for choices from this entity
                'class' => Tariff::class,
                // uses the User.username property as the visible option string
                'choice_label' => function (Tariff $tariff) {
                        $result = $tariff->getService()->getName().' ( '.$tariff->getUnitValue().' per '.$tariff->getUnitName().' )'.
                               ' at "'.$tariff->getPlace()->getName().'"';
                        if ( !is_null($tariff->getDateE()) ){
                            $result .= ' [CLOSED AT '.$tariff->getDateE()->format('Y-M-d H:i:s').']';
                        }
                        return $result;
                },
                'query_builder' => function(TariffRepository $er){
                    return $this->getTariffQueryBuilder($er);
                },
                'group_by' => function(Tariff $entity, $key, $value) {
                    return 'Place: '.$entity->getPlace()->getName().', Service: '.$entity->getService()->getName();
                }
            ])
            ->add('calculateForTariff', SubmitType::class,[
                'label'=>'Calculate'
                ])
            ->add('valueB',TextType::class,['label'=>'Value start',
                'required'=>false,
                'empty_data'=>0.000,
                'constraints'=>[
                    new Constraints\Length(['max'=>12]),
                    new Constraints\Type(['type'=>'numeric']),
                ]])
            ->add('valueE', TextType::class,['label'=>'Value up to',
                'required'=>false,
                'empty_data'=>0.000,
                'constraints'=>[
                    new Constraints\Length(['max'=>12]),
                    new Constraints\Type(['type'=>'numeric']),
                ]])
            ->add('value', TextType::class,['label'=>'Diff. / Value',
                'required'=>false,
                'empty_data'=>0.000,
                'constraints'=>[
                    new Constraints\Length(['max'=>12]),
                    new Constraints\Type(['type'=>'numeric']),
                ]])
            ->add('total',TextType::class,['label'=>'Total',
                'required'=>false,
                'constraints'=>[
                    new Constraints\NotBlank(),
                    new Constraints\Length(['max'=>15]),
                    new Constraints\Type(['type'=>'numeric']) 
                ]])
            ->add($this->getDefaultDateTimeField('dateB', $builder,['label'=>'From']))
            ->add($this->getDefaultDateTimeField('dateE', $builder,['label'=>'To']))
            ->add('note', TextareaType::class,['label'=>'Notes',
                'attr'=>['maxlength'=>255],
                'empty_data'=>'',
                'required'=>false,
                'constraints'=>[
                    new Constraints\Length(['max'=>255])
                ]]);
            
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function(FormEvent $event){
                if ( is_null($this->currentReceipt) ){
                    return;
                }
                $adjustment = $event->getData();
                $adjustment->setDateB(clone $this->currentReceipt->getDateB());
            }
        );
                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReceiptAdjustment::class,
        ));
        
        $resolver->setRequired('current_receipt');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_'.parent::getBlockPrefix();
    }

    
}
