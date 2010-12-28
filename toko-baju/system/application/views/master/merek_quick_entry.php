<h3>Tambahkan merek baru</h3>

    <form action="" method="post" id="form_merek" class="dialogbox-form">

        <table width="100%">

            <tr>
                <td style="text-align: right"><label>Nama merek:</label></td>
                <td><input type="text" name="new_nama" id="n" size="20" /></td>
            </tr>

            <tr>
                <td style="text-align: right"><label>Keterangan:</label></td>
                <td><input type="text" name="new_keterangan" id="ktr" size="30" /> (opsional)</td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <input type="submit" name="submit" id="submit" value="Tambahkan" class="button blue" />
                    <a href="index.php/master/merek" title="Buka manajemen merek" class="form_jump">Manajemen merek &raquo;</a>
                </td>
            </tr>


        </table>

    </form>

<script type="text/javascript">

$('#form_merek').submit(function(){

        if($('#n').val() == "") return false;

        var formData = $('#form_merek').serialize();
        $.ajax({
            url : 'index.php/master/merek/quick_entry',
            type : 'POST',
            data : formData,
            dataType : 'text',
            success : function(data){
                $.fn.colorbox.close();
                refreshMerek();
                // display message
                $('#x').removeClass('error_box').addClass('valid_box')
                    .html("Merek baru \"" + $('#n').val() + "\" telah ditambahkan.").slideDown('fast', function(){
                });
            },
            error : function(){
                alert("Terjadi kesalahan saat menyimpan data");
            }

        });

        return false;

});

</script>