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
	public $description = 'The Highrise integration allows you to create and search for contacts';

	/**
	 * Any notes about this application
	 *
	 * @var string
	 */
	public $notes = '
		<p>Your Highrise api token can be found by logging into your account and then visiting Account &amp; Settings -> My Info -> Api Settings.</p>
		<p>The account is the subdomain section of your account. For example if your highrise account is "mycompany.highrisehq.com" then the account is "mycompany".</p>
	';

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
		array('name' => 'token', 'type' => 'text', 'help' => 'Enter your Highrise API Token'),
		array('name' => 'account', 'type' => 'text', 'help' => 'Enter your Highrise Account Name'),
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

		$payload = $this->lookupContact($contact['value']);

		return $this->render(__DIR__.'/highrise.html', compact('payload'));
	}

	/**
	 * Look-up a contact on Highrise.
	 *
	 * @param  string  $value
	 * @return array
	 */
	protected function lookupContact($value)
	{
		$request = $this->getClient()->get($root.'/people/search.json?criteria[email]='.$value);

		$request->setAuth($this->config['token'], 'x');

		$response = $request->send();

		return simplexml_load_string((string) $response->getBody());
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
		if (isset($contact['first_name']) and isset($contact['last_name']))
		{
			if (count($this->lookupContact($contact['value'])) == 0)
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