<?php namespace Snappy\Apps\HighRise;

use Snappy\Apps\App as BaseApp;
use Guzzle\Http\Client;

class App extends BaseApp implements ContactLookupHandler, ContactCreatedHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'HighRise';

	/**
	 * The application description.
	 *
	 * @var string
	 */
	public $description = 'Application Description';

	/**
	 * The application's icon filename.
	 *
	 * @var string
	 */
	public $icon = 'highrise.png';

	/**
	 * The application author name.
	 *
	 * @var string
	 */
	public $author = 'UserScape';

	/**
	 * The application author e-mail.
	 *
	 * @var string
	 */
	public $email = 'it@userscape.com';

	/**
	 * The settings required by the application.
	 *
	 * @var array
	 */
	public $settings = array(
		array('name' => 'token', 'type' => 'text', 'help' => 'Enter your HighRise API Token'),
		array('name' => 'account', 'type' => 'text', 'help' => 'Enter your HighRise Account Name'),
	);

	/**
	 * Handle a contact look-up request.
	 *
	 * @param  array  $contact
	 * @return array
	 */
	public function handleContactLookup(array $contact)
	{
		$client = new Client('https://'.$this->config['account'].'.highrisehq.com');
		$request = $client->get('/people');
		$request->setAuth($this->config['token'], 'x');
		return $request->getUrl();
	}

}
