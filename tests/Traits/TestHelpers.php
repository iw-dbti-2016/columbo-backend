<?php

namespace Tests\Traits;

trait TestHelpers
{
	private $uri = "/";

	protected function setRefererUri($uri)
	{
		$this->uri = $uri;
	}

	public function ref($uri=null)
	{
		return $this->followingRedirects()
					->withHeader('referer', 'http://127.0.0.1:8000' . ($uri ?: $this->uri));
	}
}
