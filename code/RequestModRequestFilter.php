<?php 

class RequestModRequestFilter {
	
	const MAX_DELAY_MS = 60000; // 1 minute
	const DEFAULT_DELAY_MS = 3000; // 3 seconds
	const DEFAULT_DELAY_MATCH_REGEX = '/^(?!(dev|admin|Security))(.*)/i'; // all but /admin and /dev
	
	const DEFAULT_RESPONSE_OVERRIDE_BODY = 'RequestModRequestFilter: Test Server Error Response';
	const DEFAULT_RESPONSE_OVERRIDE_STATUS_CODE = '500';
	const DEFAULT_RESPONSE_OVERRIDE_MATCH_REGEX = '/^(?!(dev|admin|Security))(.*)/i'; // all but /admin and /dev
	
	
	/**
	 * @var TestSessionEnvironment
	 */
	protected $testSessionEnvironment;
	
	public function __construct(){
		$this->testSessionEnvironment = Injector::inst()->get('TestSessionEnvironment');
	}
	
	public function preRequest($request, $session, $model){
		$state = $this->testSessionEnvironment->getState();
		if(!$state) return;
		
		// delay
		if(isset($state->responseDelayEnabled) && $state->responseDelayEnabled){
			if($this->urlMatchesDelayRegex($state, $request)){
				$delayMs = $this->getDelayMs($state);
				if($delayMs > 0){
					$this->delay($delayMs);
				}
			}
		}
	}
	
	public function postRequest($request, $response, $model){
		$state = $this->testSessionEnvironment->getState();
		if(!$state) return;
		
		// override
		if(isset($state->responseOverrideEnabled) && $state->responseOverrideEnabled){
			if($this->urlMatchesOverrideRegex($state, $request)){
				$this->overrideResponse($state, $response);
			}
		}
	}
	
	
	/**
	 * @return boolean true if request should be delayed
	 */
	protected function urlMatchesDelayRegex($state, $request){
		$regex = isset($state->responseDelayRegex) ? $state->responseDelayRegex : '';
		if(!$regex) $regex = self::DEFAULT_DELAY_MATCH_REGEX;
		return $this->urlMatchesRegex($regex, $request);
		
	}
	
	/**
	 * @return boolean true if request should be delayed
	 */
	protected function urlMatchesOverrideRegex($state, $request){
		$regex = isset($state->responseOverrideRegex) ? $state->responseOverrideRegex : '';
		if(!$regex) $regex = self::DEFAULT_RESPONSE_OVERRIDE_MATCH_REGEX;
		return $this->urlMatchesRegex($regex, $request);
	}
	
	/**
	 * @return boolean true if request URL matches a regex
	 */
	protected function urlMatchesRegex($regex, $request){
		$matchResult = preg_match($regex, $request->getURL());
		return $matchResult ? true : false;
	}
	
	/**
	 * @return int
	 */
	protected function getDelayMs($state){
		if(!isset($state->responseDelayMs)) return 0;
		$delayMs = 0;
		if(!$state->responseDelayMs){
			$delayMs = self::DEFAULT_DELAY_MS;
		}else{
			$delayMs = intval($state->responseDelayMs);
		}
		if($delayMs > self::MAX_DELAY_MS) $delayMs = self::MAX_DELAY_MS;
		if($delayMs < 0) $delayMs = 0;
		return $delayMs;
	}
	
	protected function delay($ms){
		// microseconds = milliseconds * 1000
		usleep($ms * 1000);
	}
	
	protected function overrideResponse($state, $response){
		$statusCode = isset($state->responseOverrideStatusCode) ? $state->responseOverrideStatusCode : '';
		$body = isset($state->responseOverrideBody) ? $state->responseOverrideBody : '';
		if(!$statusCode) $statusCode = self::DEFAULT_RESPONSE_OVERRIDE_STATUS_CODE;
		if(!$body) $body = self::DEFAULT_RESPONSE_OVERRIDE_BODY;
		$response->setBody($body);
		$response->setStatusCode($statusCode);
	}
	
}