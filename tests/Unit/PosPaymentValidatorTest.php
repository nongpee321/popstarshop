<?php

namespace Tests\Unit;

use App\Services\Sales\PosPaymentValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PosPaymentValidatorTest extends TestCase
{
    private PosPaymentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PosPaymentValidator;
    }

    public function test_accepts_cash_that_covers_the_bill(): void
    {
        $total = $this->validator->validate($this->payload([
            'method' => 'cash',
            'cash_received' => 125,
        ]));

        $this->assertSame(120.0, $total);
    }

    public function test_accepts_cash_with_discount(): void
    {
        $total = $this->validator->validate($this->payload([
            'method' => 'cash',
            'discount_amount' => 20,
            'cash_received' => 100,
        ]));

        $this->assertSame(100.0, $total);
    }

    public function test_accepts_mixed_payment_with_discount(): void
    {
        $total = $this->validator->validate($this->payload([
            'method' => 'mixed',
            'payment_confirmed' => true,
            'discount_amount' => 20,
            'cash_amount' => 60,
            'transfer_amount' => 40,
        ]));

        $this->assertSame(100.0, $total);
    }

    #[DataProvider('invalidPaymentProvider')]
    public function test_rejects_invalid_payment(array $overrides, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        $this->validator->validate($this->payload($overrides));
    }

    public static function invalidPaymentProvider(): array
    {
        return [
            'cash is short' => [
                ['method' => 'cash', 'cash_received' => 100],
                'ยอดเงินสดที่รับไม่ครบ',
            ],
            'transfer is not confirmed' => [
                ['method' => 'transfer', 'payment_confirmed' => false],
                'กรุณาตรวจเงินเข้าก่อนออกบิล',
            ],
            'mixed payment is missing one side' => [
                ['method' => 'mixed', 'payment_confirmed' => true, 'cash_amount' => 120, 'transfer_amount' => 0],
                'จ่ายผสมต้องระบุทั้งยอดเงินสดและยอดโอน',
            ],
            'mixed payment total is wrong' => [
                ['method' => 'mixed', 'payment_confirmed' => true, 'cash_amount' => 50, 'transfer_amount' => 60],
                'ยอดเงินสด+โอนต้องเท่ากับยอดบิล',
            ],
        ];
    }

    private function payload(array $overrides): array
    {
        return array_replace([
            'method' => 'cash',
            'payment_confirmed' => false,
            'cash_received' => 0,
            'cash_amount' => 0,
            'transfer_amount' => 0,
            'items' => [
                ['product_id' => 1, 'qty' => 2, 'unit_price' => 50],
                ['product_id' => 2, 'qty' => 1, 'unit_price' => 20],
            ],
        ], $overrides);
    }
}
