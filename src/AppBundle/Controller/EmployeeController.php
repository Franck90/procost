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
        //$employees = $em->getRepository('AppBundle:Employee')->findAll();

        $employees = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Employee')->findAll(),
            $request->query->get('page', 1),
            10
        );

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
        $em = $this->getDoctrine()->getManager();

        $employeeToUpdate = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        $form = $this->createForm(EmployeeType::class, $employeeToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($employeeToUpdate);
            $em->flush();

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

        $employeeToDesactivate->setActive(false);

        $em->persist($employeeToDesactivate);
        $em->flush();

        return $this->redirectToRoute('employee');
    }

    /**
     * @Route("/employee/{id}/activate/", name="employee_activate", requirements = {"id"="\d+"})
     */
    public function employeeActivateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $employeeToActivate = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        $employeeToActivate->setActive(true);

        $em->persist($employeeToActivate);
        $em->flush();

        return $this->redirectToRoute('employee');
    }

    /**
     * @Route("employee/{id}/detail/", name="employee_detail", requirements= {"id"="\d+"})
     */
    public function employeeDetailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository('AppBundle:Employee')->find($id);

        $detailList = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Detail')->findBy(array('employee' => $employee->getId()), array('date' => 'desc')),
                $request->query->get('page', 1),
                10
        );

        $detail = new Detail();
        $detail->setEmployee($employee);

        $form = $this->createForm(DetailEmployeeType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($detail);
            $em->flush();

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
        $employee = $em->getRepository('AppBundle:Employee')->find($id);
        $employee->setActive(true);

        $detailList = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Detail')->findBy(array('employee' => $employee->getId()), array('date' => 'desc')),
            $request->query->get('page', 1),
            10
        );

        $detail = new Detail();
        $detail->setEmployee($employee);

        $form = $this->createForm(DetailEmployeeType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($detail);
            $em->flush();

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
     * @Route("employee/{id}/detail/desactivate", name="employee_detail_desactivate", requirements= {"id"="\d+"})
     */
    public function employeeDetailDesactivateAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository('AppBundle:Employee')->find($id);
        $employee->setActive(false);

        $detailList = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Detail')->findBy(array('employee' => $employee->getId()), array('date' => 'desc')),
            $request->query->get('page', 1),
            10
        );

        $detail = new Detail();
        $detail->setEmployee($employee);

        $form = $this->createForm(DetailEmployeeType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($detail);
            $em->flush();

            return $this->render("employee_detail", array(
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
     * @Route("employee/{id}/detail/{idDetail}/delete", name="detail_delete", requirements={"id"="\d+", "idDetail"="\d+"})
     */
    public function detailDeleteAction(Request $request, $id, $idDetail)
    {
        $em = $this->getDoctrine()->getManager();
        $detail = $em->getRepository('AppBundle:Detail')->find($idDetail);
        $em->remove($detail);
        $em->flush();


        return $this->employeeDetailAction($request, $id);
    }
}