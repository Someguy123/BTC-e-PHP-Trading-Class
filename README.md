Someguy's BTC-e-PHP-Trading-Class
=======================

A PHP class to easily facilitate trading via BTC-e's clunky API.

Donations
=========

BTC: 1SoMGuYknDgyYypJPVVKE2teHBN4HDAh3

LTC: LSomguyTSwcw3hZKFts4P453sPfn4Y5Jzv

How to Use
==========

Include the class in your script

    include('Trade.class.php');
Next, initialize the class with your API details from https://btc-e.com

    $btce = new BTCETrade('MyApiKey', 'MyApiSecret');
Now you can use the class' functions like this:
    
    $ticker = $btce->getTicker('ltc_usd');
    echo $ticker['low'];
    // Outputs something like 27.5 - the low for ltc_usd
    
Please remember if you find my class useful, donate LTC or BTC to the addresses above.

Documentation
=============

All of my functions are nicely documented using PHPDocumentor syntax. This means if you use an IDE such as phpDesigner, you'll get code completion and suggestion relevent to the functions.
It also helps you to understand how a function works, here's an example of phpDocumenter:

    /**
     * BTCETrade::Trade()
     * Trade.
     * @param BTC_USD $pair Currency pair, BTC_USD/BTC_LTC etc.
     * @param buy/sell $type Type of transaction
     * @param float $rate The rate to buy/sell
     * @param int $amount Amount to buy/sell
     * @return array( recieved, remains, order_id, funds{usd,btc,sc,ltc,ruc,nmc} )
     */
    function Trade($pair, $type, $rate, $amount)
    {
    ...
