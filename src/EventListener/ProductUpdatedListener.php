<?php
namespace App\EventListener;

use Shopware\App\SDK\Context\Webhook\WebhookAction;

#[AsEventListener(event: 'webhook.product.written')]
class ProductUpdatedListener {
    public function __invoke(WebhookAction $action): void {
        dump($action);
    }
}
