<?php

class DateZoneConverter
{
    public $version;
    public $date;
    const  time_zone_Berlin_version = '1.0.17+60';

    public function __construct(string $version, string $date)
    {
        $this->date = $date;
        $this->version = $version;
    }

    private function get_comparable(string $version)
    {
        $sub = explode('+', trim($version));
        if (count($sub) > 2) {
            throw new Exception('False Version');
        } elseif (count($sub) == 2) {
            $sub = $sub[1];
        } else {
            $sub = 0;
        }
        $main = explode('.', trim($version));
        if ($sub ) {
            $main[count($main) - 1] = substr(end($main), 0, strpos(end($main), '+'));
        }
        return [$main, [$sub]];
    }

    private function get_time_zone()
    {
        $berlin_zone = $this->get_comparable(self::time_zone_Berlin_version);
        $coming_zone = $this->get_comparable($this->version);
        for ($t = 0; $t < count($berlin_zone); $t++) {
            for ($i = 0; $i < count($berlin_zone[$t]); $i++) {
                if (!is_numeric(trim($coming_zone[$t][$i])) or !is_numeric(trim($berlin_zone[$t][$i]))) {
                    throw new Exception('False Version');
                } else {
                    if (intval($coming_zone[$t][$i]) > intval($berlin_zone[$t][$i])) {
                        return "UTC";
                    } elseif (intval($coming_zone[$t][$i]) < intval($berlin_zone[$t][$i])) {
                        return "Europe/Berlin";
                    }
                }
            }
        }
        return "Europe/Berlin";
    }

    public function get_formatted_date(){
        $datetime = new DateTime($this->date);
        $timezone = new DateTimeZone($this->get_time_zone());
        $datetime->setTimezone($timezone);
        return $datetime->format('Y-m-d H:i:s');
    }


}