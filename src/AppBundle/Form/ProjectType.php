<?php

namespace AppBundle\Form;

use AppBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'Nom'));
        $builder->add('description', TextType::class, array('label' => 'Description'));
        $builder->add('type', ChoiceType::class, array(
            'choices'  => array(
                'CAPEX' => 'CAPEX',
                'OPEX' => 'OPEX',
            ),
        ));
        $builder->add('date', DateType::class, array(
            'label' => 'Date de création',
            'widget' => 'single_text',
        ));
        $builder->add('send', ChoiceType::class, array(
            'choices'  => array(
                'Oui' => 'oui',
                'Non' => 'non',
            ),
        ));
        $builder->add('save', SubmitType::class, array('label' => 'Enregistrer'));

    }

    public function getName()
    {
        return 'project_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }

}