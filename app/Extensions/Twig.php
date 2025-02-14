<?php

namespace App\Extensions;

class Twig implements \Twig\Extension\ExtensionInterface
{
    public function getName() {
        return "AppTwigExtensions";
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new \Twig\TwigFilter("yn", function ($val) { return $val ? "Да" : "Нет"; } ),
            new \Twig\TwigFilter("dt_locale", function ($val) {
                
                $dtFormat = [
                    'en' => 'm/d/Y',
                    'de' => 'm/d/Y',
                    'ru' => 'd.m.Y',
                ];

                $loc = app()->getLocale();
                $dt = \DateTime::createFromFormat("Y-m-d", $val);
                
                $f =  $dtFormat[$loc] ?? "Y-m-d";
                
                return $dt->format($f);
            }),
            new \Twig\TwigFilter("autoreplace", function ($val) {
                $ar = \App\Models\AutoCorrect::all();
                
                foreach ($ar as $a) {
                    $val = str_replace($a->search, $a->replace, $val);
                }
                
                return $val;
            }),
            new \Twig\TwigFilter("account_num", function ($val) {
                return $val && strlen($val) == 20
                    ? substr($val, 0, 3)
                        . "." . substr($val, 3, 2)
                        . "." . substr($val, 5, 3)
                        . "." . substr($val, 8, 1)
                        . "." . substr($val, 9, 4)
                        . "." . substr($val, 13)
                    : "";

            } ),
            new \Twig\TwigFilter("dt_next", function ($val) {
                $dt = new \Datetime($val);
                $dt->add(new \DateInterval("P1D"));
                return $dt->format("Y-m-d");
            }),
            new \Twig\TwigFilter("dt_prev", function ($val) {
                $dt = new \Datetime($val);
                $dt->sub(new \DateInterval("P1D"));
                return $dt->format("Y-m-d");
            }),
            new \Twig\TwigFilter("min2hours", function ($val) {
                $h = floor($val / 60);
                $m = $val % 60;
                return round(($h*60 + $m) / 60, 1);
            }),
            new \Twig\TwigFilter("sec2str", function ($val) {
                $h = floor($val / 3600);
                $val -= $h*3600;
                $m = floor($val / 60);
                $val -= $m*60;

                return sprintf(
                    "%u ч. %u м. %u с.",
                    $h,
                    $m,
                    round($val)
                );
            }),
            new \Twig\TwigFilter("hm", function ($val) {

                if (!$val) {
                    return "00:00";
                }

                $h = 0;
                $m = 0;

                if ($val >= 60) {
                    $h = floor($val / 60);
                    $m = $val % 60;
                } else {
                    $m = $val;
                }

                if ($h < 10) {
                    $h = "0" . $h;
                }

                if ($m < 10) {
                    $m = "0" . $m;
                }

                return $h . ":" . $m /*. " (" . $val . ")"*/;
            } ),
            new \Twig\TwigFilter(
                'dtrus',
                function ($dt) {
                    return \KsUtils\Dt::int2rus($dt);
                }
            ),
            new \Twig\TwigFilter(
                'weekdaynum2rus',
                function ($num) {
                    $data = [
                        1 => "Пн",
                        2 => "Вт",
                        3 => "Ср",
                        4 => "Чт",
                        5 => "Пт",
                        6 => "Сб",
                        7 => "Вс",
                    ];
                    return $data[$num] ?? '???';
                }
            ),
            new \Twig\TwigFilter(
                'price_str',
                function ($num) {
                    $nul = 'ноль';
                        $ten = array(
                            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
                            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
                        );
                        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
                        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
                        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
                        $unit = array(// Units
                            array('копейка', 'копейки', 'копеек', 1),
                            array('рубль', 'рубля', 'рублей', 0),
                            array('тысяча', 'тысячи', 'тысяч', 1),
                            array('миллион', 'миллиона', 'миллионов', 0),
                            array('миллиард', 'милиарда', 'миллиардов', 0),
                        );
                        //
                        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
                        $out = array();
                        if (intval($rub) > 0) {
                            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                                if (!intval($v))
                                    continue;
                                $uk = sizeof($unit) - $uk - 1; // unit key
                                $gender = $unit[$uk][3];
                                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                                // mega-logic
                                $out[] = $hundred[$i1]; # 1xx-9xx
                                if ($i2 > 1)
                                    $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];# 20-99
                                else
                                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];# 10-19 | 1-9
                                // units without rub & kop
                                if ($uk > 1)
                                    $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                            } //foreach
                        } else
                            $out[] = $nul;
                        $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
                        $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
                        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
                    }
            ),
            new \Twig\TwigFilter(
                'dtshortrus',
                function ($dt) {
                    $monthNames = [
                        1 => "января",
                        2 => "февраля",
                        3 => "марта",
                        4 => "апреля",
                        5 => "мая",
                        6 => "июня",
                        7 => "июля",
                        8 => "августа",
                        9 => "сентября",
                        10 => "октября",
                        11 => "ноября",
                        12 => "декабря"
                    ];

                    return sprintf(
                        "%s %s %u",
                        date("d"),
                        $monthNames[intval(date("m"))],
                        date("Y")
                    );
                }
            )
        ];
    }
    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction(
                'num',
                function ($num, $precision = 0, $decSeparator = ",", $thousSeparator = " ") {

                    if (is_numeric($num)) {
                        return number_format($num, $precision, $decSeparator, $thousSeparator);
                    } elseif ($num) {
                        return "NaN: " . $num;
                    } else {
                        return "";
                    }
                }
            ),
            new \Twig\TwigFunction(
                "randpic",
                function () {
                    return sprintf("%02u", rand(1, 15));
                }
            ),
            new \Twig\TwigFunction(
                "t",
                function ($t, $code, $showEditButton = true) {
                    if (isset($t[$code][app()->getLocale()])) {
                        $locList = collect(\App\Models\Locale::active()->select('code')->get())->pluck('code');
                        $total = count($locList);
                        $filled = 0;
                        
                        foreach($t[$code] as $w) {
                            if (!empty($w)) {
                                $filled++;
                            }
                        }
                        
                        $color = '';
                        
                        if ($filled != $total) {
                            $color = ' style="color: #fff; background-color: #c00;"';
                        }
                        // dd(auth()->check(), !in_array($code, ['site_title']) , $showEditButton, \App\Services\SettingsService::get('show_translate_buttons'));
                        $editor = auth()->check() && !in_array($code, ['site_title']) 
                            && $showEditButton 
                            && \App\Services\SettingsService::get('show_translate_buttons')
                                ? ' <a href="' . route('admin.translate.edit', [ 'code' => $code ]) . '"' . $color . '><img src="/i/svg/pencil.svg" style="width: 16px;" /></a>'
                                : "";
                        return $t[$code][app()->getLocale()] . $editor;
                    }

                    if (isset($t[$code]['en'])) {
                        return $t[$code]['en'];
                    }

                    return '----';
                }, ['is_safe' => ['html']]
            ),
        ];
    }

    public function getNodeVisitors(): array {
        return [];
    }

    public function getOperators(): array {
        return [];
    }

    public function getTests(): array {
        return [];
    }

    public function getTokenParsers(): array {
        return [];
    }

}
