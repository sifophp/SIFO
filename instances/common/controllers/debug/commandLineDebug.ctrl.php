<?php

namespace Common;

class DebugCommandLineDebugController extends \Common\DebugIndexController
{
	protected function getDebugUrl()
	{
		return implode( ' ', \Sifo\CLBootstrap::$command_line_params );
	}
}