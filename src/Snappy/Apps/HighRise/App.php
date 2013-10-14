<?php namespace Snappy\Apps\Highrise;

use Httpful\Request;
use Snappy\Apps\App as BaseApp;
use Snappy\Apps\ContactLookupHandler;
use Snappy\Apps\ContactCreatedHandler;

class App extends BaseApp implements ContactLookupHandler, ContactCreatedHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'Highrise';

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
		$root = 'https://'.$this->config['account'].'.highrisehq.com';

		$response = Request::get($root.'/people/search.json?criteria[email]='.$contact['value'])
												->authenticateWith($this->config['token'], 'x')
												->expectsXml()
												->send();

		$payload = simplexml_load_string($response->raw_body);

		return $this->render(__DIR__.'/highrise.html', compact('payload'));
	}

	/**
	 * Handle the creation of a new contact.
	 *
	 * @param  array  $ticket
	 * @param  array  $contact
	 * @return void
	 */
	public function handleContactCreated(array $ticket, array $contact)
	{
		//
	}

}