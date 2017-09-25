<?php


function valid_date_check($date) {
    try {
        new DateTime($date,new DateTimeZone('UTC'));
        return true;
    }
    catch (Exception $e) {
        return false;
    }
}


class Statistics_timeperiod {
    // Uses UNIX time (seconds since 1970-01-01T00:00:00Z) for most time values
    // Uses weeks since (1970-01-05T00:00:00Z) for week numbers (1970-01-05 was a Monday).
    
    const MAX_PERIOD = 26*7*24*3600;  // 26 weeks
    const DEFAULT_PERIOD = 7*24*3600;  // 7 days
    const WEEK_EPOCH_OFFSET = 4*24*3600; // Seconds from 1970-01-01 to 1970-01-05
    const SECS_PER_WEEK = 7*24*3600; // Seconds per week
    const SECS_PER_DAY = 24*3600; // Seconds per day
    
    private $CI;

    private $period_start; // UNIX time. Includes start second
    private $period_end;   // UNIX time. Does not include end second
    
    public function __construct() {
        $this->CI =& get_instance();
    }

//    public static function format_week(integer $weekno) {
//        return date('W', self::week_to_unix($weekno));
//    }

    public static function format_week($unixtime) {
        return date_create("@$unixtime")->format('W');
    }

    public static function format_date(integer $unixtime) {
        return date_create("@$unixtime")->format('Y-m-d');
    }

    public static function format_day(integer $unixtime) {
        return date_create("@$unixtime")->format('d');
    }

    public static function last_midnight(integer $unixtime) {
        return (int)($unixtime / self::SECS_PER_DAY) * self::SECS_PER_DAY;
    }

    public static function next_midnight(integer $unixtime) {
        return self::last_midnight($unixtime + self::SECS_PER_DAY);
    }

    
    public static function round_to_noon(integer $unixtime) {
        return self::last_midnight($unixtime) + self::SECS_PER_DAY/2;
    }

    // Returns number of weeks since the week epoch
    public static function floor_to_week(integer $unixtime) {
        return (int)(($unixtime-self::WEEK_EPOCH_OFFSET) / self::SECS_PER_WEEK);
    }

    public static function week_to_unix(integer $weekno) {
        return $weekno*self::SECS_PER_WEEK + self::WEEK_EPOCH_OFFSET;
    }

    public static function last_monday(integer $unixtime) {
        return self::week_to_unix(self::floor_to_week($unixtime));
    }

    public static function next_monday(integer $unixtime) {
        return self::last_monday($unixtime + self::SECS_PER_WEEK);
    }

    
    private function decode_start_date($date) {
        if (is_null($date))
            return self::next_midnight(time()) - self::DEFAULT_PERIOD;

        // Set time to 00:00:00
        return date_create($date . ' 00:00:00',new DateTimeZone('UTC'))->getTimestamp();
    }

    private function decode_end_date($date) {
        if (is_null($date))
            return self::next_midnight(time());
        
        // Set time to 23:59:59 and add one second
        return date_create($date . ' 23:59:59',new DateTimeZone('UTC'))->getTimestamp() + 1;
    }

    // Returns number of weeks since Monday 1970-01-05
    public function time_to_week(integer $time) {
        // UNIX time starts on a Thursday. So move epoch to Monday 1970-01-05
        $monday_offset = 4*24*3600;
        $seconds_per_week = 7*24*3600;
        return (int)floor(($time-$monday_offset) / $seconds_per_week);
    }

//    private function timestamp_to_date(integer $d) {
//        return date_create("@$d")->format('Y-m-d');
//    }


    public function set_validation_rules() {
        $this->CI->form_validation->set_rules('start_date', 'Start date', 'trim|valid_date_check');
        $this->CI->form_validation->set_rules('end_date', 'End date', 'trim|valid_date_check');
    }

    public function ok_dates() {
        $this->period_start = $this->decode_start_date($this->CI->input->get('start_date'));
        $this->period_end = $this->decode_end_date($this->CI->input->get('end_date'));

        // If period is longer than MAX_PERIOD, adjust the end date.
        $this->period_end = min($this->period_end, $this->period_start + self::MAX_PERIOD);
    }

    public function default_dates() {
        $this->period_end = self::last_midnight(time()) + self::SECS_PER_DAY;
        $this->period_start = $this->period_end - self::DEFAULT_PERIOD;
    }

    public function start_timestamp() {
        return $this->period_start;
    }

    public function end_timestamp() {
        return $this->period_end;
    }

    public function start_string() {
        return date_create("@$this->period_start")->format('Y-m-d');
    }

    // Returns an inclusive date
    public function end_string() {
        return date_create('@' . ($this->period_end-1))->format('Y-m-d');
    }

    public function start_string_minus_half() {
        return date_create('@' . ($this->period_start - 12*3600))->format('Y-m-d 12:00:00');
    }

    public function end_string_minus_half() {
        return date_create('@' . ($this->period_end - 12*3600))->format('Y-m-d 12:00:00');
    }

    public function start_week() {
        return self::floor_to_week($this->period_start);
    }

    public function end_week() {
        return self::floor_to_week($this->period_end);
    }
}