<?php

namespace app\core\system\install\steps\addon;


class FillBase{

    public static function fill($db, $PREFIX){


        $query = $db->prepare("INSERT INTO {$PREFIX}roles
                (name, rules)
            VALUES (?, ?)");
        $query->execute(['Администратор', null]);
        $query->execute(['Пользователь', null]);
    }
}