<?php
namespace geoApiLib\locators\IpGeoLocationLocator;

class IpGeoLocationLocator implements Locator {
	private $client;
	private $apiKey;

	public function __construct(HttpClient $client, string $apiKey) {
		$this->client = $client;
		$this->apiKey = $apiKey;
	}

	public function locate(Ip $ip): ?Location {

		$url = 'https://api.ipgeolocation.io/ipgeo?' .http_build_query([
			'apiKey' => $this->apiKey,
			'ip' => $ip->getValue(),
		]);

		$response = $this->client->get($url);
		$data = json_decode($response, true);
		$data = array_map(fn($value)=> $value !== '-' ? $value : null, $data);

		if(empty($data['country_name'])) {
			return null;
		}

		return new Location(
			$data['country_name'],
			$data['state_prov'],
			$data['city']
		);
	}
}
