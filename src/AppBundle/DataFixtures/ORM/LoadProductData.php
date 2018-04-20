<?php
//src/AppBundle/DataFixtures/ORM/LoadProductData.php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Detail;
use AppBundle\Entity\Employee;
use AppBundle\Entity\Job;
use AppBundle\Entity\Project;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProductData implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        //Create jobs for employees
        $arrayNameJob = array('Web developer', 'Manager', 'SEO Manager', 'Web designer', 'Java developer', 'Front-end developer', 'Back-end developer' );
        $arrayJob = array();

        for($i = 0 ; $i < count($arrayNameJob) ; $i++)
        {
            $job = new Job();
            $job->setName($arrayNameJob[$i]);
            array_push($arrayJob,$job);
            $manager->persist($job);
        }

        $manager->flush();

        //Create employees
        $bool = true;
        $arrayEmployee = array();

        for($i = 0 ; $i < 25 ; $i++)
        {
            $employee = new Employee();
            $employee->setName("Prénom".$i);
            $employee->setSurname("Nom".$i);
            $employee->setMail("prenom".$i."nom".$i."@gmail.com");
            $employee->setCost(50*$i/2+10);
            $employee->setDate(new \DateTime('2018-04-'.$i.' 00:00:00'));
            $employee->setActive(true);

            if($bool){
                $bool = false;
                $employee->setUrl("https://randomuser.me/api/portraits/men/".$i.".jpg");
            }else{
                $bool = true;
                $employee->setUrl("https://randomuser.me/api/portraits/women/".$i.".jpg");
            }

            $employee->setJob($arrayJob[array_rand($arrayJob)]);
            array_push($arrayEmployee, $employee);
            $manager->persist($employee);
        }

        $manager->flush();

        //Create projects
        $arrayType = array("CAPEX", "OPEX");
        $arrayProject = array();

        for($i = 0 ; $i < 29 ; $i++)
        {
            $project = new Project();
            $project->setName("Projet ".$i);
            $project->setDescription("Description du projet ".$i);
            $project->setType($arrayType[array_rand($arrayType)]);
            $project->setDate(new \DateTime('2018-01-'.$i.' 00:00:00'));

            $project->setSend($bool);
            $bool = !$bool;

            array_push($arrayProject, $project);
            $manager->persist($project);
        }

        $manager->flush();

        //Create details
        $arrayDuration = array();
        for($i = 0 ; $i < 100 ; $i++)
        {
            array_push($arrayDuration,(rand(1,15)));
        }


        for($i = 0 ; $i < 800 ; $i++)
        {
            $detail = new Detail();
            $detail->setDuration($arrayDuration[array_rand($arrayDuration)]);
            $detail->setEmployee($arrayEmployee[array_rand($arrayEmployee)]);
            $detail->setProject($arrayProject[array_rand($arrayProject)]);
            $detail->setDate(new \DateTime('2018-05-'.rand(1,25).' 00:00:00'));

            $manager->persist($detail);
        }
        $manager->flush();
    }
}