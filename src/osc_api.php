<?php
/*
	OpenSourceCredits
	https://github.com/soif/OpenSourceCredits
    ----------------------------------------------
	Copyright (C) 2015  Francois Dechery

	LICENCE: ###########################################################
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
	#####################################################################
*/
class OscApi{
	var $cache_path;
	var $cache_time=0;

	var $repo;
	var $owner;

	var $url_api; 		//api url prefix
	var $token_needed	=false;
	var $token_param;
	var $token;

	var $last_url_api;
	
	// ---------------------------------------------------
	function __construct($token){
		$this->token=$token;
	}

	// ---------------------------------------------------
	function SetRepoUrl($url){
		//should parse url	and assign repo and owner, for further Get calls
	}
	
	// ---------------------------------------------------
	function GetContributors(){
		//should return contributors array
	}

	// ---------------------------------------------------
	function GetRepo(){
		//should return repo array
	}

	// ---------------------------------------------------
	function GetExternalRepo(){
		//should return repo array
	}

	// ---------------------------------------------------
	function FetchUrlJson($url){
		if(!$url){return;}
		if(!preg_match('#^http(s)?://#',$url) and $this->url_api){
			$url=$this->url_api.$url;
			if($this->token_needed){
				if($this->token_param and $this->token){
					$url .="?{$this->token_param}={$this->token}";
				}
				else{
					die("A token is needed to access {$this->url_api}");
				}
			
			}
		}
		$this->last_url_api=$url;
		$opts = array(
			'http'=> array(
				'method'=>   "GET",
				'user_agent'=> "OpenSourceCredit"
			)
		);
		if(!$arr=$this->_cacheLoadArray($url)){
			$context =stream_context_create($opts);
			if($content=file_get_contents($url,false,$context)){
				$arr = json_decode($content,true);
				$this->_cacheWriteArray($url,$arr);
			}
		}
		return $arr;
	}

	// ---------------------------------------------------
	function FormatRepo($in){
		$in['url_api']=$this->last_url_api;
		return $in;
	}

	// ---------------------------------------------------
	function FormatContributor($in){
		return $in;
	}

	// ------------------------------------------------------------------------------------
	function SetCache($path,$time){
		$this->cache_path=$path;
		$this->cache_time=$time;
	}
	// ------------------------------------------------------------------------------------
	function _cacheLoadArray($name){
		if(!$this->cache_path){
			return;
		}
		if($time=$this->cache_time){			
			$file=$this->cache_path.$this->_getCacheName($name);						
			if(file_exists($file) and filemtime($file) + $time > time() ){
				$tmp=file_get_contents($file);
				$arr=json_decode($tmp,true);
				if(is_array($arr)){
					return $arr;
				}
			}
		}
	}

	// ------------------------------------------------------------------------------------
	function _cacheWriteArray($name,$data){
		$file=$this->cache_path.$this->_getCacheName($name);		
		if($this->cache_time){
			if(!file_exists($file)){
				@touch($file);
			}
			if(is_writable($file)){
				if(is_array($data)){
					$fp = fopen($file, 'a');
					if (flock($fp, LOCK_EX)) {
						ftruncate($fp, 0) ;
						fwrite($fp, json_encode($data)) ;
						flock($fp, LOCK_UN) ;
					}
					fclose($fp) ; 
				}
			}
		}
	}

	// ------------------------------------------------------------------------------------
	function _getCacheName($url){
		$p		=parse_url($url);
		$name	="OpenSourceCredits_";
		$p['host'] and $name .=$p['host']."_";
		$p['path'] and $name .=str_replace('/', '_', $p['path']) ."_";
		$name 	.=md5($url);
		return $name;
	}


	
}
?>