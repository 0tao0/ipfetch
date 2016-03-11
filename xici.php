<?php

    $url = 'http://www.xicidaili.com/nn/';
    $agent = "User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50";
    $result = curl_string($url,$agent);
    $time = time() + microtime();
    phpQuery::newDocumentHTML($result);
    $companies = pq('#ip_list .odd');
    foreach($companies as $company)
    {
        $td = pq($company)->find("td");
        echo $td->find("img")->attr("alt")."<br>";  // 国家
        echo $td->eq(2)->text()."<br>";             // IP
        echo $td->eq(3)->text()."<br>";             // 端口 port
        echo trim($td->eq(4)->text())."<br>";       // 省份 province
        echo trim($td->eq(5)->text())."<br>";       //  是否高匿
        echo trim($td->eq(9)->text())."<br>";       // 添加时间
    }
    $time = time() + microtime() - $time;