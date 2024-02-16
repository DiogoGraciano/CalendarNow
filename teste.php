<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://homologacao.rarissimajoias.com.br/webservice/v2/vendedor/index.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'[
   {
      "codigo":"1",
      "nome":"Teste",
      "comissao":50.01
   },
   {
      "codigo":"2",
      "nome":"Teste3",
      "comissao":41.01
   }
]
   ',
  CURLOPT_HTTPHEADER => array(
    'Authorization: ddo7o47gp6oaxxge4htu9p28qhv6h2kbuye36jd4zl09w6uinnidj6p8c6c7x62tjp8b0r0j2kqon4n09imddtg9eulcql0spq8vh41iokqljb1ah81lkt3ebcaaifk341jmsroyhrtxg317',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$error_msg = curl_error($curl);

curl_close($curl);
echo($response);
