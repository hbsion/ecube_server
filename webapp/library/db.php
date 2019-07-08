<?php
/**
 *--------------------------------------------------------------------------
 * Database Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
Class db
{
    public $dbh;
    public $num_rows;

    /**
     * Database Construct.
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        if (!defined('DB_ENGINE') || !defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER'))
        {
            die('undefined db credential');
        }

        try
        {
            $this->dbh = new PDO(DB_ENGINE.':'.'dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * Database Query.
     *
     * @param $sql SQL Statement
     * @param $params SQL Parameter
     * @param $return PDO::FETCH_OBJ
     * @return fetchAll
     */
    public function query($sql, $params = NULL, $return = PDO::FETCH_OBJ)
    {
        try
        {
            $sth = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute($params);
            return $sth->fetchAll($return);
        }
        catch (PDOException $e)
        {
            echo $e->getMessage() . "-" . $sql;
            return false;
        }
    }

    /**
     * Database Record
     *
     * @param $sql SQL Statement
     * @param $params SQL Parameter
     * @param $return PDO::FETCH_OBJ
     * @return fetch
     */
    public function get_row($sql, $params = null, $return = PDO::FETCH_OBJ)
    {
        try
        {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
            $row = $sth->fetch($return);
            return $row;
        }
        catch (PDOException $e)
        {
            echo $e->getMessage() . "-" . $sql;
            return false;
        }
    }

    /**
     * Database Columns.
     *
     * @param $sql SQL Statement
     * @param $params SQL Parameter
     * @return fetch
     */
    function get_var($sql, $params = null)
    {
        try
        {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
            return $sth->fetchcolumn();
        }
        catch (PDOException $e)
        {
            echo $e->getMessage() . "-" . $sql;
            return false;
        }
    }

    /**
     * Database Insert.
     *
     * @param $table Database Table Name.
     * @param array $data
     * @return lastInsertId
     */
    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $param_fields = array();
        foreach ($fields as $field)
        {
            $param_fields[] = ":".addslashes($field);
        }
        $sql = "INSERT INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . implode( ",", $param_fields ) . ")";

        try
        {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($data);
            return $this->dbh->lastInsertId();
        }
        catch (PDOException $e)
        {
            $this->dbh->rollBack();
            echo $e->getMessage()."-".$sql;
            return false;
        }
    }

    /**
     * Database Update.
     *
     * @param $table Database Table Name.
     * @param $data array
     * @param $where SQL Where Statement
     * @return rowCount
     */
    public function update($table, $data, $where)
    {
        if (!is_array($where))
        {
            return false;
        }
        $bits = $wheres = array();
        foreach ((array) array_keys($data) as $field)
        {
            $form = ":".addslashes($field);
            $bits[] = "`$field` = {$form}";
        }
        foreach ((array) array_keys($where) as $field)
        {
            $form = ":".addslashes($field);
            $wheres[] = "`$field` = {$form}";
        }
        $sql = "UPDATE `$table` SET " . implode(', ', $bits) . ' WHERE ' . implode(' AND ', $wheres);

        try
        {
            $sth = $this->dbh->prepare($sql);
            return $sth->execute($data);
        }
        catch (PDOException $e)
        {
            $this->dbh->rollBack();
            echo $e->getMessage()."-".$sql;
            throw $e;
        }
    }

    /**
     * Database Start Transaction.
     *
     * @param void
     * @return void
     */
    public function start_transact()
    {
        $this->dbh->beginTransaction();
    }

    /**
     * Database End Transaction.
     *
     * @param void
     * @return void
     */
    public function end_transact()
    {
        $this->dbh->commit();
    }

    /**
     * Database Data RollBack
     *
     * @param void
     * @return void
     */
    public function rollback_transact()
    {
        $this->dbh->rollBack();
    }
}
