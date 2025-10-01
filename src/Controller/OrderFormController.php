<?php
namespace App\Controller;

use App\Service\ShopwareOrderService;
use App\Entity\Shop;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderFormController extends AbstractController
{
    #[Route('/order/form', name: 'order_form')]
    public function index(Request $request, ShopwareOrderService $orderService, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('orderId', TextType::class, ['label' => 'Order ID'])
            ->add('submit', SubmitType::class, ['label' => 'Bestellung abrufen'])
            ->getForm();

        $form->handleRequest($request);
        $orderImport = null;
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $orderId = $data['orderId'];
            // Beispiel: Hole den ersten aktiven Shop
            $shop = $em->getRepository(Shop::class)->findOneBy(['shopActive' => true]);
            if (!$shop) {
                $error = 'Kein aktiver Shop gefunden.';
            } else {
                $orderImport = $orderService->importOrder($orderId, $shop);
                if (!$orderImport) {
                    $error = 'Bestellung konnte nicht abgerufen werden.';
                }
            }
        }

        return $this->render('order_form/index.html.twig', [
            'form' => $form->createView(),
            'orderImport' => $orderImport,
            'error' => $error,
        ]);
    }
}

