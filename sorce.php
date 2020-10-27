<?php

require "api.php";
require "Models/orders.php";
require "Models/item.php";
require "Models/extra.php";
require "Models/potato.php";
require "Models/drink.php";


$api_url = "http://api.burgerfirefly.com/v2/old-orders";
$headers = array(
    "Content-Type: application/json",
    "Authorization: Bearer 9UgAoSno8nrMFnV8rBen33P_N99COfRw5X1kGsgP",
);

$results = CallAPI('get', $api_url, $headers, false);

$object = json_decode($results);

if ($object instanceof stdClass) {
    if ($object->success) {

        $ordersArr = [];
        foreach ($object->data as $datum) {

            $order = new Order();
            $order->id = $datum->id;
            $order->orderNumber = $datum->orderNumber;
            $order->deliveryDate = $datum->deliveryDate;
            $order->orderTime = $datum->orderTime;
            $order->orderType = $datum->orderType;
            $order->deliveryPrice = $datum->deliveryPrice;
            $order->orderTotal = $datum->orderTotal;


            $ordersArr['order'][] = $order;
            if (isset($datum->items) && !empty($datum->items)) {
                foreach ($datum->items as $item) {
                    $itemClass = new Item();
                    $itemClass->name = $item->name;
                    $itemClass->arabicName = $item->arabicName;
                    $itemClass->quantity = $item->quantity;
                    $itemClass->price = $item->price;
                    $itemClass->note = $item->note;

                    $ordersArr['items'][] = $itemClass;

                    if (isset($item->extras) && !empty($item->extras)) {
                        foreach ($item->extras as $extra) {
                            $extraClass = new extra();
                            $extraClass->name = $extra->name;
                            $extraClass->price = $extra->price;

                            $ordersArr['extras'][] = $extraClass;
                        }
                    }

                    if (isset($item->potato) && !empty($item->potato)) {
                        foreach ($item->potato as $potato) {
                            $potatoClass = new potato();
                            $potatoClass->name = $potato->name;
                            $potatoClass->price = $potato->price;

                            $ordersArr['potato'][] = $potatoClass;
                        }
                    }

                    if (isset($item->drinks) && !empty($item->drinks)) {
                        foreach ($item->drinks as $drink) {
                            $drinkClass = new drink();
                            $drinkClass->name = $drink->name;
                            $drinkClass->price = $drink->price;

                            $ordersArr['drinks'][] = $drinkClass;
                        }
                    }
                }
            }
        }
    }
}


try{
    $db = new SQLite3('app.sq3');
    $sql = "INSERT INTO  [dbo].[SalesOrderMaster] (
INV_NO,INV_PAYNO,INV_DATE,INV_CUSNO,INV_STONO,INV_ORDDATE,INV_NOTE,INV_TTOTAL,INV_GTOTAL,INV_USENO,INV_CURRENCY,INV_RATE,
INV_COSNO,INV_CASH,INV_CHEQ,INV_TBLNO,INV_ORDTYP,INV_CHARGES,INV_ORIG,INV_DESPER,INV_STOTAL,INV_DTOTAL,INV_ORDNO,INV_CLOSED,
INV_NOTE2,INV_PRINTED,INV_NO2,INV_ACCNO,INV_EMP,INV_STATUS) VALUES )
1,1,'2020-10-18 17:17:17', 'test',1,'2020-10-18 17:17:17','test',11.6,11.6,1,1,11.6,1,11.6,11.6,1,1,11.6,'1',11.6,11.6,11.6,
1,'a','test', 'a', 'test','test','test','test','test','test',1)";
    $result = $db->exec($sql);
    if($result){
        unset($db);
        echo "DONE ....... \n";
    }else{
        echo $db->lastErrorMsg() . "\n";
        unset($db);
    }
}catch(Exception $exception){
    print_r($exception->getMessage());
}


