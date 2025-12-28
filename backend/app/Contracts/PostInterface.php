<?php

namespace App\Contracts;

interface PostInterface
{
    public function store(array $data);
    public function destroy(array $data);
    public function update(array $data);
}
