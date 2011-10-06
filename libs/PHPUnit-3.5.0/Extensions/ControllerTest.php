<?php
/**
 * ControllerTest.
 *
 * Create a proxy class for a TestCase.
 * It modifies the accessibility of protected methods and properties to public.
 *
 * Example of usage for methods:
 *
 *  $output = $this->obj->accessible_<methodName>( $args );
 *
 * Where <methodName> is the name of the protected method.
 *
 * The properties remains with the same name but public access.
 */
class PHPUnit_Extensions_ControllerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Create the proxy class of the given class name.
	 *
	 * @param object $classname Class name.
	 * @return object
	 */
	protected function getProxyClass( $classname )
	{
		$proxy_classname = $classname . 'ProxyTest';

		$proxy_class = <<<CLASS
class $proxy_classname extends $classname
{
	public function __call( \$name, Array \$arguments )
	{
		if ( preg_match( '/^accessible_(.*)/i', \$name, \$matches ) )
		{
			if ( isset( \$matches[1] ) )
			{
				\$foo = self::getMethod( \$matches[1] );
				\$obj = new $classname();
				return \$foo->invokeArgs( \$obj, \$arguments );
			}
		}

		trigger_error( 'Method $classname::' . \$name . ' does not exist', E_USER_ERROR );
	}

	protected static function getMethod( \$name )
	{
		\$class = new ReflectionClass( '$classname' );
		\$method = \$class->getMethod( \$name );
		\$method->setAccessible( true );
		return \$method;
	}
CLASS;

		$reflected = new ReflectionClass( $classname );
		$class_vars = $reflected->getProperties( ReflectionProperty::IS_PROTECTED );
		
		foreach ( $class_vars as $reflected_var )
		{
			$reflected_var->setAccessible( true );
			$var_name = $reflected_var->getName();

			$real_value = $reflected_var->getValue( new $classname );
			
			$is_static = '';
			if ( false !== $reflected_var->isStatic() )
			{
				$is_static = 'static ';
			}

			$proxy_class .= <<<CLASS
	{$is_static}public \${$var_name};
CLASS;
		}

		$proxy_class .= <<<CLASS
}
CLASS;


		if ( !class_exists( $proxy_classname ) )
		{
			eval( $proxy_class );
		}

		return new $proxy_classname;
	}
}

?>