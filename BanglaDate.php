<?php

class BanglaDate
{
	//valiable contains english month id to bengali month mapping
	// e.g: 4 (April) => বৈশাখ, 12(December) => পৌষ, 1(January) => মাঘ, etc
	private $bangla_month = array("4"=>"বৈশাখ", "5"=>"জ্যৈষ্ঠ", "6"=>"আষাঢ়", "7"=>"শ্রাবণ", "8"=>"ভাদ্র", "9"=>"আশ্বিন", "10"=>"কার্তিক", "11"=>"অগ্রাহায়ণ", "12"=>"পৌষ", "1"=>"মাঘ", "2"=>"ফাল্গুন", "3"=>"চৈত্র");

	// array contains starting date of bangla month (1st day of bangla month) in english calender month
	// "1"=>14, in January (for index 1), 1st date of corresponding bangla month is January, 14
	// "6"=>15, in June (for index 6), 1st date of corresponding bangla month is June, 15
	// "12"=>16, in December (for index 12), 1st date of corresponding bangla month is December, 16
	private $des_arr = array("1"=>15, "2"=>14, "3"=>15, "4"=>14, "5"=>15, "6"=>15, "7"=>16, "8"=>16, "9"=>16, "10"=>17, "11"=>16, "12"=>16);

	// array contains ending date of bangla month (last day of bangla month) in english calendar month
	// "1"=>30, in January (for index 1), corresponding bangla month's (মাঘ) length is 30 days
	// "3"=>30, in March (for index 3), corresponding bangla month's (চৈত্র) length is 30 days
	// "6"=>31, in June (for index 6), corresponding bangla month's (আষাঢ়) length is 31 days
	// "12"=>30, in December (for index 12), corresponding bangla month's (পৌষ) length is 30 days
	//**** "2"=>29, in February (for index 2), corresponding bangla month's (ফাল্গুন) lenth is 29 days, however if leap year then month length would be 30 days 
	private $mon_end = array("1"=>30, "2"=>29, "3"=>30, "4"=>31, "5"=>31, "6"=>31, "7"=>31, "8"=>31, "9"=>31, "10"=>30, "11"=>30, "12"=>30);

	//Array variable contains bangla digit
	private $bd = array('০','১','২','৩','৪','৫','৬','৭','৮','৯');
	//Array variable contains english to bangla day name
	private $ban_day_name = array("Sat"=>"শনি","Sun"=>"রবি","Mon"=>"সোম","Tue"=>"মঙ্গল","Wed"=>"বুধ","Thu"=>"বৃহস্পতি","Fri"=>"শুক্র");
	private $const1 = "বার";
	
	//Will be used for containing English day and month for display
	private $eng_day_id = "";
	private $eng_day_name = "";
	
	//Will be used for containing Bangla day, month & year for display
	private $ban_day = "";
	private $ban_mon = "";
	private $ban_yr = "";
	
	//Will be used for containing English day, month & year for display
	private $day = "";
	private $mon = "";
	private $yr = "";
	
	//Will be used for lear year indicator if 0 then NOT leap year, if 1 then leap year
	private $is_lp = 0;
	//Default timezone is Asia/Dhaka
	private $set_time_zone = +6;
	
	function __construct()
	{		
		//Get current time from Server
		$now = time();
				
		//Fetch current timezone from from the Server Time
		$tmp_hr = date("O", $now);
				
		//Fetch current hour difference from GMT, for eg: +02, +12, -07, -11 etc (lenght 3)
		$add_hr = substr($tmp_hr,0,3);
		
		//subtruct the server hour from local timezone hour to get the exact local time. but of course conver into sec (multiply by 3600 to both)
		// for e.g: Local time zone is +06 (for Dhaka, Bangladesh) and web server is running at New York (GTM -04)
		// so the hour difference is 10 hour from New York to Dhaka, if some what to get the bangla date at 9:00 AM Dhaka Time but
		// Server reply New York time which is -10 from Dhaka time, so the time would be 11PM previout date, As date will be changed then user will not get actual bangla date
		//converted into second
		$t_hr = $this->set_time_zone*3600 - $add_hr*3600; 
		
		$this->day = date("j", $now + $t_hr);
		$this->mon = date("n", $now + $t_hr);
		$this->yr = date("Y", $now + $t_hr);
		$this->eng_day_id = date("D", $now + $t_hr);
		$this->eng_day_name = date("l", $now + $t_hr);
		$this->CheckLeapYear();
	}
	function SetDate($dt)
	{
		$this->day = date("j", strtotime($dt));
		$this->mon = date("n", strtotime($dt));
		$this->yr = date("Y", strtotime($dt));
		$this->eng_day_id = date("D", strtotime($dt));
		$this->eng_day_name = date("l", strtotime($dt));
		$this->CheckLeapYear();
	}
	function SetTimeZone($set_time_zone = +6)
	{
		$this->set_time_zone = $set_time_zone;	
	}
	function CheckLeapYear()
	{
		$this->is_lp = 0;
		if($this->yr%4==0 && ($this->yr%100!=0 || $this->yr%400==0))
			$this->is_lp = 1;
	}
	function CalculateYear()
	{
		// if month is before April (January to March) then difference between english & bengali month is 594 years for current english year
		if($this->mon<4)
			$this->ban_yr = $this->yr - 594;
		// if month is after April (May to December) then difference between english & bengali month is 593 years for current english year
		else if ($this->mon>4)
			$this->ban_yr = $this->yr - 593;
		else //if month is April then need to check whether date is before 14 or, on or above 14
		{
			// if date is below 14 then the difference is 594 years
			if($this->des_arr[$this->mon] > $this->day)
				$this->ban_yr = $this->yr - 594;
			else // if date is on or above 14 then difference is 593 years
				$this->ban_yr = $this->yr - 593;
		}
	}
	function CalculateMonthAndDay()
	{
		if($this->des_arr[$this->mon] < $this->day )
		{
			$this->ban_mon = 	$this->bangla_month[$this->mon];
			$this->ban_day = 	$this->day - $this->des_arr[$this->mon] + 1; //Add 1 Day Exclusively
		}
		else
		{
			$tmp_mon = $this->mon-1;
			if($this->mon==1)
				$tmp_mon = 12;

			$this->ban_mon = 	$this->bangla_month[$tmp_mon];
			$this->ban_day = 	$this->mon_end[$tmp_mon] - ($this->des_arr[$this->mon] - $this->day) + 1; //Add 1 Day Exclusively
			
			//In case of "2"=>"ফাল্গুন" check the leap year; if yes then add addition day
			if($this->is_lp==1 && $this->mon==3)
				$this->ban_day = $this->ban_day + 1;
		}
		//Padding with 0 for bangla day in case of single digit
		$this->ban_day = str_pad($this->ban_day,2,"0",STR_PAD_LEFT);
	}
	function PrintDate($incPrefix = "আজঃ ")
	{
		echo $incPrefix.$this->bd[substr($this->ban_day,0,1)].$this->bd[substr($this->ban_day,1,1)]."-".$this->ban_mon."-".$this->bd[substr($this->ban_yr,0,1)].$this->bd[substr($this->ban_yr,1,1)].$this->bd[substr($this->ban_yr,2,1)].$this->bd[substr($this->ban_yr,3,1)]." (".$this->ban_day_name[$this->eng_day_id].$this->const1.")";
	}
	function PrintEngDate($incPrefix = "Today: ")
	{
		echo $incPrefix.date("d-M-Y", strtotime($this->yr."-".$this->mon."-".$this->day))." (".$this->eng_day_name.")";
	}
	function PrintDayInEnglish()
	{
		echo $this->eng_day_name;	
	}
	function PrintDayInBangla($withTail = 1)
	{
		echo $this->ban_day_name[$this->eng_day_id];	
		if($withTail==1) echo $this->const1;
	}
	function PrintBanglaDate($incPrefix = "আজঃ ")
	{
		//Calculate the bangla year
		$this->CalculateYear();
		//Calculate the bangla month & day
		$this->CalculateMonthAndDay();
		//Print bangla date
		$this->PrintDate($incPrefix);
	}
}
?>