<?php

namespace AppBundle\Controller;

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
     * @Route("/employee/edit/", name="employee_edit")
     */
    public function employeeEditAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('employee/employee_edit.html.twig');
    }
}