<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\File;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Config\Definition\Builder\find;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user',EntityType::class, [
                'class' => User::class,
                'choice_label' =>'id',
            ])
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('hobby')
            ->add('address')
            ->add('image', FileType::class,[
        'label'=> 'Image file',
        'mapped'=> false,
        'required'=>false,
        'constraints'=>[
            new File([
                'maxSize'=>'1024k',
                'mimeTypesMessage'=>'Please upload a valid image',
            ])
        ]
    ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
