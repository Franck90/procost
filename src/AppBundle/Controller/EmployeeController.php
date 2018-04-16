<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Employee;
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
        $employees = $em->getRepository('AppBundle:Employee')->findAll();

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
     * @Route("/employee/edit/{id}/", name="employee_edit", requirements = {"id"="\d+"})
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
     * @Route("/employee/delete/{id}", name="employee_delete", requirements = {"id"="\d+"})
     */
    public function employeeDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $employeeToDelete = $this->getDoctrine()->getRepository('AppBundle:Employee')->findOneById($id);

        $em->remove($employeeToDelete);
        $em->flush();

        return $this->redirectToRoute('employee');
    }
}