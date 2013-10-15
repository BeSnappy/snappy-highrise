<?php
	// Config settings
	$highrise_token = '';
	$highrise_account = '';
	$contact_email = 'test@example.com';

	// Include the needed files
	require 'vendor/autoload.php';
	include 'src/Snappy/Apps/HighRise/App.php';

	// Setup the app call
	$app = new Snappy\Apps\Highrise\App();
	$app->setConfig(array(
		'token' => $highrise_token,
		'account' => $highrise_account,
	));

	// Display it to the browser.
	echo $app->handleContactLookup(
		array('value' => $contact_email)
	);

