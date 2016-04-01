<?php

require_once(dirname(__FILE__).'/phpQuery.php');

require_once(dirname(__FILE__)."/Db.php");

/**
 * Class IpFetch
 */
class XiciIpFetch {

    /**
     * @var string
     * 抓取网站的url
     */
    public $web_site_url = "http://www.xicidaili.com/nn/";

    public $pages = 0;

    /**
     * @var string
     * 抓取所用的user_agent
     */
    public $user_agent = "User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50";

    /**
     * @var string
     * 代理 地址   http://192.168.1.1:8080
     */
    public $proxy = "";

    public $db;




    /**
     * IpFetch constructor.
     */
    function __construct(array $options = array())
    {
        foreach($options as $key => $value)
        {
            if($value) $this->$key = $value;
        }
        if(!$this->pages) $this->getPages();

        $this->db = new Db();

    }

    function run()
    {

        for($i = 1;$i<=$this->pages;$i++)
        {
            echo "当前第 {$i} 页\r\n";
            $this->fetchOnePage($i);
            sleep(5);
        }
    }

    public function fetchOnePage($page)
    {

        $ip_infos = array();

        $url = $this->web_site_url.$page;
        $result = $this->curl_string($url,$this->user_agent);
        phpQuery::newDocumentHTML($result);
        $trs = pq('#ip_list .odd');
        foreach($trs as $key => $tr)
        {
            $td = pq($tr)->find("td");
            $ip_info['website']     = 1;
            $ip_info['country']     = $td->find("img")->attr("alt");  // 国家
            $ip_info['ip']          = $td->eq(2)->text();             // IP
            $ip_info['ip2long']     = ip2long($ip_info['ip']);
            $ip_info['port']        = $td->eq(3)->text();             // 端口 port
            $ip_info['province']    = trim($td->eq(4)->text());       // 省份 province
            $ip_info['type']        = trim($td->eq(5)->text()) == "高匿"?1:2;
            $ip_info['check_time']  = @strtotime(trim($td->eq(9)->text()));       // 验证时间
            $ip_info['create_time'] = time();
//            $ip_info['geo']         = $this->ipGeo($ip_info["province"]);

            $ip_infos[] = $this->db->insert($ip_info,"ip_list");
            echo $ip_info['ip']."\r\n";
        }
        return $ip_infos;
    }

    private function getPages()
    {
        if($this->pages > 0) return $this->pages;

        $result = $this->curl_string($this->web_site_url,$this->user_agent);
        phpQuery::newDocumentHTML($result);
        $this->pages = intval(pq('.next_page')->prev()->text());
        return $this->pages;
    }

    private function ipGeo($province)
    {
        if(!$province) return false;

        $province = json_decode(json_encode($province));
        $url = "http://api.map.baidu.com/geocoder/v2/?output=json&ak=FC755f71e368549753d9c73e6ca9a698&address=".urlencode($province);
        $result = file_get_contents($url);
        $geoInfo = json_decode($result,true);
        if($geoInfo['status'] != 0) return false;

        return $geoInfo['result']['location'];
    }

    private function curl_string ($url = "",$user_agent = "",$proxy = "")
    {

        if(!$url) $url = $this->web_site_url;

        $ch = curl_init();
        if($proxy) curl_setopt ($ch, CURLOPT_PROXY, $proxy);
        curl_setopt ($ch, CURLOPT_URL, $url);
        if($user_agent) curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt ($ch, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
        curl_setopt ($ch, CURLOPT_HEADER, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;

    }

}

    $xici = new XiciIpFetch();

    $xici->run();

//    var_dump($info);

