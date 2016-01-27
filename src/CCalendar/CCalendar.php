<?php

class CCalendar {
	 
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->naviHref = htmlentities($_SERVER['PHP_SELF']);
		
	}
	 
	//Variabler
	private $dayLabels = array("Mån","Tis","Ons","Tor","Fre","Lör","Sön");
	private $currentYear=0;
	private $currentMonth=0;
	private $currentDay=0;
	private $currentDate=null;
	private $daysInMonth=0;
	private $naviHref= null;
	private $pictureofthemonth=null;
	
	/**
    * print out the calendar
    */
    public function show() {
    	$year  = null;
         
        $month = null;
         
        if(null == $year && isset($_GET['year'])){
 
            $year = $_GET['year'];
         
        }else if(null==$year){
 
            $year = date("Y",time());  
         
        }          
         
        if(null==$month&&isset($_GET['month'])){
 
            $month = $_GET['month'];
         
        }else if(null==$month){
            
            $month = date("M",time());
         
        }                  
		
		$this->currentYear=$year;
		 
		$this->currentMonth=$month;
		
		$picture = null;
		$movieTitle = null;
		switch ($this->currentMonth) {
			case "01":
				$picture = "American_pie_film_series_cast";
				$movieTitle = "American Pie";
				break;
			case "02":
				$picture = "Azumi-in-Battle";
				$movieTitle = "Azumi";
				break;
			case "03":
				$picture = "the-simpsons";
				$movieTitle = "Simpsons - The Movie";
				break;
			case "04":
				$picture = "American_pie_film_series_cast";
				$movieTitle = "American Pie";
				break;
			case "05":
				$picture = "Azumi-in-Battle";
				$movieTitle = "Azumi";
				break;
			case "06":
				$picture = "the-simpsons";
				$movieTitle = "Simpsons - The Movie";
				break;	
			case "07":
				$picture = "American_pie_film_series_cast";
				$movieTitle = "American Pie";
				break;
			case "08":
				$picture = "Azumi-in-Battle";
				$movieTitle = "Azumi";
				break;
			case "09":
				$picture = "the-simpsons";
				$movieTitle = "Simpsons - The Movie";
			case "11":
				break;
			    $picture = "American_pie_film_series_cast";
				$movieTitle = "American Pie";
				break;
			case "10":
				$picture = "Azumi-in-Battle";
				$movieTitle = "Azumi";
				break;
			case "12":
				$picture = "the-simpsons";
				$movieTitle = "Simpsons - The Movie";
				break;	
			default:
				$picture = "American_pie_film_series_cast";
				$movieTitle = "American Pie";
				break;
		}
		
		$this->daysInMonth=$this->_daysInMonth($month,$year);
		
		$content = null;		
		$content .='<div class="calendar"><div><img src="img.php?src=calendar/'. $picture .'.jpg&width=602&height=333&crop-to-fit"><p class="movietitle">' . $movieTitle . '</p></div>'.
				'<div class="box">'.
				$this->_createNavi().
				'</div>'.
				'<div class="box-content">'.
				'<ul class="label">'.$this->_createLabels().'</ul>';
		$content.='<div class="clear"></div>';
		$content.='<ul class="dates">';
		 
		$weeksInMonth = $this->_weeksInMonth($month,$year);
		// Create weeks in a month
		for( $i=0; $i<$weeksInMonth; $i++ ){
			 
			//Create days in a week
			for($j=1;$j<=7;$j++){
				$content.=$this->_showDay($i*7+$j);
			}
		}
		 
		$content.='</ul>';
		 
		$content.='<div class="clear"></div>';
		 
		$content.='</div>';
		 
		$content.='</div>';
		return $content;
	}
		
	/**
	 * create the li element for ul
	 * This function will determine what value to put to the created cell. It can be empty or numbers.
	 */
	private function _showDay($cellNumber){
		setlocale(LC_TIME, 'sv_SE.UTF-8');
		if($this->currentDay==0){
			 
			$firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
			 
			if(intval($cellNumber) == intval($firstDayOfTheWeek)){
				 
				$this->currentDay=1;
				 
			}
		}
		
		if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
			 
			$this->currentDate = date('Y-M-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
			 
			$cellContent = $this->currentDay;
			 
			$this->currentDay++;
			 
		}else{
			 
			$this->currentDate =null;

			$cellContent=null;
		}
		 
		return '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
		($cellContent==null?'mask':'').'">'.$cellContent.'</li>';
	}
	 
	/**
	 * This function will create the "Prev" && "Next" navigation buttons on the top of the calendar
	 *
	 */
	private function _createNavi(){
		 
		$nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
		 
		$nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
		 
		$preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
		 
		$preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
		
		return
		'<div class="header">'.
		'<a class="prev" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'">Föregående</a>'.
		'<span class="title">'.date('Y M',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>'.
		'<a class="next" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'">Nästa</a>'.
		'</div>';
	}
	
	/**
	 * create calendar week labels
	 */
	private function _createLabels(){
		 
		$content='';
		 
		foreach($this->dayLabels as $index=>$label){
			 
			$content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';

		}
		 
		return $content;
	}
	 
	/**
	 * calculate number of weeks in a particular month
	 */
	private function _weeksInMonth($month=null,$year=null){
		//setlocale(LC_TIME, 'sv_SE.UTF-8');
		if( null==($year) ) {
			$year =  date("Y",time());
		}
		 
		if(null==($month)) {
			$month = date("M",time());
		}
		 
		// find number of days in this month
		$daysInMonths = $this->_daysInMonth($month,$year);
		 
		$numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
		 
		$monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
		 
		$monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
		 
		if($monthEndingDay<$monthStartDay){
			 
			$numOfweeks++;
			 
		}
		 
		return $numOfweeks;
	}

	/**
	 * calculate number of days in a particular month
	 */
	private function _daysInMonth($month=null,$year=null){
		setlocale(LC_TIME, 'sv_SE.UTF-8');
		if(null==($year))
			$year =  date("Y",time());

		if(null==($month))
			$month = date("M",time());
		 
		return date('t',strtotime($year.'-'.$month.'-01'));
	}
	 
}
