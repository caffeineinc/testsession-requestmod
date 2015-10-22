<?php 

class RequestModControllerExtension extends Extension {
	
	public function updateBaseFields($fields){
		// delay
		$fields->push(CheckboxField::create('responseDelayEnabled', 'Response delay enabled?'));
		$fields->push(
			TextField::create(
				'responseDelayMs', 
				'Response delay in ms (default '.RequestModRequestFilter::DEFAULT_DELAY_MS.', max: '.RequestModRequestFilter::MAX_DELAY_MS.')'
			)
		);
		$fields->push(TextField::create('responseDelayRegex', 'Response delay URL matching, as regex, default is: '.RequestModRequestFilter::DEFAULT_DELAY_MATCH_REGEX));
	
		// override
		$fields->push(CheckboxField::create('responseOverrideEnabled', 'Response override enabled?'));
		$fields->push(TextField::create('responseOverrideRegex', 'Response override URL matching, as regex, default is: '.RequestModRequestFilter::DEFAULT_RESPONSE_OVERRIDE_MATCH_REGEX));
		$fields->push(TextField::create('responseOverrideBody', 'Response override HTTP body, default is: "'.RequestModRequestFilter::DEFAULT_RESPONSE_OVERRIDE_BODY.'"'));
		$fields->push(TextField::create('responseOverrideStatusCode', 'Response override HTTP status code, default is: '.RequestModRequestFilter::DEFAULT_RESPONSE_OVERRIDE_STATUS_CODE));
	}
}