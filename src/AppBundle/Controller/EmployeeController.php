<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Detail;
use AppBundle\Entity\Employee;
use AppBundle\Form\DetailEmployeeType;
use AppBundle\Form\EmployeeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class EmployeeController extends Controller
{
    /**
     * @Route("/employee/", name="employee")
     */
    public function employeeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $employees = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Employee')->findAll(),
            $request->query->get('page', 1),
            10
        );

        if(!$employees){
            throw $this->createNotFoundException();
        }

        return $this->render('employee/employee.html.twig', array(
                "employees" => $employees)
        );
    }

    /**
     * @Route("/employee/new/", name="employee_new")
     */
    public function employeeNewAction(Request $request)
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Employé ajouté avec succès");

            return $this->redirectToRoute('employee');
        }

        return $this->render('employee/employee_new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/employee/{id}/edit/", name="employee_edit", requirements = {"id"="\d+"})
     */
    public function employeeEditAction(Request $request, $id)
    {
        $employeeToUpdate = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        if(!$employeeToUpdate)
        {
            throw $this->createNotFoundException();
        }

        if($employeeToUpdate->getActive() == false)
        {
            $this->get('session')->getFlashBag()->add('error', "Employé archivé, edition de son profil impossible");

            return $this->redirectToRoute('employee');
        }

        $form = $this->createForm(EmployeeType::class, $employeeToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($employeeToUpdate);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Edition de l'employé réalisé avec succès");

            return $this->redirectToRoute('employee');
        }

        return $this->render('employee/employee_edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
        ));
    }

    /**
     * @Route("/employee/{id}/desactivate/", name="employee_desactivate", requirements = {"id"="\d+"})
     */
    public function employeeDesactivateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $employeeToDesactivate = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        if(!$employeeToDesactivate){
            throw $this->createNotFoundException();
        }

        $employeeToDesactivate->setActive(false);

        $em->persist($employeeToDesactivate);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Employé archivé avec succès");

        return $this->redirectToRoute('employee');
    }

    /**
     * @Route("/employee/{id}/activate/", name="employee_activate", requirements = {"id"="\d+"})
     */
    public function employeeActivateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $employeeToActivate = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        if(!$employeeToActivate){
            throw $this->createNotFoundException();
        }

        $employeeToActivate->setActive(true);

        $em->persist($employeeToActivate);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Employé activé avec succès");

        return $this->redirectToRoute('employee');
    }

    /**
     * @Route("employee/{id}/detail/", name="employee_detail", requirements= {"id"="\d+"})
     */
    public function employeeDetailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository('AppBundle:Employee')->find($id);

        if(!$employee){
            throw $this->createNotFoundException();
        }

        $detailList = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Detail')->findBy(array('employee' => $employee->getId()), array('date' => 'desc')),
                $request->query->get('page', 1),
                10
        );

        if(!$detailList){
            throw $this->createNotFoundException();
        }

        $detail = new Detail();
        $detail->setEmployee($employee);

        $form = $this->createForm(DetailEmployeeType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($detail->getProject()->getSend() == true || $detail->getEmployee()->getActive() == false)
            {
                $this->get('session')->getFlashBag()->add('error', "Ajout de temps impossible, le projet est livré ou l'employé est archivé");

                return $this->redirectToRoute("employee_detail", array(
                    'id' => $id
                ));
            }

            $em->persist($detail);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Temps de travail ajouté avec succès");

            return $this->redirectToRoute("employee_detail", array(
                'id' => $id
            ));
        }

        return $this->render('employee/employee_detail.html.twig', array(
            'employee' => $employee,
            'detailList' => $detailList,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("employee/{id}/detail/activate", name="employee_detail_activate", requirements= {"id"="\d+"})
     */
    public function employeeDetailActivateAction(Request $request, $id){

        $em = $this->getDoctrine()->getManager();
        $employeeToActivate = $em->getRepository('AppBundle:Employee')->find($id);
        $employeeToActivate->setActive(true);

        if(!$employeeToActivate){
            throw $this->createNotFoundException();
        }

        $em->persist($employeeToActivate);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Employé activé avec succès");

        return $this->redirectToRoute('employee_detail', array(
            "id" => $id
        ));
    }

    /**
     * @Route("employee/{id}/detail/desactivate", name="employee_detail_desactivate", requirements= {"id"="\d+"})
     */
    public function employeeDetailDesactivateAction(Request $request, $id){

        $em = $this->getDoctrine()->getManager();
        $employeeToDesactivate = $em->getRepository('AppBundle:Employee')->find($id);
        $employeeToDesactivate->setActive(false);

        if(!$employeeToDesactivate){
            throw $this->createNotFoundException();
        }

        $em->persist($employeeToDesactivate);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Employé archivé avec succès");

        return $this->redirectToRoute('employee_detail', array(
            "id" => $id
        ));
    }

    /**
     * @Route("employee/{id}/detail/{idDetail}/delete", name="detail_employee_delete", requirements={"id"="\d+", "idDetail"="\d+"})
     */
    public function EmployeeDetailDeleteAction(Request $request, $id, $idDetail)
    {
        $em = $this->getDoctrine()->getManager();
        $detailToDelete = $em->getRepository('AppBundle:Detail')->find($idDetail);

        if(!$detailToDelete){
            throw $this->createNotFoundException();
        }

        if($detailToDelete->getProject()->getSend() == true || $detailToDelete->getEmployee()->getActive() == false)
        {
            $this->get('session')->getFlashBag()->add('error', "Suppression impossible, le projet est livré ou l'employé est archivé");

            return $this->redirectToRoute('employee_detail', array(
                    "id" => $id)
            );
        }

        $em->remove($detailToDelete);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Suppression du temps de travail sur un projet réalisé avec succès");

        return $this->redirectToRoute('employee_detail', array(
            "id" => $id
        ));
    }

    /**
     * @Route("/employee/{id}/detail/edit/", name="employee_detail_edit", requirements = {"id"="\d+"})
     */
    public function employeeDetailEditAction(Request $request, $id)
    {
        $employeeToUpdate = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        if(!$employeeToUpdate){
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EmployeeType::class, $employeeToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($employeeToUpdate);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Edition de l'employé réalisé avec succès");

            return $this->redirectToRoute('employee_detail', array(
                'id' => $id
            ));
        }

        return $this->render('employee/employee_edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
        ));
    }
}