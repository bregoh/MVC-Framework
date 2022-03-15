<?php

namespace Core;

use Core\DB;

class Proxy
{
    public $country = null;
    public $ipList = [];

    public function getProxyIP($country)
    {
        $this->country = $country;
        return $this->_getCountryIPRange()->_generateIP();
    }

    private function _generateIP()
    {
        $newIP = array();

        $fromArray = \explode(".", $this->ipList["from"]);
        $toArray = \explode(".", $this->ipList["to"]);

        // From IP
        $fourthBitFrom = array_pop($fromArray); // get the last number in the ip
        $thirdBitFrom = array_pop($fromArray); // get the third number in the ip
        
        // To IP
        $fourthBitTo = array_pop($toArray); // get the last number in the ip
        $thirdBitTo = array_pop($toArray); // get the third number in the ip

        $third = rand(intval($thirdBitFrom), intval($thirdBitTo));
        $fourth = rand(intval($fourthBitFrom), intval($fourthBitTo));

        if(intval($fromArray[0]) !== intval($toArray[0]))
        {
            $first = rand(intval($fromArray[0]), intval($toArray[0]));
        }
        else
        {
            $first = intval($toArray[0]);
        }

        if(intval($fromArray[1]) !== intval($toArray[1]))
        {
            $second = rand(intval($fromArray[1]), intval($toArray[1]));
        }
        else
        {
            $second = intval($toArray[1]);
        }

        return implode(".", array($first, $second, $third, $fourth));
    }

    private function _getCountryIPRange()
    {
        $db = DB::getInstance();
        
        $res = $db->query("SELECT * FROM `ip2location_db1` WHERE country_name = '".$this->country."' ORDER BY RAND() LIMIT 1")->first();

        return $this->_intToIP($res->ip_from, $res->ip_to);
    }

    private function _intToIP($ipStart = "", $ipEnd = "")
    {
        $from = long2ip($ipStart);
        $to = long2ip($ipEnd);
        
        $this->ipList["from"] = $from;;
        $this->ipList["to"] = $to;

        return $this;
    }
}

?>