<?php
if ($_GET['type'] == '1') {
    $params = array(
        'police-number' => '65717242',
        'name' => 'Дмитрий',
        'lastname' => 'Кочик',
        'patronymic' => 'Николаевич',
        'birthdate' => '1986-06-26',
        'phone' => '7967-041-85-08',
        'email' => 'KochikDN@Vzr.ru'
    );
} else if ($_GET['type'] == '2') {
    $params = array(
        'lastname' => '1',
        'name' => '1',
        'patronymic' => '1',
        'birthdate' => '1',
        'police-number' => '35060021',
        'police-id' => '77659925',
        'phone' => '89149568191',
        'email' => 'kiselevmg@Vzr.ru'
    );
} else if ($_GET['type'] == '3') {
    $params = array(
        'lastname' => 'Суворова',
        'name' => 'Евгения',
        'patronymic' => 'Олеговна',
        'birthdate' => '1987-01-30',
        'police-number' => '33237477',//'33237474',//'62519201',//'33237474',
        'police-id' => '66898597',
        'phone' => '89111842810',//$_GET['phone'],
        'email' => 'SubbotinIV@Vzr.ru'
    );
}

$string = http_build_query($params);
$pkeyid = openssl_pkey_get_private(file_get_contents(__DIR__ . "/../dsa_priv.pem")); //путь к приватному ключу
$rawString = $string . date('d-m-Y');
openssl_sign($string . date('d-m-Y'), $signature, $pkeyid); //шифруем. Добавляем в шифруемую строку текущую дату, чтобы ограничить время жизни ссылки

$signature = base64_encode($signature);
$string .= "&key=" . $signature; //получаем "хвост" ссылки
var_dump($string);

echo '<br>----------------------<br>';

$key = openssl_pkey_get_public(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../dsa_pub.pem"));
var_dump(openssl_verify($string, base64_decode($signature), $key));
