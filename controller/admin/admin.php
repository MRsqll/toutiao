<?php
include '../core/db.php';
class admin extends db
{
    public function index()
    {
        include '../views/admin/admin.html';
    }

    public function news()
    {
        include '../views/admin/news.html';
    }

    public function category()
    {
        include '../views/admin/category.html';
    }
}
