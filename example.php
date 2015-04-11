<?php


// Config ##################################################################################################
// the main project url -----------------------------
$project_url="https://github.com/phpMyDomo/phpMyDomo";

// External projects URLs ------------------------------
// due to the way Github API Authentication works, you have to :
// - first fork each project (you want to credit) in your own account (else the API wont let you access others projects)
// - use the forked URLs here:
$externals[]="https://github.com/phpMyDomo/smarty.git";
$externals[]="https://github.com/phpMyDomo/icalparser.git";
$externals[]="https://github.com/phpMyDomo/thooClock.git";
$externals[]="https://github.com/phpMyDomo/countdown.git";
// It would be simplier whenever Github decide that you can access public datas, without forking it first.... ;-)

// Start #####################################################################################################
//go to your https://github.com/settings/applications , and "generate a Token" (with no roles checked), then paste it here :
$tokens = array('github'=>"b33cfb6e9167873038594dd52dd39f770b3db553");

require('src/osc.php');
$osc=new OpenSourceCredits($tokens);
//$osc=new OpenSourceCredits($tokens,'/tmp/'); // you might want to cache the results, by adding a cache directory path

$c=$osc->GetCredits($project_url,$externals);

// ###########################################################################################################
$html_debug=print_r($c,true);

foreach($c['base']['contributors'] as $contrib){
	$html_contrib.=<<<EOF
		<li><img src="{$contrib['url_icon']}" height=16> <a href="{$contrib['url_repo']}">{$contrib['name']}</a> <small>({$contrib['commits']} commits)</small></li>
EOF;
}

foreach($c['repos'] as $r){
	$html_repos.=<<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="pull-right">by <img src="{$r['owner']['url_icon']}" height=16> <a href="{$r['owner']['url_repo']}">{$r['owner']['name']}</a></div>
				<h3 class="panel-title"><b><a href="{$r['url_repo']}">{$r['name']}</a></b> <small>({$r['language']})</small></h3>
			</div>
			<div class="panel-body">
				{$r['description']}
			</div>
		</div>
EOF;
}

echo <<<EOF
<html>
	<head>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
	<div class="container">
		<h1>{$c['base']['name']} <small><a href="{$c['base']['url_repo']}"></a></small></h1>
		<p>{$c['base']['description']}</p>

		<hr>

		<p class="lead"><i>Thank You to all the following Contributors and OpenSource Projects (included in {$c['base']['name']})</i></p>
	
		<h2>Contributors</h2>
		<ul>
			$html_contrib
		</ul>
	
		<h2>External Projects</h2>
	$html_repos

		<h2>Debug</h2>
		<pre>$html_debug</pre>
	<div>
	</body>
</html>
EOF;

?>