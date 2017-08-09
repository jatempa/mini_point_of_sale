<?php
/**
 * Created by PhpStorm.
 * User: atempa
 * Date: 9/08/17
 * Time: 12:47 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BuilderReportController extends Controller
{

    /**
     * @Route("/sales/category/product/waiter", name="export_sales_category_product_waiter_excel")
     * @Method("GET")
     */
    public function getSalesByCategoryProductForWaiterAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByCategoryProductAndWaiter();

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
     * @Route("/sales/product/waiter", name="export_sales_product_waiter_excel")
     * @Method("GET")
     */
    public function getSalesByProductForWaiterAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByProductAndWaiter();

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
     * @Route("/sales/account/waiter", name="export_sales_account_waiter_excel")
     * @Method("GET")
     */
    public function getSalesByAccountWaiterAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sales = $em->getRepository('AppBundle:NoteProduct')->findSalesByAccountWaiter();

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Admin")
            ->setTitle("VentasxCuentaMesero")
            ->setSubject("VentasxCuentaMesero");

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Mesero");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', "Cuenta");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', "Mesa");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', "Fecha/Hora de Apertura");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Fecha/Hora de Clausura");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F1', "Comanda");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G1', "Tipo de producto");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H1', "Cantidad");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I1', "Total");

        for ($i=0; $i < count($sales); $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.((string)$i+2), $sales[$i]['waiter']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.((string)$i+2), $sales[$i]['account']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.((string)$i+2), $sales[$i]['table']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.((string)$i+2), $sales[$i]['checkin']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.((string)$i+2), $sales[$i]['checkout']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.((string)$i+2), $sales[$i]['numberNote']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.((string)$i+2), $sales[$i]['category']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.((string)$i+2), $sales[$i]['amount']);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.((string)$i+2), $sales[$i]['total']);
        }

        // Dimensions
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(27);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(22);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(22);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(18);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('I')->setWidth(12);
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
}