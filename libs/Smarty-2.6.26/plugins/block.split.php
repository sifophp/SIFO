<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/*
  Distributed under the terms of the BSD Licence:
  Copyright (c) 2007, Internet Brands, Inc (www.internetbrands.com)

  All rights reserved.

  Redistribution and use in source and binary forms, with or without 
  modification, are permitted provided that the following conditions are met:

      * Redistributions of source code must retain the above copyright notice, this list of 
        conditions and the following disclaimer.
      * Redistributions in binary form must reproduce the above copyright notice, 
        this list of conditions and the following disclaimer in the documentation and/or other 
        materials provided with the distribution.
      * Neither the name of the Internet Brands nor the names of its contributors may be used 
        to endorse or promote products derived from this software without specific prior 
        written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
  "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
  LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
  A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
  CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
  PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
  PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
  LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * Smarty {split}{/split} block plugin
 *
 * Type:     block function<br>
 * Name:     split<br>
 * Purpose:  loop structure that splits an array into chunks to allow for column formatting -- return chunks
 * in column format
 * @param array
 * <pre>
 * Params:   count: number of columns
 *           from: source array
 *           name: smarty param to assign chunks to
 * </pre>
 * @author Kevin Sours (kevin.sours@internetbrands.com)
 *         Internet Brands (http://ibbydev.blogspot.com/)
 * @param string contents of the block
 * @param Smarty 
 * @return string $content -- smarty content is not modified.
 */
function smarty_block_split($params, $content, &$smarty, &$repeat) {
  if (!array_key_exists('from', $params)) {
    $smarty->trigger_error("split: missing 'from' parameter", E_USER_WARNING);
    $repeat = false;
    return;
  }

  //if the source var isn't set, quietly treat it as empty.
  if(is_null($params['from'])) {
    $repeat = false;
    return;
  }

  if (!is_array($params['from'])) {
    $smarty->trigger_error("split: 'from' parameter must be an array", E_USER_WARNING);
    $repeat = false;
    return;
  }

  if (!isset($params['item'])) {
    $smarty->trigger_error("split: missing 'item' parameter", E_USER_WARNING);
    $repeat = false;
    return;
  }
  
  $count = 2;
  if (isset($params['count'])) {
    $count = $params['count'];
  }

  $name = $params['item'];

  $indexName = "smarty.IB.split.$name.index";
  $index = $smarty->get_template_vars($indexName);
  if(!$index) {
    $index = 0;
  }
  $lastName = "smarty.IB.split.$name.last";
  
  $source = $params["from"];
  if(is_null($content)) {
    $index++;
    $smarty->assign($indexName, $index);

    $amount = split_helper_getsize($source, $count, $index);
    split_helper_doslice(&$smarty, $name, $lastName, $source, $amount);
  }
  else {
    if($index == $count) {
      $smarty->clear_assign($indexName);   
      $smarty->clear_assign($lastName);   
      $smarty->clear_assign($name);   
    }
    else {
      $index++;
      $smarty->assign($indexName, $index);

      $amount = split_helper_getsize($source, $count, $index);
      split_helper_doslice(&$smarty, $name, $lastName, $source, $amount);
      $repeat = true;
    }

    return $content;
  }
}

function split_helper_getsize($source, $count, $index) {
  $amount = (int) (count($source)/$count);
  $extra = count($source) % $count;
  if($extra >= $index) {
    $amount++;
  }
  return $amount;
}

function split_helper_doslice(&$smarty, $name, $lastName, $source, $amount) {
  $last = $smarty->get_template_vars($lastName);
  if(!$last) {
    $last = 0;
  }

  $slice = array_slice($source, $last, $amount, false);
  $smarty->assign($name, $slice);
  $smarty->assign($lastName, ($last + $amount));
  return $slice;
}



?>