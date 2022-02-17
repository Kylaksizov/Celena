<?php

namespace app\_classes;

/**
 * @name Authorization
 * ===================
 * @description Авторизация разными способами
 * @version 1.0.0
 * @author Kylaksizov
 * @TODO требуется доработка
 */
trait Auth{


    public static function google_init($type = 'reg'): string{

        $params = array(
            'client_id'     => CONFIG_SYSTEM["auth"]["googleClientId"],
            'redirect_uri'  => CONFIG_SYSTEM["auth"]["redirect_url"],
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'state'         => $type
        );

        $url = 'https://accounts.google.com/o/oauth2/auth?' . urldecode(http_build_query($params));

        // onclick="window.open(\'' . $url . '\', \'example\', \'width=600,height=400\');"

        return '<a href="' . $url . '" class="init_google">
            <svg viewBox="0 0 262 262" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" aria-hidden="true" focusable="false" width="24" height="24" role="img"><path d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027" fill="#4285F4"></path><path d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1" fill="#34A853"></path><path d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782" fill="#FBBC05"></path><path d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251" fill="#EB4335"></path></svg>
        </a>';
    }


    public static function google_callback(){

        if (!empty($_GET['code'])) {
            // Отправляем код для получения токена (POST-запрос).
            $params = array(
                'client_id'     => CONFIG_SYSTEM["auth"]["googleClientId"],
                'client_secret' => CONFIG_SYSTEM["auth"]["googleClientSecret"],
                'redirect_uri'  => CONFIG_SYSTEM["auth"]["redirect_url"],
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code']
            );

            $ch = curl_init('https://accounts.google.com/o/oauth2/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($data, true);
            if (!empty($data['access_token'])) {
                // Токен получили, получаем данные пользователя.
                $params = array(
                    'access_token' => $data['access_token'],
                    'id_token'     => $data['id_token'],
                    'token_type'   => 'Bearer',
                    'expires_in'   => 3599
                );

                $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
                $info = json_decode($info, true);

                echo "<pre>";
                print_r($info);
                echo "</pre>";
                exit;

                /*
                [id] => 108314916759934646748
                [email] => masterz1zzz@gmail.com
                [verified_email] => 1
                [name] => Владимир Кулаксизов
                [given_name] => Владимир
                [family_name] => Кулаксизов
                [picture] => https://lh3.googleusercontent.com/a-/AOh14GhoANkuzThi-NrC0y6m5aS8y_DBVaxUoQ5O5iE-hQ=s96-c
                [locale] => ru
                 */

                /*$AuthModel = new AuthModel(true);
                $is_reg = $AuthModel->test($info["name"], $info["email"]);

                if($is_reg){ // если пользователь уже зарегистрирован

                    SetCookie("user_id", $is_reg["id"], time() + 3600 * 24 * 7, "/"); // запоминаем на 7 дней
                    SetCookie("user_email", $is_reg["email"], time() + 3600 * 24 * 7, "/"); // запоминаем на 7 дней
                    SetCookie("user_hash", $is_reg["hash"], time() + 3600 * 24 * 7, "/"); // запоминаем на 7 дней

                } else{

                    $avatar_name = Functions::generationCode(15);

                    $avatar = !empty($info["picture"]) ? $avatar_name.'.jpg' : '';

                    $AuthCreate = $AuthModel->create($info["name"], $info["email"], '', '', 2, null, $avatar, 1);

                    // если зарегистрирован
                    if(!empty($AuthCreate["id"])){

                        System::sendMe("Новая регистрация через Google: " . $info["name"] . ' ' . $info["email"], 'success');

                        $body = 'Приветствуем <b>'.$info["given_name"].'</b>!<br>
                        Вы успешно зарегистрировались в сервисе NexCompany.<br><br>
                        <b>Ваш доступ в <a href="https://nex.company/">личный кабинет</a></b><br>
                        <b>Логин:</b> ' . $info["email"] . '<br>
                        <b>Пароль:</b> ' . $AuthCreate["password"] . '<br><br>
                        Пароль Вы можете поменять в личном кабинете.';

                        System::send(SYSTEM_CONFIG, null, $info["email"], 'Регистрация в NexCompany', $body);

                        if(!empty($info["picture"])) copy($info["picture"], ROOT . '/public/uploads/users/'.$AuthCreate["id"].'/'.$avatar);

                        SetCookie("user_id", $AuthCreate["id"], time() + 3600 * 24 * 7, "/"); // запоминаем на 7 дней
                        SetCookie("user_email", $AuthCreate["email"], time() + 3600 * 24 * 7, "/"); // запоминаем на 7 дней
                        SetCookie("user_hash", $AuthCreate["hash"], time() + 3600 * 24 * 7, "/"); // запоминаем на 7 дней
                        header("Location: /");
                        die();

                        // SmsFly::send("+380952059675", "Ваш код для регистрации: " . $AuthCreate["cod"]);

                    } else{

                        System::sendMe("Не удалось зарегистрироваться через Google: " . $info["name"] . ' ' . $info["email"], 'error');
                    }
                }

                header("Location:/");
                exit;*/
            }
        }
    }

}