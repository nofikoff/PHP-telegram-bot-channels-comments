<?php

session_start();

if (isset($_GET['exit'])) {
    session_destroy();
    header("Location: https://pogonyalo.com/test/chernovtsy/comments.php");
    exit;
}

if (isset($_SESSION['isauthorized']) and $_SESSION['isauthorized']) {
    // данне из телеграмма через сессию на сервере
    print_r($_SESSION);
    echo "<a href='?exit=1'>ВЫХОД</a>";

} else {
    // НЕ АВТОРИЗИРОВАН
    echo "Для авторизации передйите по этой ссылке в телеграм мессендежер и там нажми СТАРТ <a id='authlink' href='tg://resolve?domain=ChernivtsiTheBest_bot&start=auth%3D' >АВТОРИЗОВАТЬСЯ</a>";
}
?>

<script>
    // подставляет имя сессии в адрес авторизации
    cookie = key => (new RegExp(key + '=(.*?); ', 'gm')).exec(document.cookie + '; ')[1]
    authlink.href += cookie('PHPSESSID');
</script>






