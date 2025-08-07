<?php

namespace App\Interfaces;

interface SupplierInterface {
    public function search(array $params): array;
}
