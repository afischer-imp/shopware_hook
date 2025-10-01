<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderImport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $orderId;

    #[ORM\Column(type: 'string')]
    private string $orderNumber;

    #[ORM\Column(type: 'float')]
    private float $orderTotal;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getOrderTotal(): float
    {
        return $this->orderTotal;
    }

    public function setOrderTotal(float $orderTotal): void
    {
        $this->orderTotal = $orderTotal;
    }
}
