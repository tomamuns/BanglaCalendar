# BanglaDate
This simple php script will help to display bangla date in following format. 
e.g. আজঃ ১৭-পৌষ-১৪২৬ (বুধবার)

#Usages
include_once("./BanglaDate.php");
$bdate = new BanglaDate();

echo "".$bdate->PrintEngDate()."<br />";
echo "".$bdate->PrintBanglaDate()."<br />";
