<?php

class BTCETrade
{
    private $key;
    private $secret;

    function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }
    function query($method, array $req = array())
    {
        $key = $this->key;
        $secret = $this->secret;
        $req['method'] = $method;
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac("sha512", $post_data, $secret);

        $headers = array(
            'Sign: ' . $sign,
            'Key: ' . $key,
            );

        // our curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; BTCE PHP client; ' .
                php_uname('s') . '; PHP/' . phpversion() . ')');
        }
        curl_setopt($ch, CURLOPT_URL, 'https://btc-e.ru/tapi/');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // run the query
        $res = curl_exec($ch);
        if ($res === false)
            throw new Exception('Could not get reply: ' . curl_error($ch));
        $dec = json_decode($res, true);
        if ($dec['success'] != 1) {
            throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
            //die("The API {$method} with nonce {$req['nonce']} returned unsuccessful: " . $dec['error'] . "\n");
        }
        if (!$dec)
            throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
        return $dec;
    }
    function getInfo()
    {
        $data = $this->query("getInfo");
        return $data['return'];
    }
    function getTicker($pair = "ltc_usd") {
        $j = file_get_contents("https://btc-e.com/api/2/" . $pair . "/ticker");
        $dec = json_decode($j, true);
        return $dec['ticker'];
    }

    /**
     * BTCETrade::TransHistory()
     * 
     * @param int $from Starting Transaction ID
     * @param int $count Amount of transactions to retrieve
     * @param int $from_id The ID of the transaction to start displaying with
     * @param int $end_id The ID of the transaction to finish displaying with
     * @param ASC/DESC $order sorting
     * @param UNIXtime $since When to start displaying?
     * @param UNIXtime $end When to finish displaying?
     * @return array( type(type of transaction), amount, currency, desc(description)
     * @return status(status of transaction), timestamp(time executed) )
     */
    function TransHistory($from = null, $count = null, $from_id = null, $end_id = null,
        $order = null, $since = null, $end = null)
    {
        $param = array(
            "from" => $from,
            "count" => $count,
            "from_id" => $from_id,
            "end_id" => $end_id,
            "order" => $order,
            "since" => $since,
            "end" => $end);
        $data = $this->query("TransHistory", $param);
        return $data['return'];
    }

    /**
     * BTCETrade::TradeHistory()
     * Returns trade history
     * @param int $from Starting Transaction ID
     * @param int $count Amount of transactions to retrieve
     * @param int $from_id The ID of the transaction to start displaying with
     * @param int $end_id The ID of the transaction to finish displaying with
     * @param ASC/DESC $order sorting
     * @param UNIXtime $since When to start displaying?
     * @param UNIXtime $end When to finish displaying?
     * @param BTC_USD $pair Currency pair, BTC_USD/BTC_LTC etc.
     * @return array( pair(currency pair) type(type of transaction), amount, rate,
     * @return order_id, is_your_order, timestamp(time executed) )
     */
    function TradeHistory($from = null, $count = null, $from_id = null, $end_id = null,
        $order = null, $since = null, $end = null, $pair = null)
    {
        $param = array(
            "from" => $from,
            "count" => $count,
            "from_id" => $from_id,
            "end_id" => $end_id,
            "order" => $order,
            "since" => $since,
            "end" => $end,
            "pair" => $pair);
        $data = $this->query("TradeHistory", $param);
        return $data['return'];
    }
    /**
     * BTCETrade::OrderList()
     * Returns your open orders/the orders history
     * @param int $from the number of the order to start displaying with
     * @param int $count Amount of orders to retrieve
     * @param int $from_id id of the order to start displaying with
     * @param int $end_id id of the orde? to finish displaying
     * @param ASC/DESC $order sorting
     * @param UNIXtime $since When to start displaying?
     * @param UNIXtime $end When to finish displaying?
     * @param BTC_USD $pair Currency pair, BTC_USD/BTC_LTC etc.
     * @param 1/0 $active Display active orders only
     * @return array( pair(currency pair) type(type of transaction), amount, rate,
     * @return timestamp_created, status )
     */
    function OrderList($from = null, $count = null, $from_id = null, $end_id = null,
        $order = null, $since = null, $end = null, $pair = null, $active = null)
    {
        $param = array(
            "from" => $from,
            "count" => $count,
            "from_id" => $from_id,
            "end_id" => $end_id,
            "order" => $order,
            "since" => $since,
            "end" => $end,
            "pair" => $pair,
            "active" => $active);
        $data = $this->query("OrderList", $param);
        return $data['return'];
    }

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
        if (isset($pair, $type, $rate, $amount)) {
            $param = array(
                "pair" => $pair,
                "type" => $type,
                "rate" => $rate,
                "amount" => $amount);
            $data = $this->query("Trade", $param);
            return $data['return'];
        } else {
            die("Please fill in ALL parameters.");
        }
    }

    /**
     * BTCETrade::CancelOrder()
     * Cancel an order
     * @param int $order_id Order ID of TX to cancel
     * @return array( order_id, funds{usd,btc,sc,ltc,ruc,nmc} )
     */
    function CancelOrder($order_id)
    {
        if (isset($order_id)) {
            $param = array("order_id" => $order_id);
            $data = $this->query("CancelOrder", $param);
            return $data['return'];
        } else {
            die("Please fill in the order id.");
        }
    }
}
