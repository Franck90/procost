<?php
//src/AppBundle/Form/EmployeeType.php

namespace AppBundle\Form;

use AppBundle\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Job;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surname', TextType::class, array('label' => 'Nom'));
        $builder->add('name', TextType::class, array('label' => 'Prénom'));
        $builder->add('mail', EmailType::class, array('label' => 'Email'));
        $builder->add('job', EntityType::class, array(
            'label' => 'Métier',
            'mapped' => true,
            'class' => Job::class,
            'choice_label' => function($job){
                return $job->getName();
            }
        ));
        $builder->add('cost', NumberType::class, array('label' => 'Coût journalier (en €)'));
        $builder->add('date', DateType::class, array(
            'label' => 'Date d\'embauche',
            'widget' => 'single_text',
        ));
        $builder->add('url', TextType::class, array('label' => 'Image (url)'));
        $builder->add('active', CheckboxType::class, array(
            'label'    => 'Actif',
            'required' => false
        ));
        $builder->add('save', SubmitType::class, array('label' => 'Enregistrer'));

    }

    public function getName()
    {
        return 'employee_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Employee::class,
        ));
    }

}