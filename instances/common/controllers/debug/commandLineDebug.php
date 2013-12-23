<?php
namespace Common;


class DebugCommandLineDebugController extends \Common\DebugIndexController
{
	protected function renderDebugModule( $debug, $module_name, $template )
	{
		$view = new \Sifo\View();
		$view->assign( 'execution_key', $this->execution_key );
		$view->assign( 'debug', $debug );

		$template_route = ROOT_PATH . '/' . \Sifo\Config::getInstance()->getConfig( 'templates', $template );
		$this->debug_modules[$module_name] = $view->fetch( $template_route );
	}

	protected function finalRender( $debug )
	{
		$view = new \Sifo\View();
		$view->assign( 'debug_modules', $this->debug_modules );
		$view->assign( 'debug', $debug );
		$view->assign( 'command_line_mode', true );
		$view->assign( 'execution_key', $this->execution_key );

		$content = $view->fetch( ROOT_PATH . '/' . \Sifo\Config::getInstance()->getConfig( 'templates', 'debug/debug.tpl' ) );

		file_put_contents( ROOT_PATH . '/logs/cli_debug.html', $content );
	}
}

?>