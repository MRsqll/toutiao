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
//搜索页面资源
    public function search()
    {
         $total_num = null;
         $results = [];
         $keyword = '';
        if(isset($_GET['wd'])){
            $keyword = $_GET['wd'];
            $total_num = $this->pdo
                ->query('select count(*) as total_num from news where title like "%'.$keyword.'%"')
                ->fetch()['total_num'];

            $results = $this->pdo
                ->query('select * from news where title like "%' . $keyword . '%" limit '. $this::PER_PAGE.' offset 0')
                ->fetchAll();
        }
        include '../views/index/search.html';
    }
//搜索数据资源
    public function searchdata()
    {
        if(isset( $_GET['page']) && $_GET['wd'] ){
            $page = $_GET['page'];
            $wd = $_GET['wd'];
            $r = $this->pdo
                ->query('select * from news where title like "%' . $keyword . '%" limit '. $this::PER_PAGE .' offset '.($page - 1 )*$this::PER_PAGE)
                ->fetchAll();
            echo json_encode($r);
        }else{
            echo json_encode('参数错误');
        }
    }
//分类页面
    public function category()
    {
        $category = $this->pdo->query('select * from category where is_default = "1" ')->fetchAll();
        $categorys = $this->pdo->query('select * from category where is_default = "0" ')->fetchAll();
        include '../views/index/category.html';
    }
}