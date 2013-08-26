<?php

/**
 *	Passes control over to the APIwesomeService on valid request URL.
 *	@author Nathan Glasl <nathan@silverstripe.com.au>
 */

class APIwesome extends Controller {

	public static $allowed_actions = array(
		'retrieve'
	);

	public static $dependencies = array(
		'service' => '%$APIwesomeService',
	);

	public $service;

	/**
	 *	Hitting the controller directly is an invalid URL.
	 */

	public function index() {

		return $this->httpError(404);
	}

	/**
	 *
	 *	Retrieve the appropriate JSON/XML of the specified data object either by function call or request URL, including any user customisation.
	 *
	 *	@param string
	 *	@param string
	 *
	 *	EXAMPLE JSON:	{WEBSITE}/apiwesome/retrieve/my-first-data-object-name/json
	 *	EXAMPLE XML:	{WEBSITE}/apiwesome/retrieve/my-second-data-object-name/xml
	 *
	 */

	public function retrieve() {

		return $this->service->retrieve();
	}

	/**
	 *	Return the appropriate error page on invalid URL.
	 *
	 *	@param integer
	 *	@return string
	 */

	public function httpError($code, $message = null) {

		// Retrieve the error page for the given status code.

		if($this->getRequest()->isMedia() || !$response = ErrorPage::response_for($code)) {
			return parent::httpError($code, $message);
		}
		else {
			throw new SS_HTTPResponse_Exception($response);
		}
	}

}