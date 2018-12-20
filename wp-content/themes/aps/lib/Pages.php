<?php
class Pages{

	var $Next = "Next";
    var $Prev = "Previous";
    var $First = "";//Label First
    var $Last = "";//Label Last
    var $maxpage = 5;
    var $params = "";
	var $current = "<strong>%page%</strong>";
	var $ClassItem="num";
	var $NextClass="next";
	var $PrevClass="prev";
    var $prefixItem = "<li>";
    var $suffixItem = "</li>";
	var $SeparatorLast = "<span>...</span>";

	function multipages($totalRecord, $perpage, $curpage, $mpurl)
	{
		$page = $this->maxpage;
        $prefixItem = $this->prefixItem;
        $suffixItem = $this->suffixItem;
		$multipage = "";        
		$realpages = 1;
		$from =0;
		$to= 1;
		$num = $totalRecord;
		if($num > $perpage) {
			$offset = 2;
			$realpages = @ceil($num / $perpage); //+ (($num % $perpage > 0) ? 1 : 0);
			$pages = $realpages;//maxpage<realpages ? maxpage : realpages;            
			if($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if($to - $from < $page) {
						$to = $page;
					}
				} else if($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}

			$multipage = ($this->First ?  $prefixItem . "<a ".$this->replace_page($this->params, 1). " target=\"_self\" href=\"" . $this->replace_page($mpurl, 1) . "\" rel='1' class=\"first\">" . $this->First . "</a>" . $suffixItem : "") .
				( $prefixItem . "<a " . $this->replace_page($this->params, $curpage - 1) . " target=\"_self\"  href=\"" . $this->replace_page($mpurl, $curpage - 1) . "\" rel='".($curpage - 1)."' class=\"".$this->PrevClass."\">" . $this->Prev . "</a>" . $suffixItem);
				
			$multipage .= ($curpage - $offset > 1 && $pages > $page && $pages > $this->maxpage ? $prefixItem . '<a class="'.$this->ClassItem.'" '.$this->replace_page($this->params, 1).' href="'.$this->replace_page($mpurl, 1).'" rel="1" >1</a>' . $suffixItem .$this->SeparatorLast: '');
		   for($i = $from; $i <= $to; $i++){ 
				$multipage .= ($i == $curpage) ? $this->replace_page($this->current, $i)."" : $prefixItem . "<a class=\"".$this->ClassItem."\" " . $this->replace_page($this->params, $i) . " target=\"_self\" href=\"" . $this->replace_page($mpurl, $i) . "\" rel='".$i."' >" . $i . "</a>" . $suffixItem;
			}
			
			//$multipage .= ($curpage < $pages && $pages > $this->maxpage ? $this->SeparatorLast.'<a class="'.$this->ClassItem.'"  '.$this->replace_page($this->params, $pages).' href="'.$this->replace_page($mpurl, $pages).'" target="_self">'.$realpages.'</a>' : '');
										
			$multipage .= ( $prefixItem . "<a " . $this->replace_page($this->params, $curpage + 1) . " target=\"_self\" href=\"" . $this->replace_page($mpurl, $curpage + 1) . "\" rel='".($curpage + 1)."' class=\"".$this->NextClass."\">" . $this->Next . "</a>" . $suffixItem) .
				($this->Last ? $prefixItem . "<a " . $this->replace_page($this->params, $pages) . " target=\"_self\" href=\"" . $this->replace_page($mpurl, $pages) . "\" rel='".$pages."' class=\"last\">" . $this->Last . "</a>" .$suffixItem : "");
		}
		$this->maxpage = $realpages;
		return $multipage;
	}
	function replace_page($url,$page)
    {
        return str_replace("%page%",$page,$url);
    }	
}
?>