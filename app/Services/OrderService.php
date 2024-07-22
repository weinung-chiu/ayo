<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class OrderService
{
    public function processOrder($data)
    {
        $this->validateName($data['name']);
        $this->validatePrice($data['price']);
        $this->validateCurrency($data['currency']);

        // 當貨幣為 USD 時，需修改 price 金額乘上固定匯率 31，並且將 currency 改為 TWD
        if ($data['currency'] === 'USD') {
            $data['price'] = $data['price'] * 31;
            $data['currency'] = 'TWD';
        }

        return $data;
    }

    private function validateName($name)
    {
        if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
            throw ValidationException::withMessages(['name' => 'Name contains non-English characters']);
        }

        if (!preg_match('/^(?:[A-Z][a-z]*\s?)+$/', $name)) {
            throw ValidationException::withMessages(['name' => 'Name is not capitalized']);
        }
    }

    private function validatePrice($price)
    {
        if ($price > 2000) {
            throw ValidationException::withMessages(['price' => 'Price is over 2000']);
        }
    }

    private function validateCurrency($currency)
    {
        if (!in_array($currency, ['TWD', 'USD'])) {
            throw ValidationException::withMessages(['currency' => 'Currency format is wrong']);
        }
    }
}
