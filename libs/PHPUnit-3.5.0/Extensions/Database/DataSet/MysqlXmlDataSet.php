<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    DbUnit
 * @author     Matthew Turland <tobias382@gmail.com>
 * @copyright  2002-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */

/**
 * Data set implementation for the output of mysqldump --xml.
 *
 * @package    DbUnit
 * @author     Matthew Turland <tobias382@gmail.com>
 * @copyright  2010 Matthew Turland <tobias382@gmail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 1.0.1
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{
    protected function getTableInfo(array &$tableColumns, array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'mysqldump') {
            throw new Exception('The root element of a MySQL XML data set file must be called <mysqldump>');
        }

        foreach ($this->xmlFileContents->xpath('./database/table_data') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new Exception('<table_data> elements must include a name attribute');
            }

            $tableName = (string)$tableElement['name'];

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = array();
            }

            if (!isset($tableValues[$tableName])) {
                $tableValues[$tableName] = array();
            }

            foreach ($tableElement->xpath('./row') as $rowElement) {
                $rowValues = array();

                foreach ($rowElement->xpath('./field') as $columnElement) {
                    if (empty($columnElement['name'])) {
                        throw new Exception('<field> element name attributes cannot be empty');
                    }

                    $columnName = (string)$columnElement['name'];

                    if (!in_array($columnName, $tableColumns[$tableName])) {
                        $tableColumns[$tableName][] = $columnName;
                    }
                }

                foreach ($tableColumns[$tableName] as $columnName) {
                    $fields                 = $rowElement->xpath('./field[@name="' . $columnName . '"]');
                    $column                 = $fields[0];
                    $attr                   = $column->attributes('http://www.w3.org/2001/XMLSchema-instance');
                    $null                   = isset($column['nil']) || isset($attr[0]);
                    $columnValue            = $null ? NULL : (string)$column;
                    $rowValues[$columnName] = $columnValue;
                }

                $tableValues[$tableName][] = $rowValues;
            }
        }

        foreach ($this->xmlFileContents->xpath('./database/table_structure') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new Exception('<table_structure> elements must include a name attribute');
            }

            $tableName = (string) $tableElement['name'];

            foreach ($tableElement->xpath('./field') as $fieldElement) {
                if (empty($fieldElement['Field'])) {
                    throw new Exception('<field> elements must include a Field attribute');
                }

                $columnName = (string) $fieldElement['Field'];

                if (!in_array($columnName, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $columnName;
                }
            }
        }
    }

    public static function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset, $filename)
    {
        $pers = new PHPUnit_Extensions_Database_DataSet_Persistors_MysqlXml;
        $pers->setFileName($filename);

        try {
            $pers->write($dataset);
        }

        catch (RuntimeException $e) {
            throw new PHPUnit_Framework_Exception(__METHOD__ . ' called with an unwritable file.');
        }
    }
}
