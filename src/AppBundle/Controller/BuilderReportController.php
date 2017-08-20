<?php
/**
 * Created by PhpStorm.
 * User: atempa
 * Date: 9/08/17
 * Time: 12:47 PM
 */

namespace AppBundle\Controller;

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BuilderReportController extends Controller
{

    /**
     * @Route("/sales/category/product/waiter/day", name="export_sales_category_product_waiter_day_excel")
     * @Method("GET")
     */
    public function getSalesByCategoryProductForWaiterDayAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByCategoryProductAndWaiter(new \DateTime('-1 day'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasMeseroxCategoriaProducto")
            ->setSubject("VentasMeseroxCategoriaProducto");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasMeseroxCategoriaProducto.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/category/product/waiter/week", name="export_sales_category_product_waiter_week_excel")
     * @Method("GET")
     */
    public function getSalesByCategoryProductForWaiterWeekAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByCategoryProductAndWaiter(new \DateTime('-1 week'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasMeseroxCategoriaProducto")
            ->setSubject("VentasMeseroxCategoriaProducto");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasMeseroxCategoriaProducto.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/category/product/waiter/month", name="export_sales_category_product_waiter_month_excel")
     * @Method("GET")
     */
    public function getSalesByCategoryProductForWaiterMonthAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByCategoryProductAndWaiter(new \DateTime('-1 month'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasMeseroxCategoriaProducto")
            ->setSubject("VentasMeseroxCategoriaProducto");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasMeseroxCategoriaProducto.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/product/waiter/day", name="export_sales_product_waiter_day_excel")
     * @Method("GET")
     */
    public function getSalesByProductForWaiterDayAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByProductAndWaiter(new \DateTime('-1 day'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasMeseroxProducto")
            ->setSubject("VentasMeseroxProducto");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['product']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasMeseroxProducto.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/product/waiter/week", name="export_sales_product_waiter_week_excel")
     * @Method("GET")
     */
    public function getSalesByProductForWaiterWeekAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByProductAndWaiter(new \DateTime('-1 week'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasMeseroxProducto")
            ->setSubject("VentasMeseroxProducto");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['product']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasMeseroxProducto.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/product/waiter/month", name="export_sales_product_waiter_month_excel")
     * @Method("GET")
     */
    public function getSalesByProductForWaiterMonthAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByProductAndWaiter(new \DateTime('-1 month'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasMeseroxProducto")
            ->setSubject("VentasMeseroxProducto");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['product']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasMeseroxProducto.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/account/waiter/day", name="export_sales_account_waiter_day_excel")
     * @Method("GET")
     */
    public function getSalesByAccountWaiterDayAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && !$this->get('security.authorization_checker')->isGranted('ROLE_CAJERO')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByAccountWaiter(new \DateTime('-1 day'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasxCuentaMesero")
            ->setSubject("VentasxCuentaMesero");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Fecha/Hora Apertura Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Fecha/Hora Clausura Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['account']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['acccheckin']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['acccheckout']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(27);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(29);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('G1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasxCuentaMesero.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/account/waiter/week", name="export_sales_account_waiter_week_excel")
     * @Method("GET")
     */
    public function getSalesByAccountWaiterWeekAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByAccountWaiter(new \DateTime('-1 week'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasxCuentaMesero")
            ->setSubject("VentasxCuentaMesero");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Fecha/Hora Apertura Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Fecha/Hora Clausura Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F1', "Fecha/Hora Apertura Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G1', "Fecha/Hora Clausura Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['account']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['acccheckin']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['acccheckout']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.((string)$i+2), $sales[$i]['numberNote']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.((string)$i+2), $sales[$i]['checkin']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.((string)$i+2), $sales[$i]['checkout']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(27);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('I')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('G1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('H1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('I1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('J1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasxCuentaMesero.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/sales/account/waiter/month", name="export_sales_account_waiter_month_excel")
     * @Method("GET")
     */
    public function getSalesByAccountWaiterMonthAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByAccountWaiter(new \DateTime('-1 month'));

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasxCuentaMesero")
            ->setSubject("VentasxCuentaMesero");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Fecha/Hora Apertura Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Fecha/Hora Clausura Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F1', "Fecha/Hora Apertura Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G1', "Fecha/Hora Clausura Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['account']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['acccheckin']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['acccheckout']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.((string)$i+2), $sales[$i]['numberNote']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.((string)$i+2), $sales[$i]['checkin']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.((string)$i+2), $sales[$i]['checkout']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(27);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(29);
        $phpExcelObject->getActiveSheet()->getColumnDimension('I')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('G1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('H1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('I1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('J1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'VentasxCuentaMesero.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/report/users", name="export_users_excel")
     * @Method("GET")
     */
    public function getReportUsersAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAllWaiters();

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("Meseros")
            ->setSubject("Meseros");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Nombre");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Apellido Paterno");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Apellido Materno");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Usuario");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Celular");

        for ($i=0; $i < count($users); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $users[$i]['name']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $users[$i]['firstLastName']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $users[$i]['secondLastName']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $users[$i]['username']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.((string)$i+2), $users[$i]['cellphoneNumber']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(22);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Meseros.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/report/products", name="export_products_excel")
     * @Method("GET")
     */
    public function getReportProductsAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:Product')->findAllProductsReport();

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("Inventario")
            ->setSubject("Inventario");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Categoría");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Precio");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Stock");

        for ($i=0; $i < count($users); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $users[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $users[$i]['name']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $users[$i]['price']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $users[$i]['stock']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(24);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(24);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        // Style
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Inventario.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/report/accounts/all", name="print_all_accounts")
     * @Method("GET")
     */
    public function getAllAccountsByWaiterAction()
    {
        $chk = $this->get('security.authorization_checker');
        if ($chk->isGranted('ROLE_CAJERO')) {
            try {
                $subtotal = 0;
                $total_general = 0;
                $em = $this->getDoctrine()->getManager();
                // Get Users
                $users = $em->getRepository('AppBundle:User')->findWaitersId();
                // Configure printer
                $connector = new FilePrintConnector("/dev/usb/lp0");
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("REPUBLIK\n");
                $printer->text("Live Music");
                $printer->feed(2);
                $printer->text("Mesero(a)" . $users[0]['waiter'] . "\n");
                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text(str_pad("Cantidad", 9));
                $printer->text(str_pad("  Producto", 23));
                $printer->text(str_pad("Total", 10,' ', STR_PAD_LEFT));
                $printer->text(str_pad("_", 42,'_'));
                $printer->text("\n");
                for ($i = 0; $i < count($users); $i++) {
                    $users[$i]['accounts'] = $em->getRepository('AppBundle:Account')->findAllAccountsByDate($users[$i]['id']);
                    for ($j = 0; $j < count($users[$i]['accounts']); $j++) {
                        $users[$i]['accounts'][$j]['notes'] = $em->getRepository('AppBundle:Account')->findAccountByUserId($users[$i]['accounts'][$j]['id'], $users[$i]['id']);
                        for ($k = 0; $k < count($users[$i]['accounts'][$j]['notes']); $k++) {
                            $users[$i]['accounts'][$j]['notes'][$k]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($users[$i]['id'], $users[$i]['accounts'][$j]['notes'][$k]['numberNote']);
                            for ($l = 0; $l < count($users[$i]['accounts'][$j]['notes'][$k]['products']); $l++) {
                                if ($users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['price'] > 0) {
                                    $printer->text(str_pad($users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['amount'], 9,' ', STR_PAD_LEFT));
                                    $printer->text(str_pad('  ' . utf8_decode($users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['product']), 23));
                                    $printer->text(str_pad(number_format($users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['amount'] * $users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['price'], 2, '.', ','), 10, ' ', STR_PAD_LEFT));
                                    $printer->text("\n");
                                    $subtotal += $users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['amount'] * $users[$i]['accounts'][$j]['notes'][$k]['products'][$l]['price'];
                                }
                            }
                        }
                    }
                }
                $printer->text(str_pad("_", 42,'_'));
                $printer->text(str_pad("Subtotal $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($subtotal,2, '.', ','),10,' ',STR_PAD_LEFT));
                $servicio = $subtotal * 0.05;
                $printer->text(str_pad("Piso 5% $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($servicio,2, '.', ','),10,' ',STR_PAD_LEFT));
                $total = $subtotal + $servicio;
                $printer->text(str_pad("Total $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($total,2, '.', ','),10,' ',STR_PAD_LEFT));
                $printer->feed(2);
                $total_general += $total;
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text(str_pad("Total a pagar $", 20,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($total_general,2, '.', ','),10,' ',STR_PAD_LEFT));
                $printer->feed(2);
                $printer->cut();
                $printer->cloe();
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
}