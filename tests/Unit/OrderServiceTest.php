<?php

namespace Tests\Unit;

use App\Services\OrderService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    public function testProcessOrderWithValidData()
    {
        $data = [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 1500,
            'currency' => 'USD'
        ];

        $result = $this->orderService->processOrder($data);

        $this->assertEquals(46500, $result['price']);
        $this->assertEquals('TWD', $result['currency']);
    }

    public function testProcessOrderWithInvalidName()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Name contains non-English characters');

        $data = [
            'id' => 'A0000001',
            'name' => 'Melody123',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 1500,
            'currency' => 'TWD'
        ];

        $this->orderService->processOrder($data);
    }

    public function testProcessOrderWithNonCapitalizedName()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Name is not capitalized');

        $data = [
            'id' => 'A0000001',
            'name' => 'melody holiday inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 1500,
            'currency' => 'TWD'
        ];

        $this->orderService->processOrder($data);
    }

    public function testProcessOrderWithPriceOver2000()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Price is over 2000');

        $data = [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 2050,
            'currency' => 'TWD'
        ];

        $this->orderService->processOrder($data);
    }

    public function testProcessOrderWithInvalidCurrency()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Currency format is wrong');

        $data = [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 1500,
            'currency' => 'EUR'
        ];

        $this->orderService->processOrder($data);
    }
}
