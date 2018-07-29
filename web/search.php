<?php
/**
 * Created by PhpStorm.
 * User: Lombardo
 * Date: 29/07/18
 * Time: 13:02
 */


function curlGetRequest($url_path_str, $headers = array(), $additionalOpts = array()) {
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url_path_str);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    foreach ($additionalOpts as $k => $v) {
        curl_setopt($ch, $k, $v);
    }

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    return $output;
}

if ($_REQUEST['field'] && $_REQUEST['content'])
{
    $q = "http://elasticsearch:9200/documents/repository/_search?q=".rawurlencode($_REQUEST['field']).":".rawurlencode($_REQUEST['content'])."&pretty=true";
    $res = curlGetRequest(
        $q,
        Array('Content-Type: application/json', 'Expect:'),
        Array(CURLOPT_USERPWD =>  "elastic:changeme")
    );
    header('Content-Type: application/json');
    echo $res;
} else {
    ?>
<form method="post" action="search.php">
    <input type="text" name="field" value="content"/>
    <input type="text" name="content" value="Giuliano"/>
    <input type="submit" />

</form>
<?php
}

?>


