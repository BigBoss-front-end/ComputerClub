<?php

if (!function_exists('human_date')) {
    function human_date($date)
    {
        $date = Carbon\Carbon::parse($date);

        // Проверяем, является ли дата сегодняшней
        if ($date->isToday()) {
            // Если да, выводим время
            $formattedDate = 'сегодня в ' . $date->format('H:i');
        } elseif ($date->isYesterday()) {
            // Если дата была вчера, выводим "вчера в" и время
            $formattedDate = 'вчера в ' . $date->format('H:i');
        } else {
            // Если дата не сегодня и не вчера, выводим полную дату и время
            $formattedDate = $date->format('d.m.Y в H:i');
        }

        return $formattedDate; // Выведет "вчера в 14:15"
    }
}

if (!function_exists('human_decimal')) {
    function human_decimal($decimal)
    {
        return number_format($decimal, 1, '.', '');
    }
}

if(!function_exists('phone_mask')) {
    function phone_mask($phone) 
    {
        switch (strlen($phone)) {
            case '9':
                return '+7 (9' . $phone[0] . $phone[1] . ') ' . $phone[2] . $phone[3] . $phone[4] . '-' . $phone[5] . $phone[6] . '-' . $phone[7] . $phone[8];
                break;
            case '10':
                return '+7 (' . $phone[0] . $phone[1] . $phone[2] . ') ' . $phone[3] . $phone[4] . $phone[5] . '-' . $phone[6] . $phone[7] . '-' . $phone[8] . $phone[9];
                break;
            case '11':
                if ($phone[0] == '7') {
                    return '+' . $phone[0] . ' (' . $phone[1] . $phone[2] . $phone[3] . ') ' . $phone[4] . $phone[5] . $phone[6] . '-' . $phone[7] . $phone[8] . '-' . $phone[9] . $phone[10];
                } elseif ($phone[0] == '8') {
                    return '+7' . ' (' . $phone[1] . $phone[2] . $phone[3] . ') ' . $phone[4] . $phone[5] . $phone[6] . '-' . $phone[7] . $phone[8] . '-' . $phone[9] . $phone[10];
                }
                break;
            case '12':
                return $phone[0] . $phone[1] . ' (' . $phone[2] . $phone[3] . $phone[4] . ') ' . $phone[5] . $phone[6] . $phone[7] . '-' . $phone[8] . $phone[9] . '-' . $phone[10] . $phone[11];
                break;
            default:
                return $phone;
                break;
        }
    }
}

if(!function_exists('phone_unmask')) {
    function phone_unmask($phone) 
    {
        return str_replace(['+', '-', '(', ')', ' '], '', $phone);
    }
}



if (!function_exists('truncateWithEllipsis')) {
    /**
     * Обрезает строку до указанной длины и добавляет троеточие в конце.
     *
     * @param string $text Исходная строка.
     * @param int $length Максимальная длина строки.
     * @param string $ellipsis Строка, которую нужно добавить в конце (по умолчанию '...').
     * @return string Обрезанная строка.
     */
    function truncateWithEllipsis($text, $length = 84, $ellipsis = '...')
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $ellipsis;
    }
}