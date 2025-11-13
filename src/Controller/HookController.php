<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ShopifyOrderService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class HookController extends AbstractController
{
    #[Route('/hook/order', name: 'hook_order', methods: ['GET', 'POST'])]
    public function order(Request $request, ShopifyOrderService $orderService): Response
    {

        $orderId = $request->getPayload()->get('orderId');

        if (!$orderId) {
            return new Response('Order ID fehlt', 400);
        }

        $result = $orderService->importOrder($orderId);

        return new Response('Order importiert: ' . $orderId, 200);
    }
}
