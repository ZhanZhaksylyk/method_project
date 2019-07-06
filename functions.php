<?php

function getRestsFromPage($page)
{
    
    $pattern = '/<div class="place-story" data-site-id="[0-9]+">/u';
    $subject = file_get_contents('https://restoran.kz/restaurant?page=' . $page);
    
    $sections = preg_split($pattern, $subject);
    unset($sections[0]);
    
    $rests = [];
    foreach ($sections as $k => $section) {
        $pattern = '/<a class="place-story__title__link" href="(\/restaurant\/[0-9a-z\-]{1,})">(.{1,}?)<\/a>/u';
        $result = [];
        preg_match_all($pattern, $section, $result);
    
        $rest = [
            'name' => $result[2][0],
            'link' => $result[1][0],
        ];
        
        $pattern = '/<dl class="row place-story__param"><dt class="col-xs-5 col-sm-3 place-story__param__title">(.{1,}?)<\/dt><dd class="col-xs-7 col-sm-9 place-story__param__content">(.{1,}?)<\/dd><\/dl>/u';
        $result = [];
        preg_match_all($pattern, $section, $result);
    
        $paramsMap = [
            'Кухня' => 'cuisine',
            'Средний счёт' => 'price',
            'Время работы' => 'worktime',
            'Адрес' => 'address'
        ];
    
        foreach ($paramsMap as $k => $v) {
            $index = array_search($k, $result[1]);
            if ($index !== false) {
                $rest[$v] = $result[2][$index];
            }
        }
        $pattern = '/[0-9]+/u';
        $result = [];
        preg_match_all($pattern,$rest['price'],$result);
        $rest['price'] = [
            'min' => $result[0][0],
            'max' => isset($result[0][1]) ? $result[0][1] : $result[0][0],
        ];

        $rest['cuisine'] = preg_split('/[, ]+/u', $rest['cuisine']);
    
        $rests[] = $rest;
    }

    return $rests;
}

function getMaxPage($page)
{
    return 2;
    $pattern = '/\/restaurant\?page=([0-9]+)/u';
    $subject = file_get_contents('https://restoran.kz/restaurant?page=' . $page);
    $result = [];
    preg_match_all($pattern, $subject, $result);
    
    $max = $result[1][0];
    foreach ($result[1] as $digit) {
        if ($digit > $max) {
            $max = $digit;
        }
    }

    if ($max == $page) {
        return $page;
    } else {
        return getMaxPage($max);
    }
}

?>
