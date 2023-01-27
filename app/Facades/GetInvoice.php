<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;


class GetInvoice extends Facade
{
  protected static function getFacadeAccessor() {
    return 'getinvoice';
  }
}