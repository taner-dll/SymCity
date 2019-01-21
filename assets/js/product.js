require('../css/product.scss');
require('datatables.net-bs');

$(function () {
    $('#example1').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
        "language": {
            "lengthMenu": "Sayfa başı _MENU_ kayıt göster",
            "zeroRecords": "Kayıt bulunamadı",
            "info": "_TOTAL_ kayıttan _START_ ile _END_ arası gösteriliyor",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search":         "Ara:",
            "paginate": {
                "first":      "İlk",
                "last":       "Son",
                "next":       "Sonraki",
                "previous":   "Önceki"
            }
        }

    });
})


