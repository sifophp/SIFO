<?php

function smarty_function_pagination ($aParam, &$smarty)
{
	$sOut = null;
	if ( isset( $aParam['data']['template'] ) && !empty( $aParam['data']['template'] ) )
	{
		$sOut = $smarty->fetch( $aParam['data']['template'] );
	}
	
	return $sOut;
    
}//smarty_pagination
?>