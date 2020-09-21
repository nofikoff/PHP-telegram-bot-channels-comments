<?php

class CommentsBot
{
    public $params, $telegramRequest;

    public function __construct($params)
    {
        $this->params = $params;
        $this->telegramRequest = @json_decode(file_get_contents("php://input"));
        $this->request_incoming();
    }


    public function request_incoming()
    {
        // все другие пост гет запросы на вебхуке
        if ($this->telegramRequest) {
            $this->logs("telegramIncome.txt", print_r($this->telegramRequest, TRUE));

            //  ШАГ 001 - если ппользоватьль набрал /start или перешел по ссылке вида tg://resolve?domain=ChernivtsiTheBest_bot&start=auth%3D11111
            if (strpos($this->telegramRequest->message->text, "/start") === 0) {

                if (preg_match('/\/start auth=(.*)/', $this->telegramRequest->message->text, $keySession)) {
                    // на входе по ссылке пользователь передал нам номер свой ессии из cookes
                    // использкем его для моджификации хранилища сесии на стороне сервера
                    session_id($keySession[1]);
                    session_start();

                    $_SESSION['isauthorized'] = 1;
                    $_SESSION['telegram_id'] = $this->telegramRequest->message->chat->id;
                    $_SESSION['first_name'] = $this->telegramRequest->message->chat->first_name;
                    $_SESSION['last_name'] = $this->telegramRequest->message->chat->last_name;
                    $_SESSION['username'] = $this->telegramRequest->message->chat->username;

                    $this->telegramSend(
                        $this->telegramRequest->message->chat->id,
                        "Вы авторизированы, можете вернуться на страницу: https://pogonyalo.com/test/chernovtsy/comments.php  \n");

                } else {
                    $this->telegramSend(
                        $this->telegramRequest->message->chat->id,
                        "Сбой авторизации https://pogonyalo.com/test/chernovtsy/ \n" . print_r($keySession, true));
                }

            } elseif (isset($this->telegramRequest->message->text)) {
                $this->telegramSend(
                    $this->telegramRequest->message->chat->id,
                    "Не понял"
                );

            }
        }


    }


    // отправитель сообщений
    function telegramSend($telegramchatid, $msg, $button = [], $apiMethod = 'sendMessage', $editMessageID = '')
    {
        $this->logs("myRequest.txt", print_r([$this->telegramRequest->message->chat->id, $msg], TRUE));

        $url = 'https://api.telegram.org/bot' . $this->params->APIKey . '/' . $apiMethod;
        //
        $list = [];
        // если не массив
        if (!is_array($telegramchatid)) $list[] = $telegramchatid;
        else $list = $telegramchatid;


        foreach ($list as $chatId) {
            $data = [
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'html',
//                'parse_mode' => 'markdown',
                'disable_web_page_preview' => 1,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $button
                ]),
                'message_id' => $editMessageID
            ];

            $options = ['http' =>
                [
                    'method' => 'POST',
                    'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query($data),
                ],
            ];
            $context = stream_context_create($options);
            $responce = file_get_contents($url, false, $context);
            //
            $this->logs("sendmessagetotelgramServer.txt", print_r($data, true));
            $this->logs("telegramResponseOnMyRequest.txt", $responce);
        }

        echo('{"message":"Успешно отправлено"}');

    }

    private function logs($filelog_name, $message)
    {
        $fd = fopen(__DIR__ . "/logs/" . $filelog_name, "a");
        fwrite($fd, date("Ymd-G:i:s")
            . " -------------------------------- \n\n" . $message . "\n\n");
        fclose($fd);
    }

}
