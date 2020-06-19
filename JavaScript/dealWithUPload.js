$('#formUP').submit(function (e) {

    $.post("../src/add.php",{
            name:"菜鸟教程",
            url:"http://www.runoob.com"
        },
        function(data,status){
            alert("数据: \n" + data + "\n状态: " + status);
        });

    e.preventDefault();
    let data=new FormData(this);//获取非文本类的数据

    $.ajax({
        url:"../src/add.php",//处理页面的路径
        data:data,//通过json格式将一组数据传过去
        type:"post",//数据的提交和传递方式，最好用POST
        dataType:"json",//页面返回值的类型，共有三种：TEXT,JSON,XML可选
        cache:false,
        contentType:false,
        processData:false,
        success:function(res){//回调函数：如果ajax调用成功，就执行这个success后面的函数，(data)当做参数返回过来
            if (res.flag===1){
                alert('上传成功！');
            } else if(res.flag===2){
                alert('网络或其他未知错误，请重试！')
            }else{
                alert('图片格式错误，请重试！')
            }
        },
        error: function () {
            alert("error")
        }
    });
});