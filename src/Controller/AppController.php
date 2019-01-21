<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Settings;
use App\Entity\Siparis;
use App\Entity\SiparisDetail;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app")
 */
class AppController extends AbstractController
{

    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $orders = $this->getDoctrine()->getRepository(Siparis::class)->findAll();

        $lastFiveOrders = $this->getDoctrine()->getRepository(Siparis::class)
            ->findBy(array(), array('id' => 'DESC'), 5);


        return $this->render('app/dashboard.html.twig', [
            'controller_name' => 'AppController',
            'total_products' => count($products),
            'orders' => $orders,
            'last_five_orders' => $lastFiveOrders
        ]);
    }


    /**
     * @Route("/settings", name="app_settings")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settings(Request $request)
    {


        $settings = $this->getDoctrine()->getRepository(Settings::class)->find(1);


        if ($request->request->get('euro_usd')):
            $em = $this->getDoctrine()->getManager();
            $settings->setEurUsd(floatval($request->request->get('euro_usd')));
            $this->addFlash('success', 'EURO/USD paritesi ' . $request->request->get('euro_usd') . '
            olarak güncellendi.');
            $em->flush();
        endif;

        if ($request->request->get('renk_yuzdesi')):
            $em = $this->getDoctrine()->getManager();
            $settings->setRenkliArtisOrani(intval($request->request->get('renk_yuzdesi')));
            $this->addFlash('success', 'Artış yüzdesi %' . $request->request->get('renk_yuzdesi') . '
            olarak güncellendi.');
            $em->flush();
        endif;


        return $this->render('app/settings.html.twig', [
            'euro_usd' => $settings->getEurUsd(),
            'renk_yuzdesi' => $settings->getRenkliArtisOrani()
        ]);
    }


    /**
     * @Route("/orders", name="app_orders")
     */
    public function orders()
    {

        $orders = $this->getDoctrine()->getRepository(Siparis::class)->findBy(array(), array('id' => 'DESC'));

        return $this->render('app/orders.html.twig', [
            'orders' => $orders
        ]);

    }

    /**
     * @Route("/order/show/{id}", name="show_order")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function showOrder($id, Request $request)
    {

        //her girişte currency euro olmalı. sistem euro üzerine kurulu
        $session = $this->get('session');
        $session->remove('currency');

        $order_detail = $this->getDoctrine()->getRepository(SiparisDetail::class)
            ->findBy(array('head' => $id));

        $order = $this->getDoctrine()->getRepository(Siparis::class)
            ->find($id);


        if ($request->query->get('proforma') == 'true') {


            // Configure Dompdf according to your needs

            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $pdfOptions->set('isRemoteEnabled', TRUE);

            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);



            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('pdf/proforma_euro.html.twig',
                array(
                    'name' => $order->getName(),
                    'surname' => $order->getSurname(),
                    'invoice_address' => $order->getInvoiceAddress(),
                    'delivery_address' => $order->getAdress(),
                    'phone' => $order->getPhone(),
                    'email' => $order->getEmail(),
                    'attention_to' => $order->getAttentionTo(),
                    'date' => $order->getOrderDate(),
                    'siparis_id' => $order->getId(),
                    'siparis_kodu' => $order->getCode(),
                    'order_detail' => $order_detail,
                    'order' => $order

                ));

            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();


            /*
            #SAVE FILE

            // Store PDF Binary Data
            $output = $dompdf->output();

            // In this case, we want to write the file in the public directory
            $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf/';
            // e.g /var/www/project/public/mypdf.pdf
            $pdfFilepath =  $publicDirectory . $order->getCode().'.pdf';

            // Write file to the desired path
            file_put_contents($pdfFilepath, $output);

            #SAVE FILE END
            */


            // Output the generated PDF to Browser (force download)
            $dompdf->stream(uniqid("proforma_", false) . ".pdf", [
                "Attachment" => true
            ]);


        }


        if ($request->query->get('excel') == 'true') {

            $spreadsheet = new Spreadsheet();


            /**
             * Styles
             */
            $fontBold = ['font' => ['bold' => true]];


            /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */

            $spreadsheet->getActiveSheet()->setTitle("ELIFLE - PROFORMA INVOICE");
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal('center');
            $spreadsheet->getActiveSheet()->setCellValue('A1', 'PROFORMA INVOICE');

            $spreadsheet->getActiveSheet()->setCellValue('A3', 'Name: ');
            $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A4:C4');
            $spreadsheet->getActiveSheet()->setCellValue('A4', $order->getName());
            $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal('left');

            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Surname: ');
            $spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A6:C6');
            $spreadsheet->getActiveSheet()->setCellValue('A6', $order->getSurname());
            $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal('left');

            $spreadsheet->getActiveSheet()->setCellValue('A7', 'Phone: ');
            $spreadsheet->getActiveSheet()->getStyle('A7')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A8:C8');
            $spreadsheet->getActiveSheet()->setCellValue('A8', $order->getPhone());
            $spreadsheet->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal('left');

            $spreadsheet->getActiveSheet()->setCellValue('A9', 'E-Mail: ');
            $spreadsheet->getActiveSheet()->getStyle('A9')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A10:C10');
            $spreadsheet->getActiveSheet()->setCellValue('A10', $order->getEmail());
            $spreadsheet->getActiveSheet()->getStyle('A10')->getAlignment()->setHorizontal('left');

            $spreadsheet->getActiveSheet()->setCellValue('A11', 'Attention to: ');
            $spreadsheet->getActiveSheet()->getStyle('A11')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A12:C12');
            $spreadsheet->getActiveSheet()->setCellValue('A12', $order->getAttentionTo());
            $spreadsheet->getActiveSheet()->getStyle('A12')->getAlignment()->setHorizontal('left');

            $spreadsheet->getActiveSheet()->setCellValue('A13', 'Invoice Address: ');
            $spreadsheet->getActiveSheet()->getStyle('A13')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A14:C17');
            $spreadsheet->getActiveSheet()->setCellValue('A14', $order->getInvoiceAddress());
            $spreadsheet->getActiveSheet()->getStyle('A14')->getAlignment()->setWrapText(true);

            $spreadsheet->getActiveSheet()->setCellValue('A18', 'Delivery Adress: ');
            $spreadsheet->getActiveSheet()->getStyle('A18')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->mergeCells('A19:C22');
            $spreadsheet->getActiveSheet()->setCellValue('A19', $order->getAdress());
            $spreadsheet->getActiveSheet()->getStyle('A19')->getAlignment()->setWrapText(true);


            $spreadsheet->getActiveSheet()->mergeCells('D3:G20');
            //resim ekle
            $drawing = new Drawing();
            $drawing->setPath($this->getParameter('kernel.project_dir') . '/public/uploads/colors.jpg');
            $drawing->setCoordinates('D3');
            $drawing->setHeight(200);
            $drawing->setWidth(214);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            $drawing->setOffsetY(2);
            $drawing->setOffsetX(2);


            $spreadsheet->getActiveSheet()->mergeCells('I4:K4');
            $spreadsheet->getActiveSheet()->setCellValue('I3', 'Date:');
            $spreadsheet->getActiveSheet()->getStyle('I3')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->setCellValue('I4', $order->getOrderDate());

            $spreadsheet->getActiveSheet()->mergeCells('I7:K7');
            $spreadsheet->getActiveSheet()->setCellValue('I6', 'Invoice:');
            $spreadsheet->getActiveSheet()->getStyle('I6')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->setCellValue('I7', '#000' . $order->getId());

            $spreadsheet->getActiveSheet()->mergeCells('I10:K10');
            $spreadsheet->getActiveSheet()->setCellValue('I9', 'Order ID:');
            $spreadsheet->getActiveSheet()->getStyle('I9')->applyFromArray($fontBold);
            $spreadsheet->getActiveSheet()->setCellValue('I10', $order->getCode());


            $thead = [
                ["Picture", "Code", "Name", "Inner",
                    "Rim", "Outer", "Desc.",
                    "QTY", "Unit", "Cur", "Sub T."]
            ];
            $spreadsheet->getActiveSheet()
                ->fromArray(
                    $thead,  // The data to set
                    NULL,        // Array values with this value will not be set
                    'A25'         // Top left coordinate of the worksheet range where
                //    we want to set these values (default is A1)
                );

            //thead bold
            $spreadsheet->getActiveSheet()->getStyle('A20:K20')->applyFromArray($fontBold);

            $cell_value = 26;
            $toplam_tutar = 0;
            $adet = 0;

            foreach ($order_detail as $od) {

                $product_code = $od->getProduct()->getCode();
                $product_name = $od->getProduct()->getName();
                $inner_color = $od->getInnerColor();
                $rim_color = $od->getRimColor();
                $outer_color = $od->getOuterColor();
                $description = $od->getDescription();
                $quantity = $od->getQuantity();
                $picture = $od->getProduct()->getPicture();
                $price = floatval($od->getProduct()->getPrice());


                if ($od->getProduct()->getCollection()->getName() == 'RUBIENDA' ||
                    $od->getProduct()->getProperty() == 'custom') {
                    if ($inner_color == 0):$inner_color = 'white';endif;
                    if ($rim_color == 0):$rim_color = 'white';endif;
                    if ($outer_color == 0):$outer_color = 'white';endif;
                }


                //dolara parite uygula: sistem fiyatları euro çünkü.
                $birim = $od->getCurrency();
                $parite = $od->getEuroUsd();
                $sembol = '€';
                if ($birim == 'usd') {
                    $price = $price * $parite;
                    $sembol = '$';
                }

                //1 renkten fazlaysa %10 zam uygula
                $renk_adedi=0;
                if (intval($inner_color) > 0):$renk_adedi++;endif;//renk seçiliyse/white değilse
                if (intval($rim_color) > 0):$renk_adedi++;endif;//renk seçiliyse/white değilse
                if (intval($outer_color) > 0):$renk_adedi++;endif;//renk seçiliyse/white değilse

                if($renk_adedi>1):$price = (($price*10)/100)+$price;endif;




                //resim ekle
                $drawing = new Drawing();
                $drawing->setName('Product');
                $drawing->setDescription('P');

                if (is_file($this->getParameter('products_directory') . '/' . $picture)) {
                    $drawing->setPath($this->getParameter('products_directory') . '/' . $picture);
                    $drawing->setHeight(38);
                    $drawing->setWidth(60);
                    $drawing->setCoordinates('A' . $cell_value);
                    $drawing->setWorksheet($spreadsheet->getActiveSheet());
                    $drawing->setOffsetY(2);
                    $drawing->setOffsetX(2);


                    //resim yükseklik ve genişlik ayarları.
                    $spreadsheet->getActiveSheet()->getRowDimension($cell_value)
                        ->setRowHeight($drawing->getHeight());

                    $spreadsheet->getActiveSheet()->getColumnDimension('A')
                        ->setWidth($drawing->getWidth() - ($drawing->getWidth() * .85));
                }


                /*
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                */

                //$spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value, $picture);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell_value, $product_code);

                //ürün adının doldur ve fontunu küçült.
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell_value, $product_name);
                $spreadsheet->getActiveSheet()->getStyle('C' . $cell_value)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('C' . $cell_value)->getFont()->setSize(9);

                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell_value, $inner_color);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell_value, $rim_color);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell_value, $outer_color);

                //açıklama yazısını doldur ve fontunu küçült.
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_value, $description);
                $spreadsheet->getActiveSheet()->getStyle('G' . $cell_value)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('G' . $cell_value)->getFont()->setSize(9);

                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_value, $quantity);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_value, $price);
                $spreadsheet->getActiveSheet()->getStyle('I' . $cell_value)->getNumberFormat()->setFormatCode('0.00');

                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_value, $sembol);
                $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValue('K' . $cell_value, $quantity * $price);
                $spreadsheet->getActiveSheet()->getStyle('K' . $cell_value)->getNumberFormat()->setFormatCode('0.00');

                $toplam_tutar = $toplam_tutar + ($quantity * $price);
                $adet = $adet + $quantity;


                //dongü içindeki satırlara ait border
                $spreadsheet->getActiveSheet()
                    ->getStyle('A' . $cell_value . ':K' . $cell_value)
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('000000'));


                //her satırı ortala
                $spreadsheet->getActiveSheet()->getStyle('A' . $cell_value . ':K' . $cell_value)
                    ->getAlignment()->setHorizontal('center');


                $cell_value++;

            }

            //dongü dışındaki ilk satıra ait border
            $spreadsheet->getActiveSheet()
                ->getStyle('A' . $cell_value . ':K' . $cell_value)
                ->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->setColor(new Color('000000'));

            //toplam tutar
            $spreadsheet->getActiveSheet()->setCellValue('K' . $cell_value, $toplam_tutar);
            $spreadsheet->getActiveSheet()->getStyle('K' . $cell_value)->getNumberFormat()->setFormatCode('0.00');

            //adet
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_value, $adet);

            //ortala
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell_value . ':K' . $cell_value)
                ->getAlignment()->setHorizontal('center');



            //footer
            $cell_value = $cell_value+2;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'The goods are Turkish Origin and we declare that above given information are true.');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+2;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'Shipment: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                ' 8-10 weeks after first payment');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+1;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'Payment: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                '50% CASH IN ADVANCE WITH PROFORMA CONFIRMATION;
REMAINING 50% CASH IN ADVANCE BEFORE SHIPMENT');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+1;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'Delivery: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                'EX FACTORY DENIZLI');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+1;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'Bank Details: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                'DENIZBANK /Sarayköy Branch (4760)/14116855-353');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+1;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'EUR IBAN No: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                'TR390013400001411685500010');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+1;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'USD IBAN No: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                'TR550013400001411685500013');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);

            $cell_value = $cell_value+1;
            $spreadsheet->getActiveSheet()->mergeCells('A'.$cell_value.':B'.$cell_value);
            $spreadsheet->getActiveSheet()->mergeCells('C'.$cell_value.':K'.$cell_value);
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell_value,
                'SWIFT CODE: ');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell_value,
                'DENITRISXXX');
            $spreadsheet->getActiveSheet()->getStyle('A'.$cell_value)->applyFromArray($fontBold);



            // Create your Office 2007 Excel (XLSX Format)
            $writer = new Xlsx($spreadsheet);

            // Create a Temporary file in the system
            $fileName = uniqid("proforma_", false) . '.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);

            // Create the excel file in the tmp directory of the system
            $writer->save($temp_file);

            // Return the excel file as an attachment
            return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

        }


        return $this->render('app/order_show.html.twig', [
            'order_detail' => $order_detail,
            'order' => $order

        ]);

    }


    /**
     * @Route("/update-order-status", name="update_order_status")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateOrderStatus(Request $request)
    {

        $order_id = $request->request->get('order_id');
        $status = $request->request->get('status');


        $em = $this->getDoctrine()->getManager();

        $order = $this->getDoctrine()->getRepository(Siparis::class)
            ->find($order_id);

        $order->setStatus($status);
        $em->flush();
        $em->clear();

        return $this->redirectToRoute('show_order', array('id' => $order_id));


    }

    /**
     * @Route("/delete-order", name="delete_order")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteOrder(Request $request)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $order_id = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();

        $order = $this->getDoctrine()->getRepository(Siparis::class)
            ->find($order_id);


        if ($order->getStatus() == 'cancelled') {
            $em->remove($order);
            $em->flush();

            //TODO pdf proforma varsa onu da sil
        }

        //bellekten biten sorguları temizle. (bulk)
        $em->clear();

        $this->addFlash('success', 'İptal edilen sipariş, başarıyla silindi');

        return $this->redirectToRoute('app_orders');


    }


}
