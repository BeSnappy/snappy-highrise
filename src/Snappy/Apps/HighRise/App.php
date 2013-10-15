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
	 * Any notes about this application
	 *
	 * @var string
	 */
	public $notes = '';

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
		$root = $this->getRootUrl();

		$request = $this->getClient()->get($root.'/people/search.json?criteria[email]='.$contact['value']);

		$request->setAuth($this->config['token'], 'x');

		$response = $request->send();

		$payload = simplexml_load_string((string) $response->getBody());

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
		return;

		if (isset($contact['first_name']) and isset($contact['last_name']))
		{
			$body = $this->render(__DIR__.'/contact.xml', compact('contact'));

			$request = $this->getClient()->post($this->getRootUrl().'/people.xml', array(), $body);

			try
			{
				$request->send();
			}
			catch (\Exception $e)
			{
				//
			}
		}
	}

	/**
	 * Get the root URL of the Highrise service.
	 *
	 * @return string
	 */
	public function getRootUrl()
	{
		return 'https://'.$this->config['account'].'.highrisehq.com';
	}

	/**
	 * Get a new Guzzle HTTP client.
	 *
	 * @return \Guzzle\Http\Client
	 */
	public function getClient()
	{
		return new \Guzzle\Http\Client;
	}

}