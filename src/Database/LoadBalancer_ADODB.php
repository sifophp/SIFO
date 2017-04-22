<?php

namespace Sifo\Database;

// Some stuff needed by ADODb:
$ADODB_CACHE_DIR = ROOT_PATH . '/cache';

class LoadBalancer_ADODB extends LoadBalancer
{
    protected function addNodeIfAvailable($index, $node_properties)
    {
        try
        {
            $db     = \NewADOConnection($node_properties['db_driver']);
            $result = $db->Connect($node_properties['db_host'], $node_properties['db_user'], $node_properties['db_password'], $node_properties['db_name']);

            // If no exception at this point the server is ready:
            $this->addServer($index, $node_properties['weight']);
        }
        catch (\ADODB_Exception $e)
        {
            // The server is down, won't be added in the balancing. Log it:
            trigger_error("SERVER IS DOWN! " . $node_properties['db_host'], E_ERROR);
        }
    }
}

