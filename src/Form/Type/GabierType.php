<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 03/11/2017
 * Time: 08:08
 */

namespace App\Form\Type;


use App\Entity\Gabier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GabierType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('pseudo', null, ['required' => false])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Gabier::class);
    }

}