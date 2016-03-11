<?php
namespace Vender;
    /**
     * Class IpFetch
     */
    class IpFetch {

        /**
         * @var string
         * 抓取网站的url
         */
        public $web_site_url = "";

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
        function __construct(array $options)
        {
            
        }

        public static function fetch()
        {

        }

        private function getPages()
        {

        }

        private function curl_string ($url,$user_agent = "",$proxy = ""){

            $ch = curl_init();
            if($proxy) curl_setopt ($ch, CURLOPT_PROXY, $proxy);
            curl_setopt ($ch, CURLOPT_URL, $url);
            if($user_agent) curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
//        curl_setopt ($ch, CURLOPT_COOKIEJAR, "c:\cookie.txt");
            curl_setopt ($ch, CURLOPT_HEADER, 1);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
            $result = curl_exec ($ch);
            curl_close($ch);
            return $result;

        }

    }