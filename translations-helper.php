define('TRANS','trans.');
define('PAGINATION',10);
define('NUMBER_FORMATTER',false);
define('DATE_FORMATTER',false);


if (!function_exists('numberFormatter')) {
    function numberFormatter($date,$locale)
    {
        if (!DATE_FORMATTER){ return  $date;}
        $formatter = \datefmt_create($locale,\IntlDateFormatter::FULL,\IntlDateFormatter::FULL,config('app.timezone'));
        return \datefmt_format($formatter, $date);
    }
}

if (!function_exists('numberFormatter')) {
    function numberFormatter($number,$locale)
    {
        if (!NUMBER_FORMATTER){ return  $number;}

        $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        return $formatter->format($number);
    }
}

if (!function_exists('position')) {
    function position()
    {
       if (\LaravelLocalization::getCurrentLocaleDirection() === 'rtl'){
            return 'top-start';
       }
       return  'top-end';
    }
}
