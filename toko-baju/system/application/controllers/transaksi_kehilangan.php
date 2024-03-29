<?php
class Transaksi_kehilangan extends Controller {

    function Transaksi_kehilangan()
    {
        parent::Controller();
        $this->load->model('Produk_model', 'produk');
        $this->load->model('Merek_model', 'merek');
        $this->load->model('Model_baju_model', 'model');
        $this->load->model('kehilangan_model', 'kehilangan');
    }

    function index()
    {
        if($this->session->userdata('transaksi')!='transaksi_kehilangan'){
            $this->cart->destroy();
            $this->session->set_userdata('transaksi', 'transaksi_kehilangan');
        }
        $info = "";
        if ($this->input->post('submit')) {
            if(!$this->input->post('id')) $info = "Input produk tidak lengkap";
//            else if(($this->input->post('jumlah'))){
//                $info = "Input jumlah salah";
//            }
            else{
            $add = $this->add($this->input->post('id'), $this->input->post('jumlah'));
            if (!$add) $info = "Stok Tidak mencukupi";
            }
        }
        $data = new stdClass();
        $data->info = $info;
        $data->view_konten = 'kehilangan';
        $data->title = "Kehilangan";
        $data->daftar_merek = $this->merek->get_semua_merek();
        $this->load->view('base', $data);
    }
    
    function bayar(){
        foreach($this->cart->contents() as $item){
            $this->kehilangan->insert_kehilangan($item['id'], $item['qty']);
        }
        $this->cart->destroy();
        redirect('transaksi_kehilangan');
    }

    function batal()
    {
        $this->cart->destroy();
        redirect('/transaksi_konsumen');
    }
    function stok($id)
    {
        $produk = $this->produk->get_produk_by_id($id);
        ?>
        <input type="text" id="isi_stok" value="<?php echo $produk->stok?>" disabled style="width: 40px; font-weight: bolder" />
        <?php
    }

    function add($produk, $jumlah)
    {
        $produk = $this->produk->get_produk_by_id($produk);
        if($produk->stok<$jumlah){ return false;}
        else {
        $data = array(
            'id'    => $produk->id,
            'qty'   => $jumlah,
            'price' => $produk->harga_jual,
            'name'  => $produk->model,
            'merek' => $produk->merek,
            'warna' => $produk->warna,
            'ukuran'=> $produk->ukuran
        );
        $this->cart->insert($data);
        return true;
        }
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
        $this->load->view('ajax_ukuran', $data);
    }

}