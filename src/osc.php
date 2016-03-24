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
class OpenSourceCredits{
	var $url_gh_api="https://api.github.com";

	var $cache_path='';
	var $cache_time=86400;	//one day

	var $api_loaded=array();

	var $tokens=array();
	
	// ------------------------------------------------------------------------------------
	// tokens = (array) repo_name => token
	// cache_path = path to the cache directory with a trailing slash
	function __construct($tokens=array(),$cache_path='',$cache_time=86400){
		$this->tokens=$tokens;
		$this->cache_path=$cache_path;		
		$this->cache_time=$cache_time;		
	}
	
	// ------------------------------------------------------------------------------------
	function GetCredits($base_repo_url='',$ext_repo_urls=array(),$with_contributors=1){
		if($base_repo_url and $this->SetRepoUrl($base_repo_url)){
			if($base_repo=$this->oapi->GetRepo()){
				$out['base']=$base_repo;
				if($with_contributors and $contributors=$this->oapi->GetContributors()){
					$out['base']['contributors'] =$contributors;
				}
			}
		}

		if(is_array($ext_repo_urls) and count($ext_repo_urls)){
			$i=0;
			foreach($ext_repo_urls as $repo_url){
				if($this->SetRepoUrl($repo_url)){
					if($repo=$this->oapi->GetExternalRepo()){
						$out['repos'][$i]=$repo;
						//$owners[$tmp['owner']]=$tmp['owner'];
						if($with_contributors and $contributors=$this->oapi->GetContributors()){
							$out['repos'][$i]['contributors'] =$contributors;
						}
						$i++;
					}
				}
			}
		}
		return $out;
	}


	// ------------------------------------------------------------------------------------
	function SetRepoUrl($url){
		if(preg_match('#^https://github\.com#',$url)){
			$this->SetApiRepoUrl('github',$url);
			return true;
		}
		/*
		elseif(preg_match('#THE URL OF ANOTHER repo web site#',$url)){
		}
		*/
		
		die("No valid API found for $url");
	}

	// ------------------------------------------------------------------------------------
	function SetApiRepoUrl($name,$url){
		if(!$this->api_loaded[$name]){
			require_once(dirname(__FILE__)."/osc_api_{$name}.php");
			$this->api_loaded[$name]=true;
		}
		$class="OscApi_$name";
		$this->oapi=new $class($this->tokens[$name]);
		$this->oapi->SetRepoUrl($url);
		$this->oapi->SetCache($this->cache_path, $this->cache_time);
	}

	
}
?>