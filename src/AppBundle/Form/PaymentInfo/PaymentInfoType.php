<?php

namespace AppBundle\Form\PaymentInfo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\PaymentInfo;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IbanValidator;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\Type;
use AppBundle\Classes\MiscellaneousUtils;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\PaymentInfo\PaymentInfoJustPlacePriorType;



class PaymentInfoType extends PaymentInfoJustPlacePriorType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('firstName', TextType::class,[
                    'label'=>'First name',
                    'required'=>false,
                    'attr'=>['maxlength'=>48, 'size'=>48],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>48 /* MUST BE ENCRYPTED. That is why length was reduced from 128 (in database) to 48 for validation */
                        ])
                    ]
                ])
                ->add('secondName', TextType::class,[
                    'label'=>'Second name',
                    'required'=>true,
                    'attr'=>['maxlength'=>48, 'size'=>48],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>48 /* MUST BE ENCRYPTED. That is why length was reduced from 128 (in database) to 48 for validation */
                        ])
                    ]
                ])
                ->add('lastName', TextType::class,[
                    'label'=>'Last name',
                    'required'=>false,
                    'attr'=>['maxlength'=>48, 'size'=>48],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>48 /* MUST BE ENCRYPTED. That is why length was reduced from 128 (in database) to 48 for validation */
                        ])
                    ]
                ])
                ->add('code', TextType::class,[
                    'label'=>'Internal account code of payer.',
                    'required'=>true,
                    'attr'=>['maxlength'=>29, 'size'=>29, 'title'=>'Internal account code of payer inside the IBAN owner for the payment to enterprise.'],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>29 /* private code of owner in internal (internal account code) list of the target (enterprise, plant etc) to payment. */
                        ]),
                        new Type([
                            'type'=>'alnum'
                        ]),
                    ]
                ])
                ->add('country', ChoiceType::class, [
                    'choices' => array_flip(MiscellaneousUtils::COUNTRY_CODES_2),
                    'label'=>'Country code',
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>2 /* Country code */
                        ])
                    ]
                ])
                ->add('region', TextType::class,[
                    'label'=>'Name of region',
                    'required'=>false,
                    'attr'=>['maxlength'=>48, 'size'=>48],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>48 /* Name of region/state etc */
                        ]),
                    ]
                ])
                ->add('city', TextType::class,[
                    'label'=>'City',
                    'required'=>true,
                    'attr'=>['maxlength'=>48, 'size'=>48],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>48 /* Name of region/state etc */
                        ])
                    ]
                ])
                ->add('street', TextType::class,[
                    'label'=>'Street/Ave./Blvd.',
                    'required'=>true,
                    'attr'=>['maxlength'=>48, 'size'=>48],
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>48 /* MUST BE ENCRYPTED. That is why length was reduced from 128 (in database) to 48 for validation */
                        ])
                    ]
                ])
                ->add('house', TextType::class,[
                    'label'=>'House',
                    'attr'=>['maxlength'=>16, 'size'=>16],
                    'required'=>true,
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>16 /* MUST BE ENCRYPTED. That is why length was reduced from 96 (in database) to 16 for validation */
                        ])
                    ]
                ])
                ->add('apartment', TextType::class,[
                    'label'=>'Apartment',
                    'attr'=>['maxlength'=>16, 'size'=>16],
                    'required'=>true,
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>16 /* MUST BE ENCRYPTED. That is why length was reduced from 96 (in database) to 16 for validation */
                        ])
                    ]
                ])
                ->add('iban', TextType::class,[ //will need change on IbanValidator
                    'attr'=>['maxlength'=>29, 'size'=>29],
                    'label'=>'IBAN code', 
                    'required'=>true,
                    'constraints'=>[
                        new Type([
                            'type'=>'alnum'
                        ]),
                        new Iban(),
                    ]
                ])
                ->add('subIban', TextType::class,[ //will need change on IbanValidator
                    'label'=>'Code of the IBAN owner.', 
                    'attr'=>['maxlength'=>29, 'size'=>29],
                    'required'=>false,
                    'constraints'=>[ 
                        new Length([
                            'min'=>2,
                            'max'=>29 /* code EDRPOU/IPP of the target (enterprise, plant etc) to payment.*/
                        ]),
                        new Type([
                            'type'=>'alnum'
                        ]),
                    ]
                ])
                ->add('note', TextareaType::class,[ //text area for notices
                    'label'=>'Notes', 
                    'required'=>false,
                    'attr'=>['maxlength'=>255, 'size'=>255],
                    'constraints'=>[ 
                        new Length([
                            'min'=>0,
                            'max'=>255 /* MUST BE ENCRYPTED. That is why length  was reduced from 384 (in database) to 255 for validation */
                        ])
                    ]
                ]);
    }

}
