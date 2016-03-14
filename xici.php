<?php

require_once(dirname(__FILE__).'/phpQuery.php');

/**
 * Class IpFetch
 */
class XiciIpFetch {

    /**
     * @var string
     * 抓取网站的url
     */
    public $web_site_url = "";

    public $web_dom_tree = "";

    public $pages = 0;

    /**
     * @var string
     * 抓取所用的user_agent
     */
    public $user_agent = "";

    /**
     * @var array
     * 存贮数据库配置
     */
    private $database_config = array();

    /**
     * @var string
     * 代理 地址   http://192.168.1.1:8080
     */
    public $proxy = "";




    /**
     * IpFetch constructor.
     */
    function __construct(array $options = array())
    {
        $vars = get_class_vars(XiciIpFetch);
        foreach($options as $key => $value)
        {
            if($value && in_array($key,$vars)) $this->$key = $value;
        }
        if(!$this->pages) $this->getPages();
    }

    function run()
    {


    }

    public function fetchOnePage($page)
    {

        $ip_infos = array();

        $url = $this->web_site_url.$page;

        $result = $this->curl_string($url);
        phpQuery::newDocumentHTML($result);
        $trs = pq('#ip_list .odd');
        foreach($trs as $key => $tr)
        {
            $td = pq($tr)->find("td");
            $ip_info['country']     = $td->find("img")->attr("alt");  // 国家
            $ip_info['ip']          = $td->eq(2)->text();             // IP
            $ip_info['port']        = $td->eq(3)->text();             // 端口 port
            $ip_info['province']    = trim($td->eq(4)->text());       // 省份 province
            $ip_info['is_hide']     = trim($td->eq(5)->text());       // 是否高匿
            $ip_info['check_time']  = trim($td->eq(9)->text());       // 验证时间

            $ip_infos[$key] = $ip_info;
        }

        return $ip_infos;

    }

    private function getPages()
    {
        if($this->pages > 0) return $this->pages;

        $result = $this->curl_string();
        phpQuery::newDocumentHTML($result);
        $this->pages = intval(pq('.next_page')->prev()->text());
        return $this->pages > 0 ? true : false ;
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
    function curl_string ($url = "",$user_agent = "",$proxy = "")
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

    $config = array(
        "web_site_url"  => 'http://www.xicidaili.com/nn/',
        "user_agent"    => "User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50"
    );

    $xici = new XiciIpFetch($config);

