<?php

namespace Zarinpal;

use Illuminate\Support\Facades\Config;

class Payment
{
    /**
     * Amount in Tumaan.
     *
     * @var int
     */
    public $amount;

    /**
     * Description.
     *
     * @var string
     */
    public $description;

    /**
     * Zarinpal's Authority.
     *
     * @var string
     */
    public $authority;

    /**
     * Payment status code.
     *
     * @var int
     */
    public $status;

    /**
     * Payment RefID code.
     *
     * @var int
     */
    public $RefID;

    /**
     * Payment constructor.
     *
     * @param int         $amount
     * @param string|null $description
     * @param string|null $authority
     */
    public function __construct(int $amount, string $description = null, string $authority = null)
    {
        $this->amount = $amount;
        $this->authority = $authority;

        if ($description == null || $description == '') {
            $this->description = Config::get('services.zarinpal.description', Config::get('app.name'));
        } else {
            $this->description = $description;
        }
    }
}
