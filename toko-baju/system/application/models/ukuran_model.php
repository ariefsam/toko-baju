<?php

class Ukuran_model extends Model {

    function Ukuran_model()
    {
        parent::Model();
    }

    function get_semua_ukuran($order = 'nama')
    {
        $data = array();
        $q = $this->db->query("SELECT * FROM ukuran ORDER BY $order");

        if($q->num_rows() > 0)
        {
            foreach ($q->result_array() as $row)
            {
                $data[] = $row;
            }
        }

        $q->free_result();
        return $data;
    }

    function get_ukuran($val, $col = 'id')
    {
        $data = "";
        $q = $this->db->query("SELECT * FROM ukuran WHERE $col = $val");
        
        if($q->num_rows() > 0)
        {
            $data = $q->row();
        }

        $q->free_result();
        return $data;
    }

    function insert_ukuran($data)
    {
        return $this->db->insert('ukuran', $data);
    }

    function update_ukuran($id_ukuran, $data)
    {
        $this->db->where('id', $id_ukuran);
        return $this->db->update('ukuran', $data);
    }

    function aman_dihapus($id_ukuran)
    {
        // syarat => belum pernah dibikin produknya
        //        => nggak ada di tabel "produk"

        $n = $this->db->query("SELECT * FROM produk WHERE ukuran = {$id_ukuran}")->num_rows();
        if ($n > 0)
            return false; // kalo > 0 berarti ada modelnya => gak aman dihapus
        else
            return true;
    }
}

