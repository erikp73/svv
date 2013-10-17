<?php

class dl_uploaded_net extends Download {

	public function PreLeech($url){ 
		if(stristr($url, "/f/")) {
			$data = $this->lib->curl($url, "", "");
			$data = $this->lib->cut_str($data, '<table id="fileList">', "</table>");
			$FID = explode('<h2><a href="file', $data);
			$maxfile = count($FID);
			for ($i = 1; $i < $maxfile; $i++) {
				preg_match('%\/(.+)\/from\/(.*)%U', $FID[$i], $code);
				$list = "<a href=http://uploaded.net/file/{$code[1]}>http://uploaded.net/file/{$code[1]}/</a><br/>";
				echo $list;
			}
			exit;
		}
	}  
  
	public function CheckAcc($cookie){
		$data = $this->lib->curl("http://uploaded.net/", $cookie, "");
		if(stristr($data, '<a href="register"><em>Premium</em></a>')) return array(true, $this->lib->cut_str($this->lib->cut_str($data, "Duration:</td>","/th>"), "<th>","<")."<br/> Traffic Avaiable: ". $this->lib->cut_str($data, '<th class="aT"><em class="cB">', '</em></th>'));
		elseif(stristr($data, '<li><a href="logout">Logout</a></li>')) return array(false, "accfree");
		else return array(false, "accinvalid");
	}
         
	public function Login($user, $pass){
		$data = $this->lib->curl("http://uploaded.net/io/login", "", "id={$user}&pw={$pass}");
		$cookie = $this->lib->GetCookies($data);
		return $cookie;
	}
         
	public function Leech($url) {
		$url = $this->getredirect($url);
		if (stristr($url,'uploaded.net/404')) $this->error("dead", true, false, 2);
		$data = $this->lib->curl($url, $this->lib->cookie, ""); 
		if (stristr($data,">Extend traffic<")) $this->error("LimitAcc");
		elseif (stristr($data,"Hybrid-Traffic is completely exhausted")) $this->error("LimitAcc");
		elseif (stristr($data,"Our service is currently unavailable in your country")) $this->error("blockCountry", true, false);
		elseif (stristr($data,"You used too many different IPs")) $this->error("blockAcc", true, false);
		elseif (stristr($data,"Download Blocked (ip)")) $this->error("blockIP", true, false);
		elseif(!$this->isredirect($data)) {
			if (preg_match('/action="(https?:\/\/.+)" style/i', $data, $link))	return trim($link[1]);
		}
		else  return trim($this->redirect);
		return false;
	}

}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Uploaded Download Plugin 
* Downloader Class By [FZ]
* Fixed By djkristoph
* Fixed download link By giaythuytinh176 [5.8.2013]
*/
?>