<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Base_Model extends CI_Model
{
    public $_table;


    public function __construct()
    {
        $this->load->database();
    }


    // Sanitizes string for SQL injection.
    // returns string
    protected function _sanitize_string($value)
    {
        if (is_object($this->db->conn_id))
        {
            return $this->db->conn_id->real_escape_string($value);
        }
        else
        {
            // Note: only available in non-database
            // environments for certain types of CI compatibility
            // addslashes is not used when database is present
            return addslashes($value);
        }
    }


    public function doesValueExist($field_name, $field_value)
    {

        $sql = "SELECT id FROM " . $this->_table . " where " . $this->_sanitize_string($field_name) . " = ?  ";
        $results = $this->db->query($sql, array($field_value));
        if ($results->num_rows > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


    public function doesUserOwnChildRow($user_id, $table_name, $asset_type_pk_field, $asset_pk_id, $user_fk_field_name)
    {

        $sql = "SELECT $asset_type_pk_field as pk from " . $this->_sanitize_string($table_name) . " where " . $this->_sanitize_string($asset_type_pk_field) . " = ? and " . $this->_sanitize_string($user_fk_field_name) . " = ? ";

        $results = $this->db->query($sql, array($asset_pk_id,$user_id));
        if ($results->num_rows > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


    public function getLastInsertId()
    {
        return $this->db->insert_id();
    }


    public function deleteById($id)
    {

        $this->db->delete($this->_table, array('id' => $id));
    }


    public function deleteByFK($search_array)
    {

        $this->db->delete($this->_table, $search_array);
    }


    public function getCALCFOUNDROWS()
    {
        $count = $this->db->query('SELECT FOUND_ROWS() as rowcount');
        $count = $count->result();

        if (is_array($count))
        {
            $total_rows = $count[0]->rowcount;
        }
        else
        {
            $total_rows = 0;
        }
        return $total_rows;
    }


    public function getAll($limit = 20, $offset = 0, $sort_by = "date_created", $sort_order = "desc")
    {


        // $this->db->SQL_CALC_FOUND_ROWS();
        $this->db->order_by($sort_by, $sort_order);
        $this->db->limit($limit, $offset);

        $i = 0;
        $query = $this->db->get($this->_table);

        $data = array();
        // $total_num_rows = $this->getCALCFOUNDROWS();
        $total_num_rows = 0;
        $data['meta']['total_rows'] = $total_num_rows;

        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $data['data'][$i] = $row;
                $i ++;
            }
            return $data;
        }
        return FALSE;
    }


    public function total_record_count()
    {
        return $this->db->count_all($this->_table);
    }

    /**
     * Return all rows with an "in" clause.
     *
     * @author Created By : Nilesh
     * @version 1.0
     * @name getByWhereIn
     * @access public
     * @param field_name
     *  the column to use as the lookup field
     * @param field_values_array
     *  array containing values
     * @param fields_to_get_csv
     *  list of fields to return in query
     *
     *
     */
    //seems to be a duplicate function
    public function getByWhereIn($field_name, $field_values_array, $fields_to_get_csv = "*", $limit = 0, $start = 0, $sort_by = "", $sort_order = "")
    {
        // fail on empty values_array
        $data = array();
        $data['data'] = array();
        if (count($field_values_array) == 0)
        {
            return $data;
        }

        $this->db->select($fields_to_get_csv);
        // $this->db->SQL_CALC_FOUND_ROWS();

        $this->db->where_in($field_name, $field_values_array);
        if (intval($limit) > 0) {
            $this->db->limit($limit, $start);
        }
        if ($sort_by != '' || $sort_order != '')
        {
            $this->db->order_by($sort_by, $sort_order);
        }


        $query = $this->db->get($this->_table);
        $i = 0;
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $data['data'][$i] = $row;
                $i ++;
            }
            return $data;
        }
    }


    public function getByFK($array_key_value, $fields_to_get_csv = "*", $limit = 200, $start = 0, $sort_by = "date_created", $sort_order = "desc")
    {



        $this->db->select($fields_to_get_csv);
        // $this->db->SQL_CALC_FOUND_ROWS();
        // ernst added 2013-12-17 to allow unlimited requests
        if (intval( $limit) > 0) {
            $this->db->limit($limit, $start);
        }
        $this->db->order_by($sort_by, $sort_order);
        $query = $this->db->get_where($this->_table, $array_key_value);
        if (! $query)
        {
            return FALSE;
        }
        $data = array();
        // $total_num_rows = $this->getCALCFOUNDROWS();
        $total_num_rows = 0;
        $data['meta']['total_rows'] = $total_num_rows;
        $data['meta']['limit'] = $limit;
        $data['meta']['start'] = $start;
        $data['meta']['sort_by'] = $sort_by;
        $data['meta']['sort_order'] = $sort_order;

        $i = 0;
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $data['data'][$i] = $row;
                $i ++;
            }
        }
        else
        {
            $data['data'] = array();
        }

        return $data;
    }

    public function get_by_kvp($key_value, $key_name, $fields_csv = "*", $additional_kvp = array())
    {

        // Check for the mandatory field
        if (empty($key_value))
        {
            return;
        }

        // Set the query
        $query = "SELECT " . $this->_sanitize_string($fields_csv) . " FROM " . $this->_table . " WHERE " .  $this->_sanitize_string($key_name) . " = ? ";
        $bindings = array();
        $bindings[] = $key_value;

        if (count($additional_kvp) > 0)
        {
            foreach ($additional_kvp as $key => $value)
            {
                $query .= " AND " . $this->_sanitize_string($key) . " = ? ";

                $bindings[] = $value;
            }
        }

        // Run the query
        $result = $this->db->query($query, $bindings);

        // Set the default status
        $response = array('success' => FALSE);

        // Get the result
        if ($result->num_rows > 0)
        {

            $row = $result->row_array();
            $response['success'] = TRUE;
            $response['data'] = $row;
        }

        // Return the result
        return $response;
    }


    public function getUniqueByFK($fk_field, $fk_value)
    {


        $sql = "SELECT * FROM " . $this->_table . " where " . $this->_sanitize_string($fk_field) . " = ?  ";
        $results = $this->db->query($sql, array($fk_value));

        if ($results->num_rows() > 0)
        {
            return $results->row();
        }
        else
        {
            return array();
        }
    }





    public function getDistinctFieldValues($field_name, $fk_field = "", $fk_value = "")
    {

        $sql = "SELECT distinct " . $this->_sanitize_string($field_name) . " FROM " . $this->_table;

        $fields_bind_array = array();

        if (($fk_field != "") &&($fk_value != ""))
        {
            $sql .= " where " . $this->_sanitize_string($fk_field) . " = ?  ";
            $fields_bind_array[] = $fk_value;
        }

        $results = $this->db->query($sql, $fields_bind_array);

        $data = array();
        $i = 0;
        if ($results->num_rows() > 0)
        {
            foreach ($results->result() as $row)
            {
                $data[] = $row->$field_name;
                $i ++;
            }
        }

        return $data;
    }


    public function changeNullsToEmptyInArray($array)
    {
        foreach ($array as $key => $value)
        {
            if ($value === NULL)
            {
                $array[$key] = "";
            }
        }

        return $array;
    }


    public function getById($id, $fields_csv = "*")
    {
        // Sanitizing for SQL Injections
        $fields_csv = $this->_sanitize_string($fields_csv);

        $sql = "SELECT $fields_csv FROM " . $this->_table . " where id = ?";

        $results = $this->db->query($sql, array($id));

        if ($results->num_rows() > 0)
        {
            $data_row = $results->row();

            return $data_row;
        }
        else
        {
            return FALSE;
        }
    }


    public function getByUserId($user_id, $limit = 20, $start = 0)
    {
        return $this->getByFK(array('user_id' => $user_id), "*", $limit, $start, "date_created", "desc");
    }


    public function add($data)
    {

        $data['ip_address'] = Utils::getClientIpAddress();

        if (! array_key_exists('ip_address', $data))
        {
            $data['ip_address'] = Utils::getClientIpAddress();
        }

        if (! array_key_exists('date_created', $data))
        {
            $data['date_created'] = Utils::getDateTime();
        }
        return $this->db->insert($this->_table, $data);
    }


    public function insert_batch($data)
    {

        $this->db->insert_batch($this->_table, $data);
    }


    public function replace_batch($data)
    {
        $this->db->replace_batch($this->_table, $data);
    }


    public function updateById($key_value_data, $id)
    {

        $this->db->where('id', $id);

        $update_results = $this->db->update($this->_table, $key_value_data);

        if (! $update_results)
        {
            $debug_log = array("key_values" => $key_value_data,"table" => $this->_table,"id" => $id);
            throw new EtsyException("Sorry, Failed to update Data");
        }
        else
        {
            return $update_results;
        }
    }


    public function updateByFK($key_value_data, $FK_key_value_array)
    {

        $this->db->where($FK_key_value_array);
        return $this->db->update($this->_table, $key_value_data);
    }


    public function simple_get_all()
    {
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    public function simple_get_all_object()
    {
        $query = $this->db->get($this->_table);
        return $query->result();
    }






}