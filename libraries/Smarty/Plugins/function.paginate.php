<?php

function smarty_function_paginate ($aParam, &$smarty)
{
	$sOut = null;
	if ( isset( $aParam['data']['template'] ) && !empty( $aParam['data']['template'] ) )
	{
		$sOut = $smarty->fetch( $aParam['data']['template'] );
	}
	
	return $sOut;
    
}//smarty_paginate
?>