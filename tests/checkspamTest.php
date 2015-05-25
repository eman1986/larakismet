<?php
require_once __DIR__ . '/../vendor/autoload.php';

$config['akismet_api_key'] = '';
$config['akismet_blog_url'] = '';
$config['debug_mode'] = true;

$testContent = 'Enter in test content here';

$api = new \larakismet\Akismet($config);

$api->setCommentAuthor('John');
$api->setCommentAuthorEmail('john@example.com');
$api->setCommentContent($testContent);
$isSpam = $api->checkSpam() ? 'yes' :'no';

echo "Is this Spam? ".$isSpam."\n";