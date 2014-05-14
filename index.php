<?php

define('BASEPATH', dirname(__FILE__) .'/');
include_once(BASEPATH .'vendor/autoload.php');

// Load the config
global $config;
if(file_exists(BASEPATH .'config.php')){
	include_once(BASEPATH .'config.php');
} else {
	die('<p class="error">Error: Missing config.php file.</p>');
}

// Get our page (for pagination)
$page = isset($_GET['page']) && $_GET['page'] ? filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT) : 1;

// Setup our file cache
if(file_exists(BASEPATH .'cache/')){
	$cache = new SimpleCache();
	$cache->cache_path = BASEPATH .'cache/';
	$cache->cache_time = 60*60*6;
} else {
	die('<p class="error">Error: Missing cache directory</p>');
}

// Get data from GitHub API
$github_url = 'https://api.github.com/repos/'. $config['github_repo'] .'/commits?per_page=100&page='. $page;
if($data = $cache->get_cache($config['github_repo'].$page)){
    $data = json_decode($data);
} else {
    $data = do_curl($github_url, $config['github_user'], $config['github_pass']);
    $cache->set_cache($config['github_repo'].$page, $data);
    $data = json_decode($data);
}

// Group commits by date
$commits = $data;
$grouped_commits = array();
if(!empty($commits)){
	foreach($commits as $commit){
		$day = date('Y-m-d', strtotime($commit->commit->author->date));
		if(!isset($grouped_commits[$day])) $grouped_commits[$day] = array();
		array_push($grouped_commits[$day], $commit);
	}
}

function do_curl($url, $username, $password)
{
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, $username);
		curl_setopt($ch, CURLOPT_USERPWD, $username .':'. $password);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	} else {
		return file_get_contents($url);
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Changelog for github.com/<?php echo $config['github_repo']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="screen" />
</head>
<body>

    <div class="wrapper">

		<header class="header">
			<h1 class="title">Changelog for <a href="http://github.com/<?php echo $config['github_repo']; ?>" target="_blank" class="github">github.com/<span class="repo"><?php echo $config['github_repo']; ?></span></a></h1>
		</header>

		<section class="content">
			<?php
			// Output the HTML changelog
			echo '<ul id="changelog">';
			if(!empty($grouped_commits)){
				foreach($grouped_commits as $date=>$commits){
					if(!empty($commits)){
						echo '<li><span class="date">'. date('j F Y', strtotime($date)) .'</span><ul class="commits">';
						foreach($commits as $commit){
							echo '<li><a href="'. $commit->html_url .'" target="_blank" class="message">'. $commit->commit->message .'</a>';
							if(isset($commit->author->html_url) && isset($commit->author->login)) echo ' <a href="'. $commit->author->html_url .'" target="_blank" class="author">'. $commit->author->login .'</a>';
							echo '</li>';
						}
						echo '</ul></li>';
					}
				}
			}
			echo '</ul>';

			// Output the pagination
			echo '<div class="pagination cf">';
			if($page > 1) echo '<a href="index.php?page='. ($page - 1) .'" class="prev">&larr; Previous</a> ';
			echo '<span class="current">Page '. $page .'</span> ';
			if(!empty($grouped_commits)) echo '<a href="index.php?page='. ($page + 1) .'" class="next">Next &rarr;</a>';
			echo '</div>';
			?>
		</section>

		<footer class="footer">
			Powered by <a href="http://github.com/gilbitron/changeloghub" target="_blank">Changeloghub</a>
		</footer>

	</div>

</body>
</html>
