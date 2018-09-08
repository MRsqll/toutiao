$(function () {
    //修改数据
    let add = $('#add');
    let tbody = $('#tbody');
    tbody.on('blur','.form-control',function () {
        let id = $(this).closest('tr').attr('data-id');
        let k = $(this).attr('data-type');
        let v = $(this).val();
        $.ajax({
            url:'/admin.php?c=admin&m=update',
            data:{
              id:id,
              k:k,
              v:v,  
            },
        })
    })
    //删除数据
    tbody.on('click','.remove',function () {
        let id = $(this).closest('tr').attr('data-id');
        $.ajax({
            url:'/admin.php?c=admin&m=delete&id=' + id,
            success:function (data) {
                if (data==1){
                    location.reload();//刷新页面
                }else {
                    alert('网络延迟');
                }
            }
        })
    })
    //插入数据
    add.on('click',function () {
        $.ajax({
            url:'/admin.php?c=admin&m=insert',
            success:function (data) {
                if(data==1){
                    location.reload();
                }else {
                    alert('网络延迟');
                }
            }
        })
    })
})