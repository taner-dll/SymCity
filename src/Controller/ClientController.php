<?php

namespace App\Controller;

use App\Entity\CollectionGroup;
use App\Entity\Settings;
use App\Entity\Siparis;
use App\Entity\SiparisDetail;
use App\Entity\Product;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/change-currency", name="change_currency")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeCurrency(Request $request){

        if($request->query->get('cur')){

            $settings = $this->getDoctrine()->getRepository(Settings::class)->find(1);

            $session = $this->get('session');
            $session->set('currency',$request->query->get('cur'));
            $session->set('parite',$settings->getEurUsd());

        }

        return $this->redirectToRoute('our_collections');


    }

    /**
     * @Route("/our-products", name="our_products")
     */
    public function ourProducts()
    {
        return $this->redirectToRoute('our_collections');

        /*
        return $this->render('client/our-products.html.twig', [
            'controller_name' => 'ClientController',
        ]);
        */
    }


    /**
     * @Route("/product/{id}", name="client_product_show")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function product($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        return $this->render('client/product.html.twig', [
            'product' => $product,
        ]);
    }


    /**
     * @Route("/our-collections", name="our_collections")
     */
    public function ourCollections()
    {
        $em = $this->getDoctrine()->getManager();
        $collections = $em->getRepository(CollectionGroup::class);

        $settings = $this->getDoctrine()->getRepository(Settings::class)->find(1);
        $session = $this->get('session');
        $session->set('parite',$settings->getEurUsd());

        return $this->render('client/our-collections.html.twig', [
            'collections' => $collections->findBy(array('enable'=>1)),
        ]);
    }


    /**
     * @Route("/collection-list/{id}", name="collection_list")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function collectionList($id, Request $request)
    {
        if($request->query->get('colg')){
            $colg = $request->query->get('colg');
        }
        else{
            $colg = '';
        }

        $cur = $this->get('session');


        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)
            ->collectionGroupProducts($id,$colg,$cur);




        $colGroup = $em->getRepository(CollectionGroup::class)->find($id);
        $s = 0;
        $pcName = '';
        $image = "";
        $pcId = 0;
        foreach ($colGroup->getProdcollection() as $value) {
            $s++;
            if ($s == 1) {
                $image = $value->getPicture();
                $pcName = $value->getCollectionGroup()->getName();
                $pcId = $value->getCollectionGroup()->getId();
            }
        }



        return $this->render('client/collection-list.html.twig', [
            'products' => $products, 'image' => $image, 'pc_name'=>$pcName, 'pc_id' => $pcId,
            'prod_cols' => $colGroup->getProdcollection()

        ]);
    }

    /**
     * @Route("/custom-products", name="custom-products")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function customProducts()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)
            ->customProducts();

        return $this->render('client/custom-product-list.html.twig', [
            'products' => $products

        ]);
    }

    /**
     * @Route("/add-to-cart", name="add_to_cart", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCart(Request $request)
    {
        $product = $request->request->get('product');
        $quantity = $request->request->get('quant')[1];

        $inner_color = $request->request->get('inner_color');
        $rim_color = $request->request->get('rim_color');
        $outer_color = $request->request->get('outer_color');


        $session = $this->get('session');

        $add_elemant = array(
            'product_id' => $product,
            'quantity' => $quantity,
            'unique' => uniqid("sess_"),
            'inner_color'=>$inner_color,
            'rim_color' => $rim_color,
            'outer_color' => $outer_color
        );

        if ($session->get('cart_elements')) {
            $sessionVal = $this->get('session')->get('cart_elements');
            $sessionVal[] = $add_elemant;
            $session->set('cart_elements', $sessionVal);
        } else {
            $sessionVal[] = $add_elemant;
            $session->set('cart_elements', $sessionVal);
        }
        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart-remove", name="cart_remove")
     */
    public function cartRemove()
    {
        $session = $this->get('session');
        $session->remove('cart_elements');
        return $this->redirectToRoute('our_products');
    }


    /**
     * @Route("/cart", name="cart")
     */
    public function cart()
    {

        $session = $this->get('session');
        $cart_elements = $session->get('cart_elements');
        $em = $this->getDoctrine()->getManager();
        $siparis_grubu = array();


        if ($cart_elements):

            foreach ($cart_elements as $key => $element) {




                $p = $em->getRepository(Product::class)->find($element['product_id']);


                // + %10
                $renk_say = 0;
                if(intval($element['inner_color'])>0){$renk_say++;};
                if(intval($element['rim_color'])>0){$renk_say++;};
                if(intval($element['outer_color'])>0){$renk_say++;};

                if($renk_say > 1){
                    $price = ((floatval($p->getPrice()) * 10)/100)+floatval($p->getPrice());
                    $price = number_format(floatval($price), 2);
                }
                else{
                    $price = floatval($p->getPrice());
                }



                $urun_adi = $p->getName();
                $urun_resmi = $p->getPicture();
                $siparis_adedi = $element['quantity'];
                $toplam_tutar = floatval($price) * $siparis_adedi;


                $siparis_grubu[$key][] =

                    array(

                        'unique' => $element['unique'],
                        'urun_id' => $p->getId(),
                        'urun_adi' => $urun_adi,
                        'koleksiyon_adi' => $p->getCollection()->getName(),
                        'inner_color'=>intval($element['inner_color']),
                        'rim_color'=>intval($element['rim_color']),
                        'outer_color'=>intval($element['outer_color']),
                        'urun_resmi' => $urun_resmi,
                        'urun_fiyati' => number_format($price,2),
                        'siparis_adedi' => $siparis_adedi,
                        'ara_toplam' => number_format($toplam_tutar,2),
                        'ozellik'=>$p->getProperty()
                    );

            }

        endif;





        return $this->render('client/cart.html.twig', [
            'cart_elements' => $siparis_grubu

        ]);
    }


    /**
     * @Route("/submit-order", name="submit_order", methods={"POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function submitOrder(Request $request, \Swift_Mailer $mailer)
    {

        $name = $request->request->get('name');
        $surname = $request->request->get('surname');
        $email = $request->request->get('email');
        $address = $request->request->get('address');
        $phone = $request->request->get('phone');
        $code = uniqid("sip_", false);

        $attention = $request->request->get('attention');
        $invoiceAddress = $request->request->get('invoice_address');

        $settings = $this->getDoctrine()->getRepository(Settings::class)->find(1);


        $em = $this->getDoctrine()->getManager();
        $siparis = new Siparis();
        $siparis->setName($name);
        $siparis->setSurname($surname);
        $siparis->setEmail($email);
        $siparis->setPhone($phone);
        $siparis->setAdress($address);
        $siparis->setCode($code);
        $siparis->setStatus('pending');
        $siparis->setAttentionTo($attention);
        $siparis->setInvoiceAddress($invoiceAddress);
        $siparis->setProformaSent(0);
        $siparis->setOrderDate(new \DateTime('now', new \DateTimeZone('Europe/Istanbul')));

        $em->persist($siparis);
        $em->flush();

        $cart_elements = $this->get('session')->get('cart_elements');
        $currency = $this->get('session')->get('currency');
        $siparis_mail = array();

        foreach ($cart_elements as $key => $element) {

            $p = $em->getRepository(Product::class)->find($element['product_id']);


            $Siparis_detail = new SiparisDetail();
            $Siparis_detail->setHead($siparis);
            $Siparis_detail->setQuantity($element['quantity']);
            $Siparis_detail->setProduct($p);
            $Siparis_detail->setEuroUsd($settings->getEurUsd());




            // + %10
            $renk_say = 0;
            if(intval($element['inner_color'])>0){$renk_say++;};
            if(intval($element['rim_color'])>0){$renk_say++;};
            if(intval($element['outer_color'])>0){$renk_say++;};

            if($renk_say > 1){
                $price = ((floatval($p->getPrice()) * 10)/100)+floatval($p->getPrice());
                $price = number_format(floatval($price), 2);
            }
            else{
                $price = floatval($p->getPrice());
            }

            $birim = '€';
            if($currency):
                $Siparis_detail->setCurrency($currency);

                if($currency == 'usd'):
                    $birim = '$';
                endif;

            else:
                $Siparis_detail->setCurrency("eur");
            endif;


            // custom ürün ya da rubienda vs. çok renkliyse...
            if(($p->getProperty()=='custom') or ($renk_say > 0)){
                $description = 'IC:'.$element['inner_color'].' / '.'RC:'.$element['rim_color'].' / '.
                'OC:'.$element['outer_color'];
                $Siparis_detail->setInnerColor(intval($element['inner_color']));
                $Siparis_detail->setRimColor(intval($element['rim_color']));
                $Siparis_detail->setOuterColor(intval($element['outer_color']));

                if($p->getCollection()->getName()){
                    $description .= ' '. $p->getCollection()->getName();
                }
            }
            else{
                $description = $p->getCollection()->getName().' ('.$p->getCode().')';
            }

            $Siparis_detail->setDescription($description);






            $em->persist($Siparis_detail);

            $siparis_mail[] = array(
                'id' => $p->getId(),
                'urun' => $p->getName(),
                'code' => $p->getCode(),
                'adet' => $element['quantity'],
                'type' => $p->getType(),
                'price' => $price,

            );
        }

        $em->flush();

        $session = $this->get('session');
        $session->remove('cart_elements');
        
        
        
        
        #proformayı diske kaydet. /public/pdf/siparis-kodu.pdf
        $order_detail = $this->getDoctrine()->getRepository(SiparisDetail::class)
            ->findBy(array('head' => $siparis));

        // Configure Dompdf according to your needs

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', TRUE);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/proforma_euro.html.twig',
            array(
                'name'=>$siparis->getName(),
                'surname'=>$siparis->getSurname(),
                'invoice_address'=>$siparis->getInvoiceAddress(),
                'delivery_address'=>$siparis->getAdress(),
                'phone'=>$siparis->getPhone(),
                'email'=>$siparis->getEmail(),
                'attention_to'=>$siparis->getAttentionTo(),
                'date'=>$siparis->getOrderDate(),
                'siparis_id'=>$siparis->getId(),
                'siparis_kodu'=>$siparis->getCode(),
                'order_detail' => $order_detail,
                'order' => $siparis

            ));

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();


        #SAVE FILE

        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory . $siparis->getCode().'.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
        #SAVE FILE END





        #admine eposta

        /*
        $body = '<h2>Yeni sipariş oluşturuldu</h2><table>';
        $body .= '<h3>Sipariş Detayları:</h3><table>';
        foreach ($siparis_mail as $k => $v) {

            $fmt_price = $v['adet'] * floatval($v['price']);

            $body .=
                '<tr>' .
                '<td><b>ID:</b> ' . $v['id'] . '</td>' .
                '<td><b>Ürün Adı:</b> ' . $v['urun'] . '</td>' .
                '<td><b>Ürün Fiyat:</b> ' . $v['price'] . ' '.$birim.'</td>' .
                '<td><b>Adet:</b> ' . $v['adet'] . '</td>' .
                '<td><b>Ara Toplam:</b> ' . number_format($fmt_price, 2) . ' '.$birim.'</td>' .
                '<tr>';
        }

        $body .= '</table><hr>';
        $body .= '<a style="text-decoration:none;" 
        href="order.elifle.com/app/order/show/'.$siparis->getId().'">Siparişi Görüntüle &raquo;</a>';

        //$setTo = array('tnrdll@gmail.com','duyguhanbasaran@gmail.com');
        $setTo = array('tnrdll@gmail.com');

        $message = new \Swift_Message();
        $message->setSubject('Elifle.com Yeni Sipariş');
        $message->setBody($body, 'text/html');
        $message->setFrom('order.elifle@gmail.com');
        $message->setTo($setTo);
        //$message->attach(\Swift_Attachment::fromPath($pdfFilepath));
        $mailer->send($message);
        */


        #müşteriye eposta
        $body = '<h3>Thank You!</h3>';
        $body .= '<p>Your order has been successfully created.</p>';

        $message = new \Swift_Message();
        $message->setSubject('Elifle.com');
        $message->setBody($body, 'text/html');
        $message->setFrom('order.elifle@gmail.com');
        //$message->setTo(array('duyguhanbasaran@gmail.com',$email));
        $message->setTo(array('duyguhanbasaran@gmail.com'));
        $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
        $mailer->send($message);



        $this->addFlash('success','Your order has been successfully created');

        /*
        $session = $this->get('session');
        $session->remove('currency');
        */


        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/remove-from-cart/{unique}", name="remove_from_cart", methods={"GET"})
     * @param $unique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromCart($unique)
    {
        $session = $this->get('session');
        $cart_elements = $session->get('cart_elements');
        foreach ($cart_elements as $key => $value) {
            if ($value['unique'] == $unique) {
                unset($cart_elements[$key]);
            }
        }
        $session->set('cart_elements', $cart_elements);
        return $this->redirectToRoute('cart');
    }


}
