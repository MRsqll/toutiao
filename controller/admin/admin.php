<?php
include '../core/db.php';
class admin extends db
{
    const PER_PAGE = 10;
    public function index()
    {

        include '../views/admin/admin.html';
    }

    public function news()
    {
        //接受分类cid
        if(isset($_GET['cid'])){
            $cid = $_GET['cid'];
        }else{
            $cid = 1;
        }
        //接受页数
        if($_GET['page']){
            $page = $_GET['page'];
        }else{
            $page = 1;
        }
        //某个分类下的总条数， $this->pdo->query('select count(*) as toatl from news ')->fetch()这个表达式返回的是一个数组[total:60],

        $num = $this->pdo->query('select count(*) as total from news where cid = '.$cid)->fetch()['total'];

        //总页数
        $page_total = ceil($num / $this::PER_PAGE) ;

        //查找数据
        $news = $this->pdo
            ->query('select * from news where cid ='.$cid.' limit '.$this::PER_PAGE.' offset '.($page - 1)*$this::PER_PAGE)
            ->fetchAll();
        include '../views/admin/news.html';
    }
    public function category()
    {
        //某个分类下的总条数， $this->pdo->query('select count(*) as toatl from news ')->fetch()这个表达式返回的是一个数组[total:60],

        $num = $this->pdo->query('select count(*) as total from category ')->fetch()['total'];

        $category = $this->pdo->query('select * from category where is_default = "1" ')->fetchAll();
        include '../views/admin/category.html';
    }
    public function delete (){

        $count = $this->pdo->exec("delete from news where id=" . $_GET['id']);
        echo $count;

    }
    public function update (){
        $count = $this->pdo->prepare('update news set '.$_GET['k'].'= ? where id= ?');
        $count->bindValue(1, $_GET['v']);
        $count->bindValue(2, $_GET['id']);
        echo $count->execute();

    }
    public function insert (){
        $count = $this->pdo->prepare("insert into news (cid,title,dsc,image_urls,url,pubtime,content) values (?,?,?,?,?,?,?)");
        $count->bindValue(1, '1');
        $count->bindValue(2, '');
        $count->bindValue(3, '');
        $count->bindValue(4, '');
        $count->bindValue(5, '');
        $count->bindValue(6, '');
        $count->bindValue(7, '');
        echo $count->execute();
    }
}

