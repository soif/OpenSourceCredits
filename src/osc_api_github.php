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

require_once(dirname(__FILE__)).'/osc_api.php';

class OscApi_github extends OscApi{
	var $url_api="https://api.github.com";

	var $repo;
	var $owner;
	var $token_param	="access_token";
	var $token_needed	=true;

	// ---------------------------------------------------
	function SetRepoUrl($url){
		$this->owner='';
		$this->repo='';
		$this->_SetRepoFromGitUrl($url);		
	}
	// ---------------------------------------------------
	function GetRepo($parent=''){
		if($arr=$this->FetchUrlJson("/repos/{$this->owner}/{$this->repo}")){
			if($parent){
				return $this->FormatRepo($arr[$parent]);
			}
			else{
				return $this->FormatRepo($arr);
			}
		};
	}
	// ---------------------------------------------------
	function GetExternalRepo($parent='source'){
		return $this->GetRepo($parent);
	}

	
	// ---------------------------------------------------
	function GetContributors($parent=''){
		if($arr=$this->FetchUrlJson("/repos/{$this->owner}/{$this->repo}/contributors")){
			foreach($arr as $contrib){
				$contrib=$this->FormatContributor($contrib);
				$out[$contrib['id']]=$contrib;
			}
			return $out;
		};
	}


	// ---------------------------------------------------
	function FormatRepo($in){
		$out['id']				=$in['id'];
		$out['name']			=$in['name'];
		$out['url_icon']		=$in['owner']['avatar_url'];
		$out['url_repo']		=$in['html_url'];
		$out['url']				=$in['homepage'];
		$out['description']		=$in['description'];
		$out['language']		=$in['language'];
		$out['date_created']	=strtotime($in['created_at']);
		$out['date_updated']	=strtotime($in['updated_at']);
		//owner
		$out['owner']			=$this->FormatContributor($in['owner']);
		
		return parent::FormatRepo($out);
	}

	// ---------------------------------------------------
	function FormatContributor($in){
		$out['id']				=$in['id'];
		$out['name']			=$in['login'];
		$out['url_icon']		=$in['avatar_url'];
		$out['url_repo']		=$in['html_url'];
		$out['url']				='';
		$out['commits']			=$in['contributions'];
		return parent::FormatContributor($out);
	}


	// ---------------------------------------------------
	function _SetRepoFromGitUrl($url){
		$url=str_replace('https://github.com/','',$url);		
		if(!$url){return;}
		list($owner,$repo)=explode('/',$url);
		$repo=preg_replace('#\.git$#','',$repo);
		if(!$owner or !$repo){return;}
		$this->repo	=$repo;
		$this->owner=$owner;
		return true;
	}



}
?>