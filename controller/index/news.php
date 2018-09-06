<?php
include '../core/db.php';
class news extends db
{
    const PER_PAGE = 10;
    // 首页
    public function index()
    {
    //接受分类id
        if(isset($_GET['cid'])){
            $cid = $_GET['cid'];
        }else{
            $cid = 1;
        }
    //接收页数
        if(isset($_GET['page'])){
            $page = $_GET['page'];
        }else{
            $page = 1;
        }
    //某个分类下的总条数
        $num = $this->pdo
            ->query('select count(*) as total from news where cid ='.$cid)
            ->fetch()['total'];

    //总页数
        $page_total = ceil($num / $this::PER_PAGE);
    //分类
        $category = $this->pdo->query('select * from category where is_default = "1" ')->fetchAll();

    //新闻
        $news = $this->pdo
            ->query('select * from news where cid=' . $cid . ' limit ' . $this::PER_PAGE . ' offset ' . ($page - 1) * $this::PER_PAGE)
            ->fetchAll();

        include '../views/index/index.html';
    }

    public function search()
    {
        if(isset($_GET['k'])){
            $keyword = $_GET['k'];
        }else{
            $keyword = ' ';
        }
        if(isset($_GET['page'])){
            $page = $_GET['page'];
        }else{
            $page = 1;
        }
        $results = $this->pdo
            ->query('select * from news where title like "%' . $keyword . '%" limit '. $this::PER_PAGE .' offset '.($page -1) * $this::PER_PAGE)
            ->fetchAll();
        include '../views/index/search.html';
    }
//分类页面
    public function category()
    {
        $category = $this->pdo->query('select * from category where is_default = "1" ')->fetchAll();
        $categorys = $this->pdo->query('select * from category where is_default = "0" ')->fetchAll();
        include '../views/index/category.html';
    }
}