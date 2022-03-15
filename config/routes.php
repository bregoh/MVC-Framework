<?php

/**
*** ([a-z]+)/(\d+) =  product/3
*** (:any) =  any string, usually one
*** (:num) =  any number, usually a lot of paths
*** (.+) =  anything
*** if link contains 'api', urlbit will remove the api and return a key value array
*** example: example.com/api/test/2/testa/3/testb/4
*** urlbits: [test]=>2, [testa]=>3, [testb]=>4
*** Always do string validation for every request
**/

//$routes['(:any)/(:any)'] = 'pages/index';

$routes["logout"] = 'User_Controller/logout';
?>
