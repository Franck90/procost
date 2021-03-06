<?php
//src/AppBundle/Form/JobType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Job;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'Dénomination'));
        $builder->add('save', SubmitType::class, array('label' => 'Enregistrer'));

    }

    public function getName()
    {
        return 'job_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Job::class,
        ));
    }

}