<?php

/*
array(3) {
  [0]
  array(3) {
    [0]
    array(2) {
      ["listElemId"] = (2)
      ["listElemPoints"] = (1)
    }
    [1]
    array(2) {
      ["listElemId"] (3)
      ["listElemPoints"] (2)
    }
    [2]
    array(2) {
      ["listElemId"] (1)
      ["listElemPoints"] (3)
    }
  }
  [1]
  array(3) {
    [0]
    array(2) {
      ["listElemId"] (3)
      ["listElemPoints"] (1)
    }
    [1]
    array(2) {
      ["listElemId"] (2)
      ["listElemPoints"] (2)
    }
    [2]
    array(2) {
      ["listElemId"] (1)
      ["listElemPoints"] (3)
    }
  }
  [2]
  array(3) {
    [0]
    array(2) {
      ["listElemId"] (3)
      ["listElemPoints"] (1)
    }
    [1]
    array(2) {
      ["listElemId"] (1)
      ["listElemPoints"] (2)
    }
    [2]
    array(2) {
      ["listElemId"] (2)
      ["listElemPoints"] (3)
    }
  }
}*/


public function CalculateOrder($listOrders) {

    foreach ($listOrders as $listOrder) {
      $i = 0;
      foreach ($listOrder as $listElem) {
        $orderPlaces[$i]['listElemId'] = $listElem['listElemId'];
        $orderPlaces[$i]['listElemPoints'] += $listElem['listElemPoints'];
        $i += 1;
      }
    }

    var_dump($orderPlaces);

    function subval_sort($a, $subkey) {
      foreach($a as $k=>$v) {
        $b[$k] = strtolower($v[$subkey]);
      }
      arsort($b);
      foreach($b as $key=>$val) {
        $c[] = $a[$key];
      }
      return $c;
    }

    $orderedList = subval_sort($orderPlaces, 'listElemPoints');

    for ($i = 0; $i < count($orderedList); $i++) {
      $orderedList[$i]['listElemOrderPlace'] = $i+1;
    }

    return $orderedList;
  }
