<?php

namespace Core;

use Core\ErrorHandler;
use Core\Http;
use Core\Security;
use Core\Proxy;

class LocationServices extends ErrorHandler
{
    public $ip = null;
	public $currency = BASE_CURRENCY;
    public $lang = "en";
    public $host = "http://www.geoplugin.net/php.gp?";
    public $country = null;

    /****************************************** */
    public $http;
    public $locationInfo = [];
    public $response = null;
    public $error = array();

    /****************************************** */

    public $proxy;
    public $customIP = null;

    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->http = new Http;
        $this->proxy = new Proxy;
    }
        
    /**
     * _getIP
     *
     * @return void
     */
    private function _getIP()
    {
        if(PROXY)
        {
            return $this->proxy->getProxyIP(PROXY_COUNTRY);
        }
        else if(!is_null($this->customIP))
        {
            return $this->customIP;
        }
        else
        {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * getUserLocation
     *
     * @param  mixed $country
     * @return void
     */
    public function getUserLocation($country = null)
    {
        $this->country = $country;

        $ip = "ip=".$this->_getIP();
        $baseCurrency = "base_currency=".$this->currency;
        $language = "lang=".$this->lang;

        $url = $this->host.$ip.'&'.$baseCurrency.'&'.$language;

        if(!$this->isBot())
        {
            $exec = $this->http->setHttpOptions($url)->executeHttpRequest();
            $res = $exec->httpResponse();

            $this->error = $exec->httpError();

            $this->response = \unserialize($res);
        }

        return $this;
    }

    public function convertCurrency($amount, $float=2, $symbol=true, $num_format = true)
    {
        //easily convert amounts to geolocated currency.
		if (!is_numeric($this->locationInfo->currencyConverter) || $this->locationInfo->currencyConverter == 0) 
		{
			trigger_error('currencyConverter has no value.', E_USER_NOTICE);
			//return $amount;
		}
		
		if ( !is_numeric($amount) ) 
		{
			trigger_error('The amount passed is not numeric.', E_USER_WARNING);
			//return $amount;
		}
		
		if ( $num_format === true ) 
		{
			if ( $symbol === true ) 
			{
				return $this->locationInfo->currencySymbol .' '. number_format(($amount * $this->locationInfo->currencyConverter), $float);
			} 
			else 
			{
				return number_format(($amount * $this->locationInfo->currencyConverter), $float);
			}
		}
		else
		{
			return round(($amount * $this->locationInfo->currencyConverter), $float);
		}
    }
    public function placesNearBy($radius=10, $limit=null)
    {
        if ( !is_numeric($this->locationInfo->latitude) || !is_numeric($this->locationInfo->longitude) ) 
		{
			trigger_error ('Incorrect latitude or longitude values.', E_USER_NOTICE);
			return array();
		}
		
        $host = "http://www.geoplugin.net/extras/nearby.gp?lat={$this->locationInfo->latitude}&long={$this->locationInfo->longitude}&radius={$radius}";
		
		if ( is_numeric($limit) )
			$host .= "&limit={$limit}";
            

        if(!$this->isBot())
        {
            $exec = $this->http->setHttpOptions($host)->executeHttpRequest();
            $res = $exec->httpResponse();

            $this->error = $exec->httpError();

            return \unserialize($res);
        }
    }

    public function isBot()
    {
        $botRegexPattern = "(googlebot\/|Googlebot\-Mobile|Googlebot\-Image|Google favicon|Mediapartners\-Google|bingbot|slurp|java|wget|curl|Commons\-HttpClient|Python\-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST\-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub\.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum\.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips\-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail\.RU_Bot|discobot|heritrix|findthatfile|europarchive\.org|NerdByNature\.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb\-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web\-archive\-net\.com\.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks\-robot|it2media\-domain\-crawler|ip\-web\-crawler\.com|siteexplorer\.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki\-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e\.net|GrapeshotCrawler|urlappendbot|brainobot|fr\-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf\.fr_bot|A6\-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive\.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j\-asr|Domain Re\-Animator Bot|AddThis|YisouSpider|BLEXBot|YandexBot|SurdotlyBot|AwarioRssBot|FeedlyBot|Barkrowler|Gluten Free Crawler|Cliqzbot)";
		
		$bot = Security::getUserAgent();
	 
		return preg_match("/{$botRegexPattern}/", $bot);
    }

    public function locationResponse()
    {
        $this->locationInfo = [];
        
        $this->locationInfo["ip"] = $this->response["geoplugin_request"];
        $this->locationInfo["city"] = $this->response["geoplugin_city"];
        $this->locationInfo["countryName"] = $this->response["geoplugin_countryName"];
        $this->locationInfo["countryCode"] = $this->response["geoplugin_countryCode"];
        $this->locationInfo["region"] = $this->response["geoplugin_region"];
        $this->locationInfo["regionCode"] = $this->response["geoplugin_regionCode"];
        $this->locationInfo["regionName"] = $this->response["geoplugin_regionName"];
        $this->locationInfo["continentCode"] = $this->response["geoplugin_continentCode"];
        $this->locationInfo["continentName"] = $this->response["geoplugin_continentName"];
        $this->locationInfo["longitude"] = $this->response["geoplugin_longitude"];
        $this->locationInfo["latitude"] = $this->response["geoplugin_latitude"];
        $this->locationInfo["timezone"] = $this->response["geoplugin_timezone"];
        $this->locationInfo["currencyCode"] = $this->response["geoplugin_currencyCode"];
        $this->locationInfo["currencySymbol"] = $this->response["geoplugin_currencySymbol"];
        $this->locationInfo["currencyConverter"] = $this->response["geoplugin_currencyConverter"];
        $this->locationInfo["locationAccuracyRadius"] = $this->response["geoplugin_locationAccuracyRadius"];
        $this->locationInfo["euVAT"] = $this->response["geoplugin_euVATrate"];
        $this->locationInfo["isEU"] = $this->response["geoplugin_inEU"];

        $tmp = \json_encode($this->locationInfo);
        return $this->locationInfo = \json_decode($tmp);
    }

    public function getCurrentTimeDate()
    {
        if(!empty($this->locationInfo))
        {
            date_default_timezone_set($this->locationInfo->timezone);
            return date("Y-m-d H:i:s");
        }

        $this->error = array("Message" => "Timezone is not set");
    }
    public function e()
    {

    }
    
}

?>