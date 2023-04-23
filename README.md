# ZPAY PACKAGE

### Laravel wrapper for [MPESA AND AZAM PAY APIS into LARAVEL Package](https://github.com/Idrisa24/zpay)

## Introduction

Zpay provides an expressive, fluent interface to [Vodacom's](https://vodacom.co.tz/) and [Azam pay](https://azampay.com/) billing services. It handles almost all of the boilerplate  billing code you are dreading writing. In addition to basic payment management, Zpay can handle coupons, swapping subscription, subscription "quantities", cancellation grace periods and much more.

## Installation

### Laravel
Require this package in your composer.json and update composer. This will download the package and the zpay + phpseclib,simple-qrcode and dompdf libraries also.

    composer require saidtech/zpay


## Using

You can create a new ZPAY instance and load it's functions or view name.

```php
    use Barryvdh\DomPDF\Facade\Pdf;
    $pdf = Pdf::loadView('pdf.invoice', $data);
    return $pdf->download('invoice.pdf');
```