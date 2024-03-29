<?php
class Retur_agen extends Controller {

    function Retur_agen()
    {
        parent::Controller();
        $this->load->model('Produk_model', 'produk');
        $this->load->model('Merek_model', 'merek');
        $this->load->model('Model_baju_model', 'model');
        $this->load->model('Retur_model', 'retur');
        $this->load->model('Agen_model', 'agen');
    }

    function index()
    {
        if($this->session->userdata('transaksi')!='retur_agen'){
            $this->cart->destroy();
            $this->session->set_userdata('transaksi', 'retur_agen');
            $this->session->unset_userdata('id_agen');
        }
        $info = "";
        if(!$this->session->userdata('id_agen')) redirect('/retur_agen/tentukan_agen');
        
        if ($this->input->post('submit')) {
            if(!$this->input->post('id')) $info = "Input produk tidak lengkap";
            else{
                $add = $this->add($this->input->post('id'), $this->input->post('jumlah'), $this->input->post('harga'));
                if (!$add) $info = "Gagal";
            }
        }
        $data = new stdClass();
        $id_agen = $this->session->userdata('id_agen');
        $data->nama_agen = "";
        $data->nama_agen .= $this->agen->get_agen($id_agen)->nama;
        $data->daftar_agen = $this->agen->get_semua_agen();
        $data->info = $info;
        $data->view_konten = 'retur_agen';
        $data->title = "Retur Agen";
        $data->daftar_merek = $this->merek->get_semua_merek();
        $this->load->view('base', $data);
    }
    function pilih_agen($id)
    {
        $agen = $this->agen->get_agen($id);
        if ($agen) $this->session->set_userdata('id_agen', $id);
        redirect('/retur_agen');
    }

    function tentukan_agen()
    {
        $data = new stdClass();
        $data->view_konten = 'tentukan_agen_retur';
        $data->title = "Retur Agen";
        $data->nama_agen = "";
        $data->nama_agen .= $this->session->userdata('id_agen');
        $data->daftar_agen = $this->agen->get_semua_agen();
        $data->daftar_merek = $this->merek->get_semua_merek();
        $this->load->view('base', $data);
    }

    function refund(){
        $tanggal = date("Y-m-d H:i:s");
        $this->load->model('Order_model', 'order');

        // ====================Order====================
        $data = array(
            "tanggal"   => $tanggal,
            "total"     => $this->cart->total(),
            "jenis"     => "retur_agen",
            "lunas"     => $this->session->userdata('pembayaran')
        );
        $id_order = $this->order->insert_order($data);

        //======================Retur=======================
        foreach($this->cart->contents() as $item){
            $this->retur->retur_agen($item['id'], $item['qty'], $item['price'], $this->session->userdata('id_agen'), $id_order);
        }
        //$this->cart->destroy();
        //$this->session->unset_userdata('id_agen');

        $this->session->set_userdata('print_order_id', $id_order);

        redirect('/retur_agen/print_confirm');
    }
    function batal(){
        $this->cart->destroy();
        $this->session->unset_userdata('id_agen');
        redirect('/retur_agen');
    }
    function add($produk, $jumlah, $harga)
    {
        $produk = $this->produk->get_produk_by_id($produk);
        $data = array(
            'id'    => $produk->id,
            'qty'   => $jumlah,
            'price' => $harga,
            'name'  => $produk->model,
            'merek' => $produk->merek,
            'warna' => $produk->warna,
            'ukuran'=> $produk->ukuran
        );
        return $this->cart->insert($data);        
    }

    function model($merek)
    {
        $x = $this->model->get_semua_model_by_merek($merek);
        $data = new stdClass();
        $data->daftar_model = $x;
        $this->load->view('ajax_model', $data);
    }

    function warna($model)
    {
        $x = $this->produk->get_semua_warna_by_model($model);
        $data = new stdClass();
        $data->daftar_warna = $x;
        $data->model_baju = $model;
        $this->load->view('ajax_warna', $data);
    }

    function ukuran($model, $warna)
    {
        $x = $this->produk->get_semua_ukuran($model, $warna);
        $data = new stdClass();
        $data->daftar_ukuran = $x;
        $data->model = $model;
        $data->warna = $warna;
        $this->load->view('ajax_ukuran_2', $data);
    }

    function harga($id_produk)
    {
        $harga_jual = $this->produk->get_produk_by_id($id_produk)->harga_jual;
        $diskon = $this->agen->get_agen($this->session->userdata('id_agen'))->diskon;
        
        echo round($harga_jual * (100 - $diskon) / 100);
        exit;
    }

    function print_confirm()
    {
        $data = new stdClass();

        $data->view_konten = 'print_transaksi_agen_confirm';
        $data->title = "Retur Agen &raquo; Print";
        $data->return_page = "retur_agen";
        $data->jenis = "RETUR";

        // transfer dulu datanya
        $data->nomer_transaksi = $this->session->userdata('print_order_id');
        $data->isi_transaksi = $this->cart->contents();
        $data->agen = $this->agen->get_agen($this->session->userdata('id_agen'));

        // baru dihancurin
        $this->cart->destroy();
        $this->session->unset_userdata('print_order_id');
        $this->session->unset_userdata('id_agen');

        $data->is_print = true;

        $this->load->view('base', $data);
    }


}