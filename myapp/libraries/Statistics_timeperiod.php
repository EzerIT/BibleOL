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
    const MAX_PERIOD = 26*7*24*3600;  // 26 weeks
    const DEFAULT_PERIOD = 7*24*3600;  // 7 days
    private $CI;

    private $period_start;
    private $period_end;
    
    public function __construct() {
        $this->CI =& get_instance();
    }

    public static function format_week(integer $weekno) {
        // $week is number of weeks since 1970-01-05
        $monday_offset = 4*24*3600;
        $seconds_per_week = 7*24*3600;

        $unixtime = $weekno * $seconds_per_week + $monday_offset;

        return date('W',$unixtime);
    }

    public static function format_day(integer $unixtime) {
        return date_create("@$unixtime")->format('d');
    }
        
    
    private function decode_start_date($date) {
        if (is_null($date))
            return ((int)(time() / (24*3600)) + 1) * 24*3600 /*Midnight tonight*/ - self::DEFAULT_PERIOD;

        // Set time to 00:00:00
        return date_create($date . ' 00:00:00',new DateTimeZone('UTC'))->getTimestamp();
    }

    private function decode_end_date($date) {
        if (is_null($date))
            return ((int)(time() / (24*3600)) + 1) * 24*3600;  // Midnight tonight
        
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
        $this->period_end = $this->decode_end_date($this->CI->input->get('end_date')) -1;  // -1 to turn exclusive time into inclusive

        // If period is longer than MAX_PERIOD, adjust the end date.
        $this->period_end = min($this->period_end, $this->period_start + self::MAX_PERIOD -1);
    }

    public function default_dates() {
        $this->period_end = ((int)(time() / (24*3600)) + 1) * 24*3600;  // Midnight tonight
        $this->period_start = $this->period_end - self::DEFAULT_PERIOD;
        --$this->period_end;  // Turn exclusive time into inclusive
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

    public function end_string() {
        return date_create("@$this->period_end")->format('Y-m-d');
    }

    public function start_string_minus_half() {
        return date_create('@' . ($this->period_start - 12*3600))->format('Y-m-d 12:00:00');
    }

    public function end_string_minus_half() {
        return date_create('@' . ($this->period_end - 12*3600))->format('Y-m-d 12:00:00');
    }

    public function start_week() {
        return $this->time_to_week($this->period_start);
    }

    public function end_week() {
        return $this->time_to_week($this->period_end);
    }
}